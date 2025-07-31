@extends('layouts.app')

@section('title', 'Daftar Proyek')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Proyek</h5>
        <a href="{{ route('projects.create') }}" class="btn btn-primary">Tambah Proyek</a>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Proyek</th>
                    <th>Mandor</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($projects as $project)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->mandor->name }}</td>
                    <td>{{ $project->start_date->format('d/m/Y') }}</td>
                    <td>{{ $project->end_date ? $project->end_date->format('d/m/Y') : '-' }}</td>
                    <td>
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-info">Detail</a>
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
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
</div>
@endsection