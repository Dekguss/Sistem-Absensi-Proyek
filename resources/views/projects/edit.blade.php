@extends('layouts.app')

@section('title', 'Edit Proyek')

@push('styles')
<style>
    .btn-check:checked+.btn-outline-secondary {
        background-color: #0d6efd !important;
        color: #fff !important;
        border-color: #0d6efd !important;
    }

    .btn-outline-secondary {
        transition: all 0.2s ease-in-out;
    }

</style>
@endpush

@section('content')
<div class="min-vh-100">
    <div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="mb-4">
                    <h1 class="h3 fw-bold text-dark mb-2">Edit Proyek</h1>
                    <p class="text-muted mb-0">Lengkapi form di bawah untuk mengubah proyek</p>
                </div>
                <div class="card shadow-sm border-1 rounded-3 mb-4">
                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('projects.update', $project) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row g-4">
                                <!-- Informasi Proyek (Left Column) -->
                                <div class="col-lg-6">
                                    <h2 class="h5 fw-bold text-dark mb-4 pb-2 border-bottom">Informasi Proyek</h2>

                                    <!-- Nama Proyek -->
                                    <div class="mb-4">
                                        <label for="name" class="form-label fw-medium">
                                            Nama Proyek <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control form-control-lg" id="name" name="name" placeholder="Masukkan nama proyek" required value="{{ $project->name }}" />
                                        <div class="invalid-feedback">Nama proyek wajib diisi</div>
                                    </div>

                                    <!-- Lokasi Proyek -->
                                    <div class="mb-4">
                                        <label for="location" class="form-label fw-medium">
                                            Lokasi Proyek <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" name="location" class="form-control form-control-lg" placeholder="Masukkan lokasi proyek" required value="{{ $project->location }}">
                                    </div>

                                    <!-- Date Range -->
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label fw-medium">
                                                Tanggal Mulai <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="start_date" class="form-control form-control-lg" required value="{{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '' }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label fw-medium">
                                                Tanggal Selesai
                                            </label>
                                            <input type="date" name="end_date" class="form-control form-control-lg" value="{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('Y-m-d') : '' }}">
                                        </div>
                                    </div>

                                    <!-- Deskripsi Proyek -->
                                    <div class="mb-4">
                                        <label for="description" class="form-label fw-medium">
                                            Deskripsi Proyek
                                        </label>
                                        <textarea name="description" rows="4" class="form-control form-control-lg" placeholder="Deskripsi detail proyek...">{{ $project->description }}</textarea>
                                    </div>

                                    <!-- Status Proyek -->
                                    <label for="status" class="form-label fw-medium d-block mb-3">
                                        Status Proyek <span class="text-danger">*</span>
                                    </label>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="status" id="aktif" value="aktif" autocomplete="off" required {{ $project->status == 'aktif' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-secondary btn-status w-100 py-2 d-flex align-items-center justify-content-center" for="aktif">
                                                <span>Aktif</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="status" id="ditunda" value="ditunda" autocomplete="off" required {{ $project->status == 'ditunda' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-secondary btn-status w-100 py-2 d-flex align-items-center justify-content-center" for="ditunda">
                                                <span>Ditunda</span>
                                            </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="radio" class="btn-check" name="status" id="selesai" value="selesai" autocomplete="off" required {{ $project->status == 'selesai' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-secondary btn-status w-100 py-2 d-flex align-items-center justify-content-center" for="selesai">
                                                <span>Selesai</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Assignment Tim (Right Column) -->
                                <div class="col-lg-6">
                                    <h2 class="h5 fw-bold text-dark mb-4 pb-2 border-bottom">Assignment Tim</h2>

                                    <!-- Mandor Proyek -->
                                    <div class="mb-4">
                                        <label class="form-label fw-medium mb-3">Mandor Proyek <span class="text-danger">*</span></label>
                                        <div class="vstack gap-3">
                                            @foreach ($mandor as $m)
                                            <label class="form-check p-3 border rounded-3" for="mandor{{ $m->id }}">
                                                <div class="d-flex align-items-center">
                                                    <div class="text-primary d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-size: 16px;">
                                                        <i class="ri-user-3-fill"></i>
                                                    </div>
                                                    <span class="fw-medium">{{ $m->name }}</span>
                                                    <input class="form-check-input ms-auto" type="radio" name="mandor_id" id="mandor{{ $m->id }}" value="{{ $m->id }}" onchange="document.querySelectorAll('label[for^=mandor]').forEach(el => {
                                                                el.classList.remove('border-primary', 'bg-soft-primary');
                                                            });
                                                            if(this.checked) {
                                                                this.closest('label').classList.add('border-primary', 'bg-soft-primary');
                                                            }" {{ $project->mandor_id == $m->id ? 'checked' : '' }}>
                                                </div>
                                            </label>
                                            @endforeach
                                            @if($mandor->isEmpty())
                                            <div class="text-muted text-center border-1">Tidak ada mandor tersedia</div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Pekerja Assigned -->
                                    <div class="mb-4">
                                        <label class="form-label fw-medium mb-3">Pekerja Assigned
                                            <span class="text-muted">(<span id="selected-count">0</span> dipilih)</span>
                                        </label>
                                        <div class="border rounded-3" style="max-height: 300px; overflow-y: auto;">
                                            <div class="workers-list">
                                                @forelse ($workers as $worker)
                                                <label class="form-check p-3 border-bottom border-opacity-25 hover-bg-gray-100 cursor-pointer d-block m-0">
                                                    <div class="form-check d-flex align-items-center">
                                                        @if($worker->role == 'tukang')
                                                        <input class="form-check-input me-3 worker-checkbox" type="checkbox" id="worker{{ $worker->id }}" name="workers[]" value="{{ $worker->id }}" {{ $project->workers->contains($worker->id) ? 'checked' : '' }}>
                                                        <div class="d-flex align-items-center">
                                                            <div class="text-success me-3">
                                                                <i class="ri-hammer-fill"></i>
                                                            </div>
                                                            <span class="form-check-label fw-medium">{{ $worker->name }}</span>
                                                        </div>
                                                        @elseif($worker->role == 'peladen')
                                                        <input class="form-check-input me-3 worker-checkbox" type="checkbox" id="worker{{ $worker->id }}" name="workers[]" value="{{ $worker->id }}" {{ $project->workers->contains($worker->id) ? 'checked' : '' }}>
                                                        <div class="d-flex align-items-center">
                                                            <div class="text-warning me-3">
                                                                <i class="ri-hammer-fill"></i>
                                                            </div>
                                                            <span class="form-check-label fw-medium">{{ $worker->name }}</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </label>
                                                @empty
                                                <div class="text-muted text-center p-3">Tidak ada pekerja tersedia</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Actions -->
                            <div class="d-flex gap-3 border-top pt-3 mt-4">
                                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary flex-grow-1 py-2">
                                    Batal
                                </a>
                                <button type="submit" class="btn btn-primary flex-grow-1 py-2">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('input[name="workers[]"]');
        const selectedCount = document.getElementById('selected-count');

        function updateSelectedCount() {
            const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
            selectedCount.textContent = checkedCount;
        }

        // Add event listener to all checkboxes
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCount);
        });

        // Initial count
        updateSelectedCount();
    });

</script>
@endpush
@endsection
