@extends('layouts.app')

@section('title', 'Detail Proyek')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Detail Proyek: {{ $project->name }}</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Deskripsi:</strong> {{ $project->description ?? '-' }}</p>
                <p><strong>Tanggal Mulai:</strong> {{ $project->start_date->format('d/m/Y') }}</p>
                <p><strong>Tanggal Selesai:</strong> {{ $project->end_date ? $project->end_date->format('d/m/Y') : '-' }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Mandor:</strong> {{ $project->mandor->name }}</p>
                <p><strong>Jumlah Pekerja:</strong> {{ $project->workers->count() }}</p>
            </div>
        </div>

        <div class="mb-4">
            <h6>Daftar Pekerja:</h6>
            <ul>
                @foreach($project->workers as $worker)
                <li>{{ $worker->name }} ({{ ucfirst($worker->role) }}) - Rp {{ number_format($worker->daily_salary, 0, ',', '.') }}/hari</li>
                @endforeach
            </ul>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('projects.attendances.index', $project) }}" class="btn btn-primary">Lihat Absensi</a>
            <div>
                <a href="{{ route('projects.edit', $project) }}" class="btn btn-warning">Edit</a>
                <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Yakin ingin menghapus?')">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 