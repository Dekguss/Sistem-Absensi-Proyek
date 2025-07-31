@extends('layouts.app')

@section('title', 'Absensi Proyek: ' . $project->name)

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Absensi Proyek: {{ $project->name }}</h5>
        <div>
            <a href="{{ route('projects.attendances.create', $project) }}" class="btn btn-primary">Tambah Absensi</a>
            <a href="{{ route('projects.attendances.report', $project) }}" class="btn btn-success">Laporan</a>
        </div>
    </div>
    <div class="card-body">
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
                        <th>Check In</th>
                        <th>Check Out</th>
                        <th>Jam Lembur</th>
                        <th>Hitung 2 Hari</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dateAttendances as $attendance)
                    <tr>
                        <td>{{ $attendance->worker->name }}</td>
                        <td>{{ $attendance->check_in }}</td>
                        <td>{{ $attendance->check_out }}</td>
                        <td>{{ $attendance->overtime_hours }} jam</td>
                        <td>{{ $attendance->count_as_two_days ? 'Ya' : 'Tidak' }}</td>
                        <td>
                            <a href="{{ route('projects.attendances.edit', [$project, $attendance]) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('projects.attendances.destroy', [$project, $attendance]) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                            </form>
                        </td>
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