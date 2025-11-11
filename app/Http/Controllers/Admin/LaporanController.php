<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Peminjaman;
use App\Models\Ruang;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', 'hari_ini');
        
        // Tentukan range tanggal berdasarkan periode
        $startDate = $this->getStartDate($periode);
        $endDate = now();
        
        // Query peminjaman berdasarkan periode
        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Statistik
        $stats = [
            'total_peminjaman' => $peminjaman->count(),
            'pending' => $peminjaman->where('status', 'pending')->count(),
            'approved' => $peminjaman->where('status', 'approved')->count(),
            'rejected' => $peminjaman->where('status', 'rejected')->count(),
            'selesai' => $peminjaman->where('status', 'selesai')->count(),
            'cancelled' => $peminjaman->where('status', 'cancelled')->count(),
        ];
        
        // Statistik ruangan
        $ruangStats = Peminjaman::select('id_ruang', DB::raw('COUNT(*) as total'))
            ->with('ruang')
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'selesai'])
            ->groupBy('id_ruang')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        // Statistik user (peminjam terbanyak)
        $userStats = Peminjaman::select('id_user', DB::raw('COUNT(*) as total'))
            ->with('user')
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->whereIn('status', ['approved', 'selesai'])
            ->groupBy('id_user')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
        
        return view('admin.laporan.index', compact(
            'peminjaman',
            'stats',
            'ruangStats',
            'userStats',
            'periode',
            'startDate',
            'endDate'
        ));
    }
    
    private function getStartDate($periode)
    {
        switch ($periode) {
            case 'hari_ini':
                return now()->startOfDay();
            case 'minggu_ini':
                return now()->startOfWeek();
            case 'bulan_ini':
                return now()->startOfMonth();
            case 'tahun_ini':
                return now()->startOfYear();
            default:
                return now()->startOfDay();
        }
    }
    
    public function export(Request $request)
    {
        $periode = $request->get('periode', 'hari_ini');
        $format = $request->get('format', 'excel'); // excel or csv
        $startDate = $this->getStartDate($periode);
        $endDate = now();
        
        $peminjaman = Peminjaman::with(['user', 'ruang'])
            ->whereBetween('tanggal_pinjam', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
        
        if ($format === 'excel') {
            return $this->exportExcel($peminjaman, $periode, $startDate, $endDate);
        } else {
            return $this->exportCsv($peminjaman, $periode);
        }
    }
    
    private function exportExcel($peminjaman, $periode, $startDate, $endDate)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('Sistem Peminjaman Ruang')
            ->setTitle('Laporan Peminjaman')
            ->setSubject('Laporan Peminjaman Ruangan')
            ->setDescription('Laporan peminjaman ruangan periode ' . $periode);
        
        // Title
        $sheet->setCellValue('A1', 'LAPORAN PEMINJAMAN RUANGAN');
        $sheet->mergeCells('A1:K1');
        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Periode
        $sheet->setCellValue('A2', 'Periode: ' . ucfirst(str_replace('_', ' ', $periode)));
        $sheet->mergeCells('A2:K2');
        $sheet->getStyle('A2')->getFont()->setSize(12);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Tanggal Range
        $sheet->setCellValue('A3', 'Tanggal: ' . Carbon::parse($startDate)->format('d M Y') . ' - ' . Carbon::parse($endDate)->format('d M Y'));
        $sheet->mergeCells('A3:K3');
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Header
        $headers = ['No', 'Tgl Pengajuan', 'Peminjam', 'Ruangan', 'Tgl Mulai', 'Tgl Selesai', 'Waktu Mulai', 'Waktu Selesai', 'Keperluan', 'Status', 'Catatan'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '5', $header);
            $column++;
        }
        
        // Style header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2C3E50']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];
        $sheet->getStyle('A5:K5')->applyFromArray($headerStyle);
        
        // Data
        $row = 6;
        $no = 1;
        foreach ($peminjaman as $p) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $p->created_at->format('d/m/Y H:i'));
            $sheet->setCellValue('C' . $row, $p->user->nama ?? '-');
            $sheet->setCellValue('D' . $row, $p->ruang->nama_ruang ?? '-');
            $sheet->setCellValue('E' . $row, Carbon::parse($p->tanggal_pinjam)->format('d/m/Y'));
            $sheet->setCellValue('F' . $row, Carbon::parse($p->tanggal_kembali)->format('d/m/Y'));
            $sheet->setCellValue('G' . $row, $p->waktu_mulai);
            $sheet->setCellValue('H' . $row, $p->waktu_selesai);
            $sheet->setCellValue('I' . $row, $p->keperluan);
            $sheet->setCellValue('J' . $row, strtoupper($p->status));
            $sheet->setCellValue('K' . $row, $p->catatan ?? '-');
            
            // Style data rows
            $sheet->getStyle('A' . $row . ':K' . $row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ]
            ]);
            
            // Color coding for status
            $statusColor = $this->getStatusColor($p->status);
            $sheet->getStyle('J' . $row)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $statusColor]
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);
            
            $row++;
        }
        
        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Set row height for title
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(5)->setRowHeight(25);
        
        // Footer - Summary
        $row++;
        $sheet->setCellValue('A' . $row, 'Total Peminjaman: ' . $peminjaman->count());
        $sheet->mergeCells('A' . $row . ':K' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        // Create writer and download
        $filename = 'laporan_peminjaman_' . $periode . '_' . date('YmdHis') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    }
    
    private function exportCsv($peminjaman, $periode)
    {
        $filename = 'laporan_peminjaman_' . $periode . '_' . date('YmdHis') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        
        $output = fopen('php://output', 'w');
        
        // Header CSV
        fputcsv($output, [
            'No',
            'Tanggal Pengajuan',
            'Nama Peminjam',
            'Ruangan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Waktu Mulai',
            'Waktu Selesai',
            'Keperluan',
            'Status',
            'Catatan'
        ]);
        
        // Data
        $no = 1;
        foreach ($peminjaman as $p) {
            fputcsv($output, [
                $no++,
                $p->created_at->format('d/m/Y H:i'),
                $p->user->nama ?? '-',
                $p->ruang->nama_ruang ?? '-',
                Carbon::parse($p->tanggal_pinjam)->format('d/m/Y'),
                Carbon::parse($p->tanggal_kembali)->format('d/m/Y'),
                $p->waktu_mulai,
                $p->waktu_selesai,
                $p->keperluan,
                strtoupper($p->status),
                $p->catatan ?? '-'
            ]);
        }
        
        fclose($output);
        exit();
    }
    
    private function getStatusColor($status)
    {
        switch ($status) {
            case 'pending':
                return 'FFC107'; // Yellow
            case 'approved':
                return '28A745'; // Green
            case 'rejected':
                return 'DC3545'; // Red
            case 'selesai':
                return '6C757D'; // Gray
            case 'cancelled':
                return '6C757D'; // Gray
            default:
                return '007BFF'; // Blue
        }
    }
}
