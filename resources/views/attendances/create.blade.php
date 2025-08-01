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
                <h5>Daftar Pekerja</h5>
                @php
                    $groupedWorkers = $workers->groupBy('role');
                @endphp

                @foreach($groupedWorkers as $role => $workersGroup)
                    <div class="mb-4">
                        <h6 class="mt-3">{{ ucfirst($role) }}</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Lembur (jam)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($workersGroup as $index => $worker)
                                        <tr>
                                            <td>
                                                <input type="hidden" name="workers[{{ $index }}][id]" value="{{ $worker->id }}">
                                                {{ $worker->name }}
                                            </td>
                                            <td>
                                                <select name="workers[{{ $index }}][status]" class="form-select" required>
                                                    <option value="hadir">Hadir (1 hari)</option>
                                                    <option value="setengah_hari">Setengah Hari</option>
                                                    <option value="tidak_hadir">Tidak Hadir</option>
                                                    <option value="2_hari_kerja">2 Hari Kerja</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="number" 
                                                       name="workers[{{ $index }}][overtime_hours]" 
                                                       class="form-control" 
                                                       min="0" 
                                                       value="0"
                                                       style="width: 80px;">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('projects.attendances.index', $project) }}" class="btn btn-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">Simpan Semua Absensi</button>
            </div>
        </form>
    </div>
</div>

@push('styles')
<style>
    .table th, .table td {
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
    // Set default date to today
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('date').value = today;
    });
</script>
@endpush
@endsection
