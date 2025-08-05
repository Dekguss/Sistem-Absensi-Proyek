@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold mb-2">Dashboard Manajemen Absensi</h1>
        <p class="text-muted mb-0">{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</p>
    </div>
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-lg-3 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="ri-team-line text-primary fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-1">Total Pekerja</p>
                        <h3 class="mb-0">{{ $workers->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="ri-building-line text-success fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-1">Total Proyek</p>
                        <h3 class="mb-0">{{ $projects->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="ri-calendar-check-line text-warning fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-1">Hadir Hari Ini</p>
                        <h3 class="mb-0">18</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3 mb-4">
            <div class="card h-100 border shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 me-3">
                        <i class="ri-close-circle-line text-danger fs-4"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-1">Tidak Hadir</p>
                        <h3 class="mb-0">6</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('workers.index') }}" class="text-decoration-none">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-3">
                                <i class="ri-team-line text-primary fs-4"></i>
                            </div>
                            <i class="ri-arrow-right-s-line text-muted fs-4"></i>
                        </div>
                        <h5 class="card-title">Kelola Pekerja</h5>
                        <p class="card-text text-muted small">Tambah, edit, dan hapus data pekerja tukang dan mandor</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('projects.index') }}" class="text-decoration-none">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3">
                                <i class="ri-building-line text-success fs-4"></i>
                            </div>
                            <i class="ri-arrow-right-s-line text-muted fs-4"></i>
                        </div>
                        <h5 class="card-title">Kelola Proyek</h5>
                        <p class="card-text text-muted small">Manajemen proyek, mandor, dan assignment pekerja</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="#" class="text-decoration-none">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-3">
                                <i class="ri-calendar-check-line text-warning fs-4"></i>
                            </div>
                            <i class="ri-arrow-right-s-line text-muted fs-4"></i>
                        </div>
                        <h5 class="card-title">Absensi</h5>
                        <p class="card-text text-muted small">Input dan lihat absensi pekerja per proyek</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="#" class="text-decoration-none">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-info bg-opacity-10 p-3 rounded-3">
                                <i class="ri-file-text-line text-info fs-4"></i>
                            </div>
                            <i class="ri-arrow-right-s-line text-muted fs-4"></i>
                        </div>
                        <h5 class="card-title">Laporan</h5>
                        <p class="card-text text-muted small">Export laporan absensi ke Excel per proyek</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="#" class="text-decoration-none">
                <div class="card h-100 border shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-3">
                                <i class="ri-edit-line text-success fs-4"></i>
                            </div>
                            <i class="ri-arrow-right-s-line text-muted fs-4"></i>
                        </div>
                        <h5 class="card-title">Input Absensi</h5>
                        <p class="card-text text-muted small">Input absensi harian pekerja per proyek</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
            <a href="#" class="text-decoration-none">
                <div class="card h-100 border shadow-sm ">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-secondary bg-opacity-10 p-3 rounded-3">
                                <i class="ri-settings-line text-secondary fs-4"></i>
                            </div>
                            <i class="ri-arrow-right-s-line text-muted fs-4"></i>
                        </div>
                        <h5 class="card-title">Pengaturan</h5>
                        <p class="card-text text-muted small">Konfigurasi sistem dan preferensi</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
