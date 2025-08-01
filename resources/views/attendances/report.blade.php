@extends('layouts.app')

@section('title', 'Laporan Absensi: ' . $project->name)

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Laporan Absensi Proyek: {{ $project->name }}</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('projects.attendances.report', $project) }}" method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Tanggal Mulai</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ request('start_date', $startDate->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">Tanggal Selesai</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ request('end_date', $endDate->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('projects.attendances.export', array_merge(request()->all(), ['project' => $project->id])) }}" 
                       class="btn btn-success ms-2">Export ke Excel</a>
                </div>
            </div>
        </form>

        @if($attendances->isEmpty())
            <div class="alert alert-info">Tidak ada data absensi untuk rentang tanggal yang dipilih</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>NAMA</th>
                            <th>JABATAN</th>
                            @php
                                $currentDate = clone $startDate;
                                $endOfWeek = clone $endDate;
                            @endphp
                            @while($currentDate <= $endOfWeek)
                                <th>{{ $currentDate->format('D') }}<br>{{ $currentDate->format('d/m') }}</th>
                                @php $currentDate->addDay() @endphp
                            @endwhile
                            <th>TOTAL OT</th>
                            <th>HARI KERJA</th>
                            <th>GAJI</th>
                            <th>LEMBUR</th>
                            <th>TOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($attendances as $workerId => $workerAttendances)
                            @php
                                $worker = $workerAttendances->first()->worker;
                                $totalOt = $workerAttendances->sum('overtime_hours');
                                $workDays = $workerAttendances->where('status', '!=', 'tidak_hadir')->count();
                                $totalWage = 0;
                                $overtimePay = $totalOt * 20000; // Assuming 20,000 per hour of overtime
                                
                                foreach ($workerAttendances as $att) {
                                    if ($att->status === 'hadir') {
                                        $totalWage += $worker->daily_salary;
                                    } elseif ($att->status === 'setengah_hari') {
                                        $totalWage += $worker->daily_salary / 2;
                                    } elseif ($att->status === '2_hari_kerja') {
                                        $totalWage += $worker->daily_salary * 2;
                                    }
                                }
                                
                                $total = $totalWage + $overtimePay;
                            @endphp
                            <tr>
                                <td>{{ $worker->name }}</td>
                                <td>{{ strtoupper($worker->role) }}</td>
                                
                                @php
                                    $currentDate = clone $startDate;
                                @endphp
                                
                                @while($currentDate <= $endDate)
                                    @php
                                        $attendanceForDay = $workerAttendances->first(function($att) use ($currentDate) {
                                            return $att->date->format('Y-m-d') === $currentDate->format('Y-m-d');
                                        });
                                        
                                        $status = '';
                                        if ($attendanceForDay) {
                                            if ($attendanceForDay->status === 'tidak_hadir') {
                                                $status = '0';
                                            } elseif ($attendanceForDay->status === '2_hari_kerja') {
                                                $status = '2';
                                            } else {
                                                $status = '1';
                                            }
                                        }
                                        
                                        $currentDate->addDay();
                                    @endphp
                                    <td class="text-center">{{ $status }}</td>
                                @endwhile
                                
                                <td class="text-center">{{ $totalOt }}</td>
                                <td class="text-center">{{ $workDays }}</td>
                                <td class="text-end">{{ number_format($totalWage, 0, ',', '.') }}</td>
                                <td class="text-end">{{ number_format($overtimePay, 0, ',', '.') }}</td>
                                <td class="text-end fw-bold">{{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection