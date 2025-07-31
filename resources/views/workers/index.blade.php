@extends('layouts.app')

@section('title', 'Daftar Pekerja')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Pekerja</h5>
        <a href="{{ route('workers.create') }}" class="btn btn-primary">Tambah Pekerja</a>
    </div>
    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Role</th>
                    <th>Gaji Harian</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($workers as $worker)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $worker->name }}</td>
                    <td>{{ ucfirst($worker->role) }}</td>
                    <td>Rp {{ number_format($worker->daily_salary, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('workers.edit', $worker) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('workers.destroy', $worker) }}" method="POST" class="d-inline">
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