@extends('layouts.app')

@section('title', 'Daftar Pekerja')

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
    }

    .table {
        margin-bottom: 0;
    }

</style>
@endpush

@section('content')
<div>
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Daftar Pekerja</h1>
        <a href="{{ route('workers.create') }}" class="btn btn-primary btn-add">
            <i class="ri-add-line"></i>
            <span class="d-none d-sm-inline">Tambah Pekerja</span>
        </a>
    </div>

    <div class="bg-white rounded shadow-sm border overflow-hidden p-0">
        <div class="card-header bg-white border-bottom p-3">
            <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                <div class="text-left text-md-start">
                    <h6 class="mb-1">Daftar Pekerja</h6>
                    <p class="text-muted mb-0">Total {{ $workers->count() }} pekerja terdaftar</p>
                </div>
                <div class="d-flex flex-wrap justify-content-md-start gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-circle-fill text-primary small d-block"></i>
                        <span class="text-muted small">Mandor: {{ $workers->where('role', 'mandor')->count() }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-circle-fill text-success small d-block"></i>
                        <span class="text-muted small">Tukang: {{ $workers->where('role', 'tukang')->count() }}</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="ri-circle-fill text-warning small d-block"></i>
                        <span class="text-muted small">Peladen: {{ $workers->where('role', 'peladen')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover w-100">
                    <thead class="table-light">
                        <tr>
                            <th scope="col" class="px-4 py-3">No</th>
                            <th scope="col" class="px-4 py-3">Nama</th>
                            <th scope="col" class="px-4 py-3">Role</th>
                            <th scope="col" class="px-4 py-3 text-nowrap">Gaji Harian</th>
                            <th scope="col" class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workers as $worker)
                        <tr>
                            <td class="px-4 py-3 align-middle">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 align-middle">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #e7f1ff;">
                                        <span class="text-primary fw-medium small">{{ $worker->name[0] }}</span>
                                    </div>
                                    <span class="text-dark fw-medium">{{ $worker->name }}</span>
                                </div>
                            </td>
                            @php
                                $roleConfig = [
                                    'mandor' => [
                                        'icon' => 'ri-user-3-fill',
                                        'color' => '#4e73df',
                                        'bg' => '#e8f0fe'
                                    ],
                                    'tukang' => [
                                        'icon' => 'ri-hammer-fill',
                                        'color' => '#1cc88a',
                                        'bg' => '#e6f7f0'
                                    ],
                                    'peladen' => [
                                        'icon' => 'ri-shield-fill',
                                        'color' => '#f6c23e',
                                        'bg' => '#fef9e7'
                                    ]
                                ];
                                $role = $worker->role;
                                $config = $roleConfig[$role] ?? $roleConfig['tukang'];
                            @endphp

                            <td class="px-4 py-3 align-middle">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="{{ $config['icon'] }}" style="color: {{ $config['color'] }}; font-size: 1rem;"></i>
                                    <span class="badge" style="color: {{ $config['color'] }}; font-weight: 500; padding: 0.25rem 0.5rem;">
                                        {{ ucfirst($role) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <span class="fw-semibold text-nowrap">
                                    Rp {{ number_format($worker->daily_salary, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 align-middle">
                                <div class="d-flex gap-2">
                                    <a href="{{ route('workers.edit', $worker) }}" class="btn btn-sm btn-outline-primary border-1 rounded-3" data-bs-toggle="tooltip" title="Edit">
                                        <i class="ri-edit-line"></i>
                                        <span class="d-none d-sm-inline ms-1">Edit</span>
                                    </a>
                                    <form action="{{ route('workers.destroy', $worker) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-1 rounded-3" onclick="return confirm('Yakin ingin menghapus data pekerja ini?')" data-bs-toggle="tooltip" title="Hapus">
                                            <i class="ri-delete-bin-line"></i>
                                            <span class="d-none d-sm-inline ms-1">Hapus</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="ri-user-line fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada data pekerja</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(method_exists($workers, 'hasPages') && $workers->hasPages())
        <div class="mt-3">
            {{ $workers->links() }}
        </div>
        @endif
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    // Enable tooltips
    $(function() {
        $('[data-toggle="tooltip"]').tooltip();
    });

</script>
@endpush
