<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Trigger 1: Log Status Change
        DB::unprepared("
            DROP TRIGGER IF EXISTS log_peminjaman_status_change;
            
            CREATE TRIGGER log_peminjaman_status_change
            AFTER UPDATE ON peminjaman
            FOR EACH ROW
            BEGIN
                IF OLD.status != NEW.status THEN
                    INSERT INTO activity_log (
                        action,
                        user_id,
                        peminjaman_id,
                        old_status,
                        new_status,
                        description,
                        created_at
                    ) VALUES (
                        'status_change',
                        NEW.id_user,
                        NEW.id_peminjaman,
                        OLD.status,
                        NEW.status,
                        CONCAT('Status changed from ', OLD.status, ' to ', NEW.status),
                        NOW()
                    );
                END IF;
            END;
        ");
        
        // Trigger 2: Log Peminjaman Created
        DB::unprepared("
            DROP TRIGGER IF EXISTS log_peminjaman_created;
            
            CREATE TRIGGER log_peminjaman_created
            AFTER INSERT ON peminjaman
            FOR EACH ROW
            BEGIN
                INSERT INTO activity_log (
                    action,
                    user_id,
                    peminjaman_id,
                    new_status,
                    description,
                    created_at
                ) VALUES (
                    'created',
                    NEW.id_user,
                    NEW.id_peminjaman,
                    NEW.status,
                    CONCAT('New peminjaman created for room ID ', NEW.id_ruang),
                    NOW()
                );
            END;
        ");
        
        // Trigger 3: Decrease Room Slots
        DB::unprepared("
            DROP TRIGGER IF EXISTS decrease_room_slots;
            
            CREATE TRIGGER decrease_room_slots
            AFTER UPDATE ON peminjaman
            FOR EACH ROW
            BEGIN
                IF OLD.status != 'approved' AND NEW.status = 'approved' THEN
                    UPDATE ruang 
                    SET available_slots = GREATEST(IFNULL(available_slots, 0) - 1, 0),
                        updated_at = NOW()
                    WHERE id_ruang = NEW.id_ruang;
                END IF;
            END;
        ");
        
        // Trigger 4: Increase Room Slots
        DB::unprepared("
            DROP TRIGGER IF EXISTS increase_room_slots;
            
            CREATE TRIGGER increase_room_slots
            AFTER UPDATE ON peminjaman
            FOR EACH ROW
            BEGIN
                IF OLD.status = 'approved' AND NEW.status IN ('rejected', 'cancelled', 'selesai') THEN
                    UPDATE ruang 
                    SET available_slots = IFNULL(available_slots, 0) + 1,
                        updated_at = NOW()
                    WHERE id_ruang = OLD.id_ruang;
                END IF;
            END;
        ");
    }

    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS log_peminjaman_status_change");
        DB::unprepared("DROP TRIGGER IF EXISTS log_peminjaman_created");
        DB::unprepared("DROP TRIGGER IF EXISTS decrease_room_slots");
        DB::unprepared("DROP TRIGGER IF EXISTS increase_room_slots");
    }
};