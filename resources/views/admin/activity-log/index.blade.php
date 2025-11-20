@extends('layouts.app')

@section('title', 'Activity Log')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h4><i class="fas fa-history"></i> Activity Log</h4>
            <p class="text-muted">Log aktivitas yang tercatat otomatis oleh TRIGGER</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3>{{ $stats['total'] }}</h3>
                    <p class="mb-0">Total Log</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-primary text-white">
                <div class="card-body">
                    <h3>{{ $stats['today'] }}</h3>
                    <p class="mb-0">Log Hari Ini</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-warning">
                <div class="card-body">
                    <h3>{{ $stats['status_changes'] }}</h3>
                    <p class="mb-0">Status Changes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center bg-success text-white">
                <div class="card-body">
                    <h3>{{ $stats['created'] }}</h3>
                    <p class="mb-0">New Records</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label>Action</label>
                    <select name="action" class="form-select">
                        <option value="">Semua Action</option>
                        <option value="status_change" {{ request('action') == 'status_change' ? 'selected' : '' }}>
                            Status Change
                        </option>
                        <option value="created" {{ request('action') == 'created' ? 'selected' : '' }}>
                            Created
                        </option>
                        <option value="approve" {{ request('action') == 'approve' ? 'selected' : '' }}>
                            Approve
                        </option>
                        <option value="reject" {{ request('action') == 'reject' ? 'selected' : '' }}>
                            Reject
                        </option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Tanggal</label>
                    <input type="date" name="date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('admin.activity-log.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Log Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Action</th>
                            <th>User</th>
                            <th>Peminjaman</th>
                            <th>Perubahan Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                                <br>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                @if($log->action == 'status_change')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-exchange-alt"></i> Status Change
                                    </span>
                                @elseif($log->action == 'created')
                                    <span class="badge bg-info">
                                        <i class="fas fa-plus"></i> Created
                                    </span>
                                @elseif($log->action == 'approve')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check"></i> Approve
                                    </span>
                                @elseif($log->action == 'reject')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times"></i> Reject
                                    </span>
                                @else
                                    <span class="badge bg-secondary">{{ $log->action }}</span>
                                @endif
                            </td>
                            <td>
                                @if($log->user)
                                    <strong>{{ $log->user->nama }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $log->user->username }}</small>
                                @else
                                    <span class="text-muted">System</span>
                                @endif
                            </td>
                            <td>
                                @if($log->peminjaman)
                                    <a href="{{ route('admin.peminjaman.show', $log->peminjaman_id) }}">
                                        #{{ $log->peminjaman_id }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        {{ $log->peminjaman->ruang->nama_ruang ?? '-' }}
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($log->old_status)
                                    <span class="badge bg-secondary">{{ $log->old_status }}</span>
                                    <i class="fas fa-arrow-right mx-1"></i>
                                @endif
                                @if($log->new_status)
                                    <span class="badge 
                                        @if($log->new_status == 'approved') bg-success
                                        @elseif($log->new_status == 'rejected') bg-danger
                                        @elseif($log->new_status == 'pending') bg-warning
                                        @elseif($log->new_status == 'selesai') bg-info
                                        @else bg-secondary
                                        @endif
                                    ">
                                        {{ $log->new_status }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $log->description }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Belum ada activity log</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection