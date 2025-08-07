@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold mb-2">Laporan Absensi</h1>
        <p class="text-muted mb-0">Export laporan absensi pekerja per proyek ke format Excel</p>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form id="reportForm" method="GET" action="{{ route('attendances.report') }}" class="mb-4">
                <div class="row g-3">   
                    <div class="col-md-6">
                        <label class="form-label fw-medium">Pilih Proyek <span class="text-danger">*</span></label>
                        <select name="project_id" class="form-select" required>
                            <option value="">Pilih Proyek...</option>
                            @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $startDate ?? now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate ?? now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ri-eye-line me-1"></i> Preview Laporan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(isset($selectedProject))
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="project-info">
                    <h5 class="mb-0 text-uppercase fw-bold">
                        Laporan Absensi Pekerja - {{ $selectedProject->name }}
                    </h5>
                    <div class="d-flex gap-3 mt-2">
                        <p class="text-muted small mb-0">
                            <i class="ri-calendar-line me-1"></i> 
                            Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}
                        </p>
                        <p class="text-muted small mb-0">
                            <i class="ri-group-line me-1"></i> 
                            Total Pekerja: {{ $selectedProject->workers->count() + 1 }}
                        </p>
                    </div>
                </div>
                <div class="ms-3">
                    <form action="{{ route('attendances.export', $selectedProject->id) }}" method="GET" class="m-0">
                        <input type="hidden" name="project_id" value="{{ $projectId }}">
                        <input type="hidden" name="start_date" value="{{ $startDate }}">
                        <input type="hidden" name="end_date" value="{{ $endDate }}">
                        <button type="submit" class="btn btn-success">
                            <i class="ri-file-excel-line me-1"></i> Export Excel
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="align-middle">NAME</th>
                            <th rowspan="2" class="align-middle">POSITION</th>
                            @foreach($dates as $date)
                            <th colspan="2" class="text-center" style="min-width: 100px;">
                                <div>{{ \Carbon\Carbon::parse($date)->isoFormat('ddd') }}</div>
                            </th>
                            @endforeach
                            <th rowspan="2" class="align-middle text-center text-uppercase">Total OT</th>
                            <th rowspan="2" class="align-middle text-center text-uppercase">Work Day</th>
                            <th rowspan="2" class="align-middle text-center text-uppercase">Wage</th>
                            <th rowspan="2" class="align-middle text-center text-uppercase">OT Wage</th>
                            <th rowspan="2" class="align-middle text-center text-uppercase">Total Wage</th>
                            <th rowspan="2" class="align-middle text-center text-uppercase">Total Overtime</th>
                            <th rowspan="2" class="align-middle text-center text-uppercase">Grand Total</th>
                        </tr>
                        <tr>
                            @foreach($dates as $date)
                            <th class="text-center" style="min-width: 100px;">
                                <div>{{ \Carbon\Carbon::parse($date)->format('d/m') }}</div>
                            </th>
                            <th class="text-center" style="min-width: 100px;">OT</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        // pengelompokan absensi berdasarkan pekerja
                        $groupedAttendances = [];
                        foreach ($attendances as $date => $dateAttendances) {
                            foreach ($dateAttendances as $attendance) {
                                $workerId = $attendance->worker_id;
                                if (!isset($groupedAttendances[$workerId])) {
                                    $groupedAttendances[$workerId] = [
                                        'worker' => $attendance->worker,
                                        'attendances' => [],
                                        'total_ot' => 0,
                                        'work_days' => 0,
                                        'wage' => 0,
                                        'ot_wage' => 0,
                                        'total_wage' => 0,
                                        'total_overtime' => 0,
                                        'grand_total' => 0
                                    ];
                                }
                                $groupedAttendances[$workerId]['attendances'][$date] = $attendance;
                                $groupedAttendances[$workerId]['total_ot'] += $attendance->overtime_hours;

                                // penghitungan work days (1 for full day, 0.5 for half day, etc.)
                                $status = $attendance->status;
                                $workDay = 0;
                                if ($status === '1_hari') {
                                    $workDay = 1;
                                } elseif ($status === 'setengah_hari') {
                                    $workDay = 0.5;
                                } elseif ($status === '1.5_hari') {
                                    $workDay = 1.5;
                                } elseif ($status === '2_hari') {
                                    $workDay = 2;
                                }

                                $groupedAttendances[$workerId]['work_days'] += $workDay;
                            }
                        }

                        // penghitungan gaji dan total
                        foreach ($groupedAttendances as &$workerData) {
                            $worker = $workerData['worker'];

                            // Set OT wage based on role (20000 for mandor/tukang, 15000 for peladen)
                            $otRate = in_array($worker->role, ['mandor', 'tukang']) ? 20000 : 15000;
                            $dailyWage = $worker->daily_salary;

                            $workerData['wage'] = $dailyWage;
                            $workerData['ot_wage'] = $otRate;
                            $workerData['total_wage'] = $dailyWage * $workerData['work_days'];
                            $workerData['total_overtime'] = $workerData['total_ot'] * $otRate;
                            $workerData['grand_total'] = $workerData['total_wage'] + $workerData['total_overtime'];
                        }
                        unset($workerData); // Break the reference
                        @endphp

                        @php
                        // filter mandor, tukang, peladen
                        $sortedAttendances = collect($groupedAttendances)->sortBy(function($item) {
                            $roleOrder = ['mandor' => 1, 'tukang' => 2, 'peladen' => 3];
                            $role = strtolower($item['worker']->role);
                            return $roleOrder[$role] ?? 999; // Default to end if role not in order
                        });
                        @endphp

                    @foreach($sortedAttendances as $workerId => $data)
                        @php
                        $worker = $data['worker'];
                        $workerAttendances = $data['attendances'];
                        @endphp
                        <tr>
                            <td class="text-uppercase">{{ $worker->name }}</td>
                            <td class="text-uppercase">{{ $worker->role }}</td>
                            @foreach($dates as $date)
                                @php
                                $attendance = $workerAttendances[$date] ?? null;
                                $status = $attendance ? $attendance->status : '';
                                $overtime = $attendance ? $attendance->overtime_hours : 0;

                                $statusText = '';
                                $bgclass = '';
                                if ($status === '1_hari') {
                                    $statusText = '1';
                                } elseif ($status === 'setengah_hari') {
                                    $statusText = '0,5';
                                } elseif ($status === '1.5_hari') {
                                    $statusText = '1,5';
                                } elseif ($status === '2_hari') {
                                    $statusText = '2';
                                } elseif ($status === 'tidak_bekerja') {
                                    $bgclass = 'bg-danger';
                                }

                                $overtimeClass = empty($overtime) ? 'bg-danger' : '';
                                $finalBgClass = $bgclass ?: $overtimeClass;

                                // cek apakah absensi null karena tidak ada absensi
                                if ($attendance === null) {
                                    $bgclass = 'bg-danger';
                                }
                                @endphp
                                <td class="text-center {{ $bgclass }}">
                                {{ $statusText }}</td>
                                <td class="text-center {{ $finalBgClass }}">{{ $overtime ?: '' }}</td>
                            @endforeach
                            <td class="text-center">{{ number_format($data['total_ot'], 1, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($data['work_days'], 1, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($data['wage'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($data['ot_wage'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($data['total_wage'], 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($data['total_overtime'], 0, ',', '.') }}</td>
                            <td class="text-end fw-bold">{{ number_format($data['grand_total'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="fas fa-file-alt fa-4x text-gray-300"></i>
        </div>
        <h5 class="text-gray-600">Pilih proyek dan rentang tanggal untuk melihat laporan</h5>
    </div>
    @endif
</div>

@push('styles')
<style>
    .table th {
        white-space: nowrap;
        vertical-align: middle;
    }

    .badge {
        min-width: 60px;
        font-weight: normal;
    }

</style>
@endpush

@endsection
