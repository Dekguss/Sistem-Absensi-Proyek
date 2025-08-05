@extends('layouts.app')

@section('title', 'Input Absensi')

@section('content')
<div>
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold mb-2">Input Absensi Harian</h1>
        <p class="text-muted mb-0">Input kehadiran pekerja per proyek setiap hari</p>
    </div>

    <!-- Form Input Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('attendances.create') }}">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="projectSelect" class="form-label fw-semibold">
                            Pilih Proyek <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <select id="projectSelect" name="project_id" class="form-select" required onchange="this.form.submit()">
                                <option value="" disabled {{ !request('project_id') ? 'selected' : '' }}>Pilih proyek...</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="attendanceDate" class="form-label fw-semibold">
                            Tanggal Absensi <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input type="date" id="attendanceDate" name="attendance_date" value="{{ request('attendance_date', date('Y-m-d')) }}" class="form-control" required onchange="this.form.submit()" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!request('project_id') || !request('attendance_date'))
    <div class="card shadow-sm">
        <div class="card-body text-center p-5">
            <div class="mb-4">
                <i class="ri-calendar-todo-line text-muted" style="font-size: 4rem;"></i>
            </div>
            <h4 class="h5 text-gray-800 mb-3">Pilih Proyek dan Tanggal</h4>
            <p class="text-muted mb-4">Silakan pilih proyek dan tanggal terlebih dahulu untuk melihat daftar pekerja.</p>
        </div>
    </div>
    @else
    <!-- Status Summary Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="mb-4">
                <h2 class="h5 fw-semibold text-gray-900 mb-1" id="attendanceTitle">
                    Absensi {{ $projects->firstWhere('id', request('project_id'))->name ?? 'Pilih Proyek' }} -
                    {{ \Carbon\Carbon::parse(request('attendance_date'))->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                </h2>
                <p class="text-muted mb-0">
                    Input kehadiran {{ $workers->count() }} pekerja
                </p>
            </div>

            <div class="row g-3 text-center">
                <div class="col-6 col-md">
                    <div id="total-workers" class="h3 fw-bold text-gray-900 mb-1">{{ $workers->count() }}</div>
                    <div class="small text-muted">Total</div>
                </div>
                <div class="col-6 col-md">
                    <div id="count-1-hari" class="h3 fw-bold text-success mb-1">{{ $workers->where('attendance_status', '1_hari')->count() }}</div>
                    <div class="small text-muted">1 Hari Kerja</div>
                </div>
                <div class="col-6 col-md">
                    <div id="count-setengah-hari" class="h3 fw-bold text-warning mb-1">{{ $workers->where('attendance_status', 'setengah_hari')->count() }}</div>
                    <div class="small text-muted">Setengah Hari</div>
                </div>
                <div class="col-6 col-md">
                    <div id="count-1.5-hari" class="h3 fw-bold text-info mb-1">{{ $workers->where('attendance_status', '1.5_hari')->count() }}</div>
                    <div class="small text-muted">1.5 Hari Kerja</div>
                </div>
                <div class="col-6 col-md">
                    <div id="count-2-hari" class="h3 fw-bold text-primary mb-1">{{ $workers->where('attendance_status', '2_hari')->count() }}</div>
                    <div class="small text-muted">2 Hari Kerja</div>
                </div>
                <div class="col-6 col-md">
                    <div id="count-tidak-bekerja" class="h3 fw-bold text-danger mb-1">{{ $workers->where('attendance_status', 'tidak_bekerja')->count() }}</div>
                    <div class="small text-muted">Tidak Bekerja</div>
                </div>
                
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <!-- Worker List -->
        <div class="mt-2 p-4">
            <h5 class="fw-semibold mb-3">Daftar Pekerja</h5>
            <form method="POST" action="{{ route('attendances.store') }}" id="attendance-form">
                @csrf
                <input type="hidden" name="project_id" value="{{ request('project_id') }}">
                <input type="hidden" name="attendance_date" value="{{ request('attendance_date') }}">

                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle">
                        <thead>
                            <tr>
                                <th class="align-middle">Nama Pekerja</th>
                                <th class="text-center align-middle">Status</th>
                                <th class="text-center align-middle">Lembur</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($workers as $worker)
                            <tr>
                                <td class="px-2 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-none d-sm-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #e7f1ff;">
                                            <span class="text-primary fw-medium small">{{ strtoupper(substr($worker->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            @if($worker->role == 'mandor')
                                            <div class="fw-medium text-dark">{{ $worker->name }}</div>
                                            <i class="ri-user-3-line text-primary small d-block"> Mandor</i>
                                            @elseif($worker->role == 'tukang')
                                            <div class="fw-medium text-dark">{{ $worker->name }}</div>
                                            <i class="ri-hammer-line text-success small d-block"> Tukang</i>
                                            @elseif($worker->role == 'peladen')
                                            <div class="fw-medium text-dark">{{ $worker->name }}</div>
                                            <i class="ri-hammer-line text-warning small d-block"> Peladen</i>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center">
                                        <select name="attendance[{{ $worker->id }}]" class="form-select form-select-sm" style="width: auto;">
                                            <option value="1_hari" {{ $worker->attendance_status == '1_hari' ? 'selected' : '' }}>1 Hari</option>
                                            <option value="setengah_hari" {{ $worker->attendance_status == 'setengah_hari' ? 'selected' : '' }}>Setengah Hari</option>
                                            <option value="tidak_bekerja" {{ $worker->attendance_status == 'tidak_bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                                            <option value="1.5_hari" {{ $worker->attendance_status == '1.5_hari' ? 'selected' : '' }}>1.5 Hari</option>
                                            <option value="2_hari" {{ $worker->attendance_status == '2_hari' ? 'selected' : '' }}>2 Hari</option>
                                        </select>
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="d-flex justify-content-center">
                                        <select name="overtime[{{ $worker->id }}]" class="form-select form-select-sm" style="width: auto;">
                                            <option value="0" {{ $worker->overtime == '0' ? 'selected' : '' }}>0 Jam</option>
                                            <option value="1" {{ $worker->overtime == '1' ? 'selected' : '' }}>1 Jam</option>
                                            <option value="2" {{ $worker->overtime == '2' ? 'selected' : '' }}>2 Jam</option>
                                            <option value="3" {{ $worker->overtime == '3' ? 'selected' : '' }}>3 Jam</option>
                                            <option value="4" {{ $worker->overtime == '4' ? 'selected' : '' }}>4 Jam</option>
                                            <option value="5" {{ $worker->overtime == '5' ? 'selected' : '' }}>5 Jam</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-2-line me-2"></i>Simpan Absensi
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('attendance-form');
        if (!form) return; // Pastikan form ada di halaman

        const statusSelects = form.querySelectorAll('select[name^="attendance["]');

        // Inisialisasi status awal
        const workerStatus = {};
        statusSelects.forEach(select => {
            const match = select.name.match(/\[(\d+)\]/);
            if (match) {
                workerStatus[match[1]] = select.value;
            }
        });

        // Fungsi untuk update counter
        function updateCounters() {
            const counts = {
                '1_hari': 0
                , 'setengah_hari': 0
                , 'tidak_bekerja': 0
                , '1.5_hari': 0
                , '2_hari': 0
            };

            // Hitung status
            Object.values(workerStatus).forEach(status => {
                if (status in counts) {
                    counts[status]++;
                }
            });

            // Update UI
            document.getElementById('count-1-hari').textContent = counts['1_hari'];
            document.getElementById('count-setengah-hari').textContent = counts['setengah_hari'];
            document.getElementById('count-tidak-bekerja').textContent = counts['tidak_bekerja'];
            document.getElementById('count-1.5-hari').textContent = counts['1.5_hari'];
            document.getElementById('count-2-hari').textContent = counts['2_hari'];
        }

        // Event listener untuk perubahan status
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const match = this.name.match(/\[(\d+)\]/);
                if (match) {
                    workerStatus[match[1]] = this.value;
                    updateCounters();
                }
            });
        });

        // Inisialisasi awal
        updateCounters();
    });

</script>
@endpush
