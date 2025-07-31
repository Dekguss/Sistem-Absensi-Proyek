@extends('layouts.app')

@section('title', 'Laporan Absensi: ' . $project->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Laporan Absensi Proyek: {{ $project->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('projects.attendances.export', $project) }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-success">Export ke Excel</button>
                </div>
            </div>
        </form>

        @if($attendances->isEmpty())
        <div class="alert alert-info">Belum ada data absensi</div>
        @else
        @foreach($attendances as $date => $dateAttendances)
        <div class="mb-4">
            <h6>Tanggal: {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h6>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Nama Pekerja</th>
                        <th>Role</th>
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Jam Lembur</th>
                        <th>Hitung 2 Hari</th>
                        <th>Gaji Harian</th>
                        <th>Total Gaji</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dateAttendances as $attendance)
                    @php
                        $dailySalary = $attendance->worker->daily_salary;
                        $totalSalary = $dailySalary;
                        
                        if ($attendance->count_as_two_days) {
                            $totalSalary = $dailySalary * 2;
                        } elseif ($attendance->overtime_hours > 0) {
                            $overtimePay = ($dailySalary / 8) * $attendance->overtime_hours;
                            $totalSalary += $overtimePay;
                        }
                    @endphp
                    <tr>
                        <td>{{ $attendance->worker->name }}</td>
                        <td>{{ ucfirst($attendance->worker->role) }}</td>
                        <td>{{ $attendance->check_in }}</td>
                        <td>{{ $attendance->check_out }}</td>
                        <td>{{ $attendance->overtime_hours }} jam</td>
                        <td>{{ $attendance->count_as_two_days ? 'Ya' : 'Tidak' }}</td>
                        <td>Rp {{ number_format($dailySalary, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($totalSalary, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection