@extends('layouts.app')

@section('title', 'Absensi Proyek')

@section('content')
<div>
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold mb-2">Absensi Proyek</h1>
        <p class="text-muted mb-0">Kelola absensi harian pekerja per proyek</p>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <form id="filterForm" method="GET" action="{{ route('attendances.index') }}">
                <div class="row g-3 align-items-end">
                    <!-- Date Picker -->
                    <div class="col-md-4">
                        <label class="form-label fw-medium mb-1">Tanggal</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="ri-calendar-line text-primary"></i>
                            </span>
                            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="form-control">
                        </div>
                    </div>

                    <!-- Project Dropdown -->
                    <div class="col-md-5">
                        <label class="form-label fw-medium mb-1">Proyek</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="ri-building-line text-primary"></i>
                            </span>
                            <select name="project_id" class="form-select">
                                <option value="">Semua Proyek</option>
                                @foreach ($projects as $project)
                                <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Filter Status -->
                    <div class="col-md-3">
                        <div class="mb-1">
                            <label for="status" class="form-label fw-medium mb-1">Status</label>
                        </div>
                        <select name="status" id="status" class="form-select">
                            <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>1 Hari</option>
                            <option value="0.5" {{ request('status') == '0.5' ? 'selected' : '' }}>Setengah Hari</option>
                            <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>2 Hari</option>
                            <option value="1.5" {{ request('status') == '1.5' ? 'selected' : '' }}>1.5 Hari</option>
                            <option value="not_working" {{ request('status') == 'not_working' ? 'selected' : '' }}>Tidak Bekerja</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="ri-filter-line me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('attendances.index') }}" class="btn btn-outline-secondary">
                        <i class="ri-refresh-line me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="px-0 mb-4">
        <div class="row g-3">
            <!-- Total Pekerja -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                <div class="card shadow-sm border h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-dark bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="ri-team-line text-dark fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Total Absensi</p>
                                <h3 class="h4 fw-bold mb-0">{{ $attendances->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1 Hari Kerja -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                <div class="card shadow-sm border h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="ri-user-follow-line text-success fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">1 Hari Kerja</p>
                                <h3 class="h4 fw-bold text-success mb-0">{{ $attendances->where('status', '1_hari')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 0.5 Hari Kerja -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                <div class="card shadow-sm border h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="ri-time-line text-warning fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">0.5 Hari Kerja</p>
                                <h3 class="h4 fw-bold text-warning mb-0">{{ $attendances->where('status', 'setengah_hari')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 1.5 Hari Kerja -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                <div class="card shadow-sm border h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-info bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="ri-calendar-todo-line text-info fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">1.5 Hari Kerja</p>
                                <h3 class="h4 fw-bold text-info mb-0">{{ $attendances->where('status', '1.5_hari')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2 Hari Kerja -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                <div class="card shadow-sm border h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="ri-calendar-event-line text-primary fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">2 Hari Kerja</p>
                                <h3 class="h4 fw-bold text-primary mb-0">{{ $attendances->where('status', '2_hari')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tidak Bekerja -->
            <div class="col-6 col-sm-6 col-md-4 col-lg-4 col-xl-2">
                <div class="card shadow-sm border h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 p-3 rounded-3 me-3">
                                <i class="ri-close-line text-danger fs-4"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-0 small">Tidak Bekerja</p>
                                <h3 class="h4 fw-bold text-danger mb-0">{{ $attendances->where('status', 'tidak_bekerja')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Section -->
<div class="card shadow-sm border mb-4">
    <div class="card-header bg-white border-bottom p-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="card-title mb-1">Data Absensi - {{ \Carbon\Carbon::parse(request('date', now()))->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</h5>
                <p class="text-muted small mb-0">Menampilkan {{ $attendances->count() }} data</p>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
        @if($attendances->count() == 0)
         <div class="col-12">
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="ri-refresh-line fa-2x mb-2"></i>
                        <p class="mb-0">Belum ada data absensi</p>
                    </div>
                </div>
            </div>
        @else   
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-uppercase small text-muted px-4 py-3 align-middle">No</th>
                        <th class="text-uppercase small text-muted px-4 py-3 align-middle">Pekerja</th>
                        <th class="text-uppercase small text-muted px-4 py-3 align-middle">Proyek</th>
                        <th class="text-uppercase small text-muted px-4 py-3 align-middle text-center">Status</th>
                        <th class="text-uppercase small text-muted px-4 py-3 align-middle text-center">Lembur</th>
                        <th class="text-uppercase small text-muted px-4 py-3 align-middle">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $index => $attendance)
                    <tr>
                        <td class="px-4 py-3 align-middle">{{ $loop->iteration }}</td>
                        <td class="px-2 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-none d-sm-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #e7f1ff;">
                                    <span class="text-primary fw-medium small">{{ substr($attendance->worker->name, 0, 1) }}</span>
                                </div>
                                <div>
                                    @if($attendance->worker->role == 'mandor')
                                    <div class="fw-medium text-dark">{{ $attendance->worker->name }}</div>
                                    <i class="ri-user-3-line text-primary small d-block"> Mandor</i>
                                    @elseif($attendance->worker->role == 'tukang')
                                    <div class="fw-medium text-dark">{{ $attendance->worker->name }}</div>
                                    <i class="ri-hammer-line text-success small d-block"> Tukang</i>
                                    @elseif($attendance->worker->role == 'peladen')
                                    <div class="fw-medium text-dark">{{ $attendance->worker->name }}</div>
                                    <i class="ri-hammer-line text-warning small d-block"> Peladen</i>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 align-middle text-nowrap">
                            <div>{{ $attendance->project->name }}</div>
                            <small class="text-muted">Lokasi: {{ $attendance->project->location }}</small>
                        </td>
                        <td class="px-4 py-3 align-middle text-center">
                            @if($attendance->status == '1_hari')
                            <span class="badge bg-success bg-opacity-10 text-success"><i class="ri-check-line me-1"></i> 1 Hari</span>
                            @elseif($attendance->status == 'setengah_hari')
                            <span class="badge bg-warning bg-opacity-10 text-warning"><i class="ri-half-line me-1"></i> Setengah Hari</span>
                            @elseif($attendance->status == '1.5_hari')
                            <span class="badge bg-info bg-opacity-10 text-info"><i class="ri-calendar-todo-line me-1"></i> 1.5 Hari</span>
                            @elseif($attendance->status == 'tidak_bekerja')
                            <span class="badge bg-danger bg-opacity-10 text-danger"><i class="ri-close-line me-1"></i> Tidak Bekerja</span>
                            @elseif($attendance->status == '2_hari')
                            <span class="badge bg-primary bg-opacity-10 text-primary"><i class="ri-calendar-event-line me-1"></i> 2 Hari</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 align-middle text-center">
                            {{ $attendance->overtime_hours > 0 ? $attendance->overtime_hours . ' Jam' : '-' }}
                        </td>
                        <td class="px-4 py-3 align-middle text-nowrap">
                            <button class="btn btn-sm btn-light text-primary border-0" data-bs-toggle="modal" data-bs-target="#editAttendanceModal" data-id="{{ $attendance->id }}" data-status="{{ $attendance->status }}" data-overtime-hours="{{ $attendance->overtime_hours }}">
                                <i class="ri-edit-line"></i>
                            </button>
                            <form action="{{ route('attendances.destroy', $attendance->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light text-danger border-0" onclick="return confirm('Yakin ingin menghapus data absensi ini?')">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<!-- Edit Attendance Modal -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-labelledby="editAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-semibold" id="editAttendanceModalLabel">
                    <i class="ri-edit-2-line me-2"></i>Edit Data Absensi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAttendanceForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 mb-4" role="alert">
                        <i class="ri-information-line me-2"></i> Silakan perbarui data absensi sesuai kebutuhan.
                    </div>

                    <div class="mb-4">
                        <label for="status" class="form-label fw-medium text-muted mb-2">Status Kehadiran</label>
                        <select class="form-select form-select-lg" id="status" name="status" required>
                            <option value="1_hari" class="text-success">
                                <i class="ri-checkbox-circle-line me-2"></i> 1 Hari
                            </option>
                            <option value="setengah_hari" class="text-warning">
                                <i class="ri-time-line me-2"></i> Setengah Hari
                            </option>
                            <option value="1.5_hari" class="text-info">
                                <i class="ri-time-line me-2"></i> 1.5 Hari
                            </option>
                            <option value="tidak_bekerja" class="text-danger">
                                <i class="ri-close-circle-line me-2"></i> Tidak Bekerja
                            </option>
                            <option value="2_hari" class="text-primary">
                                <i class="ri-calendar-check-line me-2"></i> 2 Hari
                            </option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="overtime_hours" class="form-label fw-medium text-muted mb-2">
                            <i class="ri-time-line me-2"></i>Jam Lembur
                        </label>
                        <select class="form-select form-select-lg" id="overtime_hours" name="overtime_hours">
                            <option value="0">-</option>
                            <option value="1">1 Jam</option>
                            <option value="2">2 Jam</option>
                            <option value="3">3 Jam</option>
                            <option value="4">4 Jam</option>
                            <option value="5">5 Jam</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                        <i class="ri-close-line me-2"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="ri-save-line me-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    .modal-content {
        border-radius: 12px;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
        padding: 1.25rem 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
    }

    .form-select {
        background-position: right 1rem center;
    }

    .btn {
        border-radius: 8px;
        padding: 0.5rem 1.25rem;
        font-weight: 500;
    }

    .btn-primary {
        background-color: #4361ee;
        border-color: #4361ee;
    }

    .btn-primary:hover {
        background-color: #3a56d4;
        border-color: #3a56d4;
    }

    .form-label {
        font-size: 0.875rem;
    }

</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editModal = document.getElementById('editAttendanceModal');
        if (editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const status = button.getAttribute('data-status');
                const overtimeHours = button.getAttribute('data-overtime-hours');

                const form = editModal.querySelector('#editAttendanceForm');
                form.action = `/attendances/${id}`;

                const statusSelect = editModal.querySelector('#status');
                statusSelect.value = status;

                const overtimeInput = editModal.querySelector('#overtime_hours');
                overtimeInput.value = overtimeHours || '0';
            });
        }
    });

</script>
@endpush

@endsection
