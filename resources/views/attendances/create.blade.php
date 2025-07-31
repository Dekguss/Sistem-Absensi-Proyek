@extends('layouts.app')

@section('title', 'Tambah Absensi')

@section('content')
<div class="card">
    <div class="card-header">Tambah Absensi Baru</div>
    <div class="card-body">
        <form action="{{ route('projects.attendances.store', $project) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="date" class="form-label">Tanggal</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="mb-3">
                <label for="worker_id" class="form-label">Pekerja</label>
                <select class="form-select" id="worker_id" name="worker_id" required>
                    <option value="">Pilih Pekerja</option>
                    @foreach($workers as $worker)
                    <option value="{{ $worker->id }}">{{ $worker->name }} ({{ ucfirst($worker->role) }})</option>
                    @endforeach
                </select>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="check_in" class="form-label">Check In</label>
                    <input type="time" class="form-control" id="check_in" name="check_in" required>
                </div>
                <div class="col-md-6">
                    <label for="check_out" class="form-label">Check Out</label>
                    <input type="time" class="form-control" id="check_out" name="check_out" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Catatan</label>
                <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('projects.attendances.index', $project) }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection