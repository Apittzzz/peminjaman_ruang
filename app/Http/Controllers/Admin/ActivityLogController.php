<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // ========================================
        // LIHAT HASIL TRIGGER: log_peminjaman_status_change
        // ========================================
        
        $query = ActivityLog::with(['user', 'peminjaman.ruang'])
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by date
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $logs = $query->paginate(20);

        // Get statistics
        $stats = [
            'total' => ActivityLog::count(),
            'today' => ActivityLog::whereDate('created_at', today())->count(),
            'status_changes' => ActivityLog::where('action', 'status_change')->count(),
            'created' => ActivityLog::where('action', 'created')->count(),
        ];

        return view('admin.activity-log.index', compact('logs', 'stats'));
    }
}