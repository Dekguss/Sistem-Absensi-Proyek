@extends('layouts.app')

@section('title', 'Daftar Proyek')

@section('content')
<div>
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Kelola Proyek</h1>
        <a href="{{ route('projects.create') }}" class="btn btn-success btn-add">
            <i class="ri-add-line"></i>
            <span class="d-none d-sm-inline">Tambah Proyek</span>
        </a>
    </div>


    <div class="bg-white rounded shadow-sm border overflow-hidden p-0">
        <!-- Project List Header -->
        <div class="card-header bg-white border-bottom p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div class="text-left text-md-start">
                    <h6 class="mb-1">Daftar Proyek</h6>
                    <p class="text-muted mb-0">Total {{ $projects->count() }} proyek terdaftar</p>
                </div>
                <div class="d-flex flex-wrap justify-content-md-start gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-circle-fill text-success small d-block"></i>
                        <span class="text-muted small">Aktif: {{ $projects->where('status', 'aktif')->count() }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-circle-fill text-secondary small d-block"></i>
                        <span class="text-muted small">Selesai: {{ $projects->where('status', 'selesai')->count() }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-circle-fill text-warning small d-block"></i>
                        <span class="text-muted small">Ditunda: {{ $projects->where('status', 'ditunda')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Cards Grid -->
        <div class="row g-3 p-3">
            @if($projects->count() > 0)
            @foreach($projects as $project)
            <!-- Project Card -->
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-1 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h3 class="h5 fw-bold mb-0">{{ $project->name }}</h3>
                            @if($project->status == 'aktif')
                            <span class="badge bg-success bg-opacity-10 text-success fw-medium">Aktif</span>
                            @elseif($project->status == 'ditunda')
                            <span class="badge bg-warning bg-opacity-10 text-warning fw-medium">Ditunda</span>
                            @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary fw-medium">Selesai</span>
                            @endif
                        </div>

                        <p class="text-muted small mb-4">{{ $project->description }}</p>

                        <ul class="list-unstyled mb-4">
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ri-map-pin-line text-muted"></i>
                                <span class="small">{{ $project->location }}</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ri-user-line text-muted"></i>
                                <span class="small">Mandor: {{ $project->mandor->name }}</span>
                            </li>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="ri-group-line text-muted"></i>
                                <span class="small">{{ $project->workers->count() }} Pekerja</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="ri-calendar-line text-muted"></i>
                                <span class="small">
                                    {{ $project->start_date ? $project->start_date->format('d M Y') : '' }} -
                                    {{ $project->end_date ? $project->end_date->format('d M Y') : '' }}
                                </span>
                            </li>
                        </ul>

                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#projectDetailModal{{ $project->id }}">
                                <i class="ri-eye-line"></i>
                                Detail
                            </button>
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-warning btn-sm px-3">
                                <i class="ri-edit-line"></i>
                            </a>
                            <form action="{{ route('projects.destroy', $project) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Yakin ingin menghapus data proyek ini?')">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Project Detail Modal -->
            <div class="modal fade" id="projectDetailModal{{ $project->id }}" tabindex="-1" aria-labelledby="projectDetailModalLabel{{ $project->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="projectDetailModalLabel{{ $project->id }}">
                                <i class="fas fa-info-circle me-2"></i>Detail Proyek
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-primary">Informasi Proyek</h6>
                                    <div class="mb-3">
                                        <p class="mb-1 text-muted">Nama Proyek</p>
                                        <p class="fw-semibold">{{ $project->name }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1 text-muted">Lokasi</p>
                                        <p class="fw-semibold">{{ $project->location }}</p>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1 text-muted">Tanggal Mulai</p>
                                        <p class="fw-semibold">{{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}</p>
                                    </div>
                                    @if($project->end_date)
                                    <div class="mb-3">
                                        <p class="mb-1 text-muted">Tanggal Selesai</p>
                                        <p class="fw-semibold">{{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</p>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <p class="mb-1 text-muted">Status</p>
                                        <span class="badge bg-{{ $project->status === 'selesai' ? 'secondary' : ($project->status === 'aktif' ? 'success' : 'warning') }}">
                                            {{ ucfirst($project->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold text-primary">Deskripsi Proyek</h6>
                                <div class="border rounded p-3 bg-light">
                                    {{ $project->description ?? 'Tidak ada deskripsi' }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-primary mb-0">Tim Pekerja</h6>
                                    <span class="badge bg-primary rounded-pill">{{ $project->workers->count() + 1 }} Orang</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>No</th>
                                                <th>Nama</th>
                                                <th>Posisi</th>
                                                <th>Gaji</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>{{ $project->mandor->name }}</td>
                                                <td>
                                                    <i class="ri-user-3-line text-primary"></i>
                                                    <span>Mandor</span>
                                                </td>
                                                <td>
                                                    <span>Rp. </span>
                                                    {{ number_format($project->mandor->daily_salary, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @forelse($project->workers as $worker)
                                            <tr>
                                                <td>{{ $loop->iteration + 1 }}</td>
                                                <td>{{ $worker->name }}</td>
                                                <td>
                                                    @if($worker->role == 'tukang')
                                                    <i class="ri-hammer-line text-success"></i>
                                                    <span>Tukang</span>
                                                    @elseif($worker->role == 'peladen')
                                                    <i class="ri-hammer-line text-warning"></i>
                                                    <span>Peladen</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span>Rp. </span>
                                                    {{ number_format($worker->daily_salary, 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">Belum ada pekerja yang ditugaskan</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="col-12">
                <div class="text-center py-4">
                    <div class="text-muted">
                        <i class="ri-inbox-line fa-2x mb-2"></i>
                        <p class="mb-0">Belum ada data proyek</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
