@extends('layouts.app')

@section('title', 'Jadwal Ruangan')

@section('breadcrumb')
<li class="breadcrumb-item active" aria-current="page">
    <i class="fas fa-calendar-alt"></i> Jadwal Ruangan
</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
        }

        .room-card {
            height: 100%;
            background: white;
            transition: all 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-3px);
        }

        .btn-primary {
            background: #2c3e50;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: #1a252f;
            transform: translateY(-2px);
        }

        .calendar-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
        }

        #calendar .fc-toolbar-title {
            font-size: 1.25rem;
            color: #2c3e50;
        }

        #calendar .fc-button {
            background: #2c3e50;
            border: none;
        }

        h5 
    </style>

    <!-- Room Status Section -->
    <div class="row mb-4">
        @foreach($ruangs as $ruang)
        <div class="col-md-3 mb-3">
            <div class="card room-card">
                <div class="card-body text-center">
                    <i class="fas fa-door-open fa-3x mb-3 text-{{ $ruang->status == 'kosong' ? 'success' : 'danger' }}"></i>
                    <h5 class="text-muted mb-2">{{ $ruang->nama_ruang }}</h5>
                    <p class="text-muted mb-2">Kapasitas: {{ $ruang->kapasitas }} orang</p>
                    <span class="badge bg-{{ $ruang->status == 'kosong' ? 'success' : 'danger' }}">
                        {{ $ruang->status == 'kosong' ? 'Tersedia' : 'Dipakai' }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Calendar Section -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-calendar-alt"></i> Jadwal Peminjaman</h5>
            <select class="form-select form-select-sm w-auto" id="ruangFilter">
                <option value="">Semua Ruangan</option>
                @foreach($ruangs as $ruang)
                    <option value="{{ $ruang->id_ruang }}">{{ $ruang->nama_ruang }}</option>
                @endforeach
            </select>
        </div>
        <div class="card-body">
            <div id="calendar"></div>
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
                                     style="width: 20px; height: 20px; background-color: {{ sprintf('#%06X', mt_rand(0, 0xFFFFFF)) }}; border-radius: 3px;"></div>
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

@push('scripts')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
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
        events: [
            @foreach($peminjaman as $item)
            {
                title: '{{ $item->ruang->nama_ruang }} - {{ $item->user->nama }}',
                start: '{{ $item->tanggal_pinjam }}T{{ $item->waktu_mulai }}',
                end: '{{ $item->tanggal_kembali }}T{{ $item->waktu_selesai }}',
                backgroundColor: '{{ sprintf('#%06X', mt_rand(0, 0xFFFFFF)) }}',
                extendedProps: {
                    keperluan: '{{ $item->keperluan }}',
                    status: '{{ $item->status }}'
                }
            }@if(!$loop->last),@endif
            @endforeach
        ],
        eventDidMount: function(info) {
            $(info.el).tooltip({
                title: `${info.event.extendedProps.keperluan}\nStatus: ${info.event.extendedProps.status}`,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: false,
            hour12: false
        }
    });
    calendar.render();

    // Filter events based on room selection
    document.getElementById('ruangFilter').addEventListener('change', function() {
        const selectedRuang = this.value;
        const allEvents = calendar.getEvents();
        
        allEvents.forEach(event => {
            if (!selectedRuang || event.title.includes(this.options[this.selectedIndex].text)) {
                event.setProp('display', 'auto');
            } else {
                event.setProp('display', 'none');
            }
        });
    });
});
</script>

<style>
#calendar {
    background-color: white;
    border-radius: 8px;
    padding: 20px;
}

.fc-event {
    cursor: pointer;
}

.fc-day-today {
    background-color: #e8f4fd !important;
}
</style>
@endpush
@endsection