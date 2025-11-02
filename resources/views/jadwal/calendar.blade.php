@extends('layouts.app')

@section('title', 'Kalender Jadwal Ruangan')

@section('breadcrumb')
<li class="breadcrumb-item">
    <a href="{{ route('jadwal.index') }}">Jadwal Ruangan</a>
</li>
<li class="breadcrumb-item active">Kalender</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-calendar"></i> Kalender Jadwal Ruangan</h4>
                <div>
                    <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">
                        <i class="fas fa-list"></i> Tampilan List
                    </a>
                    @auth
                        <a href="{{ Auth::user()->isPeminjam() ? route('peminjam.dashboard') : (Auth::user()->isAdmin() ? route('admin.dashboard') : route('petugas.dashboard')) }}" 
                           class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Kalender Peminjaman</h5>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="row mt-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-palette"></i> Keterangan Warna</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($ruangs as $index => $ruang)
                        <div class="col-md-2 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="color-box me-2" 
                                     style="width: 20px; height: 20px; background-color: {{ $events[$index]['color'] ?? '#666' }}; border-radius: 3px;"></div>
                                <small>{{ $ruang->nama_ruang }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<style>
#calendar {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
}

.color-box {
    border: 1px solid #ddd;
}

.fc-event {
    cursor: pointer;
}

.fc-day-today {
    background-color: #e8f4fd !important;
}
</style>

<!-- Include FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            day: 'Hari'
        },
        events: @json($events),
        eventClick: function(info) {
            // You can add modal or redirect to detail page here
            console.log('Event: ', info.event.title);
            console.log('Start: ', info.event.start);
            console.log('End: ', info.event.end);
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false,
            hour12: false
        }
    });
    calendar.render();
});
</script>
@endsection