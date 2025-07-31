@extends('layouts.app')

@section('title', 'Update Pekerja')

@section('content')
<div class="card">
    <div class="card-header">Update Pekerja</div>
    <div class="card-body">
        <form action="{{ route('workers.update', $worker) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="form-label">Nama</label>
                <input type="text" class="form-control" id="name" name="name" required value="{{ $worker->name }}">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="tukang" {{ $worker->role == 'tukang' ? 'selected' : '' }}>Tukang</option>
                    <option value="mandor" {{ $worker->role == 'mandor' ? 'selected' : '' }}>Mandor</option>
                    <option value="peladen" {{ $worker->role == 'peladen' ? 'selected' : '' }}>Peladen</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="daily_salary" class="form-label">Gaji Harian</label>
                <input type="number" class="form-control" id="daily_salary" name="daily_salary" required value="{{ $worker->daily_salary }}">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('workers.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
