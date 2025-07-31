@extends('layouts.app')

@section('title', 'Tambah Pekerja')

@section('content')
<div class="card">
    <div class="card-header">Tambah Pekerja Baru</div>
    <div class="card-body">
        <form action="{{ route('workers.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="tukang">Tukang</option>
                    <option value="mandor">Mandor</option>
                    <option value="peladen">Peladen</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="daily_salary" class="form-label">Gaji Harian</label>
                <input type="number" class="form-control" id="daily_salary" name="daily_salary" required>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('workers.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection