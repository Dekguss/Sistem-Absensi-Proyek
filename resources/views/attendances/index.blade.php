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
                        <th>Tanggal</th>
                        <th>Nama Pekerja</th>
                        <th>Status</th>
                        <th>Jam Lembur</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances as $date => $dateAttendances)
                    @foreach($dateAttendances as $attendance)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</td>
                        <td>{{ $attendance->worker->name }}</td>
                        <td>
                            @if($attendance->status === 'hadir')
                            1 Hari
                            @elseif($attendance->status === 'setengah_hari')
                            Setengah Hari
                            @elseif($attendance->status === '2_hari_kerja')
                            2 Hari 
                            @else
                            Tidak Hadir
                            @endif
                        </td>
                        <td>{{ $attendance->overtime_hours }} jam</td>
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
                    @endforeach
                </tbody>
            </table>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection
