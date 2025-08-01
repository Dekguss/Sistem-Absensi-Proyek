@extends('layouts.app')

@section('title', 'Tambah Proyek')

@section('content')
<div class="card">
    <div class="card-header">Tambah Proyek Baru</div>
    <div class="card-body">
        <form action="{{ route('projects.update', $project) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label for="name" class="form-label">Nama Proyek</label>
                <input type="text" class="form-control" id="name" name="name" required value="{{ $project->name }}">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ $project->description }}</textarea>
            </div>
            <div class="mb-3">
                <label for="start_date" class="form-label">Tanggal Mulai</label>
                <input type="date" class="form-control" id="start_date" name="start_date" required value="{{ $project->start_date }}">
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">Tanggal Selesai</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $project->end_date }}">
            </div>
            <div class="mb-3">
                <label for="mandor_id" class="form-label">Mandor</label>
                <select class="form-select" id="mandor_id" name="mandor_id" required>
                    <option value="">Pilih Mandor</option>
                    @foreach($mandor as $mandor)
                    <option value="{{ $mandor->id }}" {{ $mandor->id == $project->mandor_id ? 'selected' : '' }}>{{ $mandor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Pekerja</label>
                <div class="row">
                    @foreach($workers as $worker)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="worker_{{ $worker->id }}" name="workers[]" value="{{ $worker->id }}" {{ $project->workers->contains($worker->id) ? 'checked' : '' }}>
                            <label class="form-check-label" for="worker_{{ $worker->id }}">
                                {{ $worker->name }}
                                <span class="badge bg-{{ $worker->role === 'mandor' ? 'primary' : ($worker->role === 'peladen' ? 'success' : 'secondary') }}">
                                    {{ ucfirst($worker->role) }}
                                </span>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
