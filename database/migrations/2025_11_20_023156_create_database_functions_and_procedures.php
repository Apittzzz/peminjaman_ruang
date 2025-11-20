<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ========================================
        // FUNCTIONS
        // ========================================
        
        // Function 1: Count Peminjaman by User
        DB::unprepared("
            DROP FUNCTION IF EXISTS count_peminjaman_by_user;
            
            CREATE FUNCTION count_peminjaman_by_user(user_id INT)
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE total INT;
                SELECT COUNT(*) INTO total
                FROM peminjaman
                WHERE id_user = user_id;
                RETURN total;
            END;
        ");
        
        // Function 2: Count Peminjaman by Status
        DB::unprepared("
            DROP FUNCTION IF EXISTS count_peminjaman_by_status;
            
            CREATE FUNCTION count_peminjaman_by_status(p_status VARCHAR(50))
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE total INT;
                SELECT COUNT(*) INTO total
                FROM peminjaman
                WHERE status = p_status;
                RETURN total;
            END;
        ");
        
        // Function 3: Check Room Availability
        DB::unprepared("
            DROP FUNCTION IF EXISTS check_room_availability;
            
            CREATE FUNCTION check_room_availability(
                p_ruang_id INT,
                p_tanggal DATE,
                p_waktu_mulai TIME,
                p_waktu_selesai TIME
            )
            RETURNS VARCHAR(20)
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE is_available VARCHAR(20);
                DECLARE conflict_count INT;
                
                SELECT COUNT(*) INTO conflict_count
                FROM peminjaman
                WHERE id_ruang = p_ruang_id
                  AND tanggal_pinjam = p_tanggal
                  AND status IN ('pending', 'approved')
                  AND (
                      (p_waktu_mulai BETWEEN waktu_mulai AND waktu_selesai)
                      OR (p_waktu_selesai BETWEEN waktu_mulai AND waktu_selesai)
                      OR (waktu_mulai BETWEEN p_waktu_mulai AND p_waktu_selesai)
                  );
                
                IF conflict_count > 0 THEN
                    SET is_available = 'TIDAK TERSEDIA';
                ELSE
                    SET is_available = 'TERSEDIA';
                END IF;
                
                RETURN is_available;
            END;
        ");
        
        // ========================================
        // STORED PROCEDURES
        // ========================================
        
        // Procedure 1: Update Room Status After Approval
        DB::unprepared("
            DROP PROCEDURE IF EXISTS update_room_status_after_approval;
            
            CREATE PROCEDURE update_room_status_after_approval(
                IN p_peminjaman_id INT
            )
            BEGIN
                DECLARE v_ruang_id INT;
                DECLARE v_status VARCHAR(50);
                
                SELECT id_ruang, status INTO v_ruang_id, v_status
                FROM peminjaman 
                WHERE id_peminjaman = p_peminjaman_id;
                
                IF v_status = 'approved' THEN
                    UPDATE ruang 
                    SET status = 'dipakai',
                        updated_at = NOW()
                    WHERE id_ruang = v_ruang_id;
                    
                ELSEIF v_status IN ('rejected', 'selesai', 'cancelled') THEN
                    IF NOT EXISTS (
                        SELECT 1 FROM peminjaman 
                        WHERE id_ruang = v_ruang_id 
                        AND status = 'approved' 
                        AND id_peminjaman != p_peminjaman_id
                    ) THEN
                        UPDATE ruang 
                        SET status = 'kosong',
                            updated_at = NOW()
                        WHERE id_ruang = v_ruang_id;
                    END IF;
                END IF;
            END;
        ");
        
        // Procedure 2: Approve Peminjaman with Transaction
        DB::unprepared("
            DROP PROCEDURE IF EXISTS approve_peminjaman;
            
            CREATE PROCEDURE approve_peminjaman(
                IN p_peminjaman_id INT,
                IN p_catatan TEXT,
                IN p_approved_by INT
            )
            BEGIN
                DECLARE v_user_id INT;
                DECLARE v_ruang_id INT;
                DECLARE v_nama_ruang VARCHAR(100);
                DECLARE v_error VARCHAR(255);
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    GET DIAGNOSTICS CONDITION 1 v_error = MESSAGE_TEXT;
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error;
                END;
                
                START TRANSACTION;
                
                SELECT p.id_user, p.id_ruang, r.nama_ruang
                INTO v_user_id, v_ruang_id, v_nama_ruang
                FROM peminjaman p
                JOIN ruang r ON p.id_ruang = r.id_ruang
                WHERE p.id_peminjaman = p_peminjaman_id;
                
                UPDATE peminjaman
                SET status = 'approved',
                    catatan = p_catatan,
                    approved_at = NOW(),
                    updated_at = NOW()
                WHERE id_peminjaman = p_peminjaman_id;
                
                UPDATE ruang
                SET status = 'dipakai',
                    updated_at = NOW()
                WHERE id_ruang = v_ruang_id;
                
                INSERT INTO notifications (user_id, title, message, type, created_at, updated_at)
                VALUES (
                    v_user_id,
                    'Peminjaman Disetujui',
                    CONCAT('Peminjaman ruangan ', v_nama_ruang, ' telah disetujui'),
                    'approval',
                    NOW(),
                    NOW()
                );
                
                COMMIT;
            END;
        ");
        
        // Procedure 3: Reject Peminjaman
        DB::unprepared("
            DROP PROCEDURE IF EXISTS reject_peminjaman;
            
            CREATE PROCEDURE reject_peminjaman(
                IN p_peminjaman_id INT,
                IN p_catatan TEXT,
                IN p_rejected_by INT
            )
            BEGIN
                DECLARE v_user_id INT;
                DECLARE v_ruang_id INT;
                DECLARE v_nama_ruang VARCHAR(100);
                DECLARE v_error VARCHAR(255);
                
                DECLARE EXIT HANDLER FOR SQLEXCEPTION
                BEGIN
                    GET DIAGNOSTICS CONDITION 1 v_error = MESSAGE_TEXT;
                    ROLLBACK;
                    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = v_error;
                END;
                
                START TRANSACTION;
                
                SELECT p.id_user, p.id_ruang, r.nama_ruang
                INTO v_user_id, v_ruang_id, v_nama_ruang
                FROM peminjaman p
                JOIN ruang r ON p.id_ruang = r.id_ruang
                WHERE p.id_peminjaman = p_peminjaman_id;
                
                UPDATE peminjaman
                SET status = 'rejected',
                    catatan = p_catatan,
                    updated_at = NOW()
                WHERE id_peminjaman = p_peminjaman_id;
                
                INSERT INTO notifications (user_id, title, message, type, created_at, updated_at)
                VALUES (
                    v_user_id,
                    'Peminjaman Ditolak',
                    CONCAT('Peminjaman ruangan ', v_nama_ruang, ' ditolak. Alasan: ', p_catatan),
                    'rejection',
                    NOW(),
                    NOW()
                );
                
                COMMIT;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP FUNCTION IF EXISTS count_peminjaman_by_user");
        DB::unprepared("DROP FUNCTION IF EXISTS count_peminjaman_by_status");
        DB::unprepared("DROP FUNCTION IF EXISTS check_room_availability");
        
        DB::unprepared("DROP PROCEDURE IF EXISTS update_room_status_after_approval");
        DB::unprepared("DROP PROCEDURE IF EXISTS approve_peminjaman");
        DB::unprepared("DROP PROCEDURE IF EXISTS reject_peminjaman");
    }
};