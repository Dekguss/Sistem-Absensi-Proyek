@extends('layouts.app')

@section('title', 'Tambah Pekerja')

@push('styles')
<style>
    .card {
        border: 1px solid #dee2e6;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
    }

</style>
@endpush

@section('content')
<div class="min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-1">
                    <div class="card-body p-4 p-md-5">
                        <div class="mb-4">
                            <h1 class="h3 fw-bold text-dark mb-2">Tambah Pekerja Baru</h1>
                            <p class="text-muted mb-0">Lengkapi form di bawah untuk menambahkan pekerja baru</p>
                        </div>
                        <form class="needs-validation" novalidate action="{{ route('workers.store') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="name" class="form-label fw-medium">
                                    Nama <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Masukkan nama pekerja" required />
                                <div class="invalid-feedback">Nama wajib diisi</div>
                            </div>

                            <div class="mb-4">
                                <label class="role fw-medium d-block mb-3">
                                    Role / Jabatan <span class="text-danger">*</span>
                                </label>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="role" id="mandor" value="mandor" autocomplete="off" required>
                                        <label class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center" for="mandor">
                                            <i class="ri-user-3-line me-2"></i>
                                            <span>Mandor</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="role" id="tukang" value="tukang" autocomplete="off" required>
                                        <label class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center" for="tukang">
                                            <i class="ri-hammer-line me-2"></i>
                                            <span>Tukang</span>
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="radio" class="btn-check" name="role" id="peladen" value="peladen" autocomplete="off" required>
                                        <label class="btn btn-outline-primary w-100 py-3 d-flex align-items-center justify-content-center" for="peladen">
                                            <i class="ri-shield-line me-2"></i>
                                            <span>Peladen</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label for="daily_salary" class="form-label fw-medium">
                                    Gaji Harian (Rupiah) <span class="text-danger">*</span>
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-white">Rp</span>
                                    <input type="number" class="form-control" id="daily_salary" name="daily_salary" placeholder="0" required />
                                    <div class="invalid-feedback">Gaji harian wajib diisi</div>
                                </div>
                            </div>

                            <div class="d-grid gap-3 d-md-flex pt-3">
                                <a href="{{ route('workers.index') }}" class="btn btn-outline-secondary btn-lg flex-grow-1">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg flex-grow-1">
                                    Tambah Pekerja
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation
    (function() {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()

</script>
@endsection
