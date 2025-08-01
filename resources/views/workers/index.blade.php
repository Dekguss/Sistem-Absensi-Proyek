@extends('layouts.app')

@section('title', 'Daftar Pekerja')

@push('styles')
<style>
    .card {
        border-radius: 10px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        border: none;
    }

    .card-header {
        background-color: #fff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        border-top: none;
        border-bottom: 1px solid #e3e6f0;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6e707e;
    }

    .table tbody tr {
        transition: all 0.2s;
    }

    .table tbody tr:hover {
        background-color: #f8f9fc;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
        border-radius: 5px;
    }

    .badge-role {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 600;
        border-radius: 50rem;
    }

    .btn-add {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }

</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pekerja</h1>
        <a href="{{ route('workers.create') }}" class="btn btn-primary btn-add">
            <i class="fas fa-plus"></i> Tambah Pekerja
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Role</th>
                            <th>Gaji Harian</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($workers as $worker)
                        <tr>
                            <td class="align-middle">{{ $loop->iteration }}</td>
                            <td class="align-middle font-weight-bold">{{ $worker->name }}</td>
                            <td class="align-middle">
                                <span class="badge badge-role" style="background-color: {{ $worker->role === 'mandor' ? '#4e73df' : '#1cc88a' }}; color: white;">
                                    {{ ucfirst($worker->role) }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <span class="text-primary font-weight-bold">
                                    Rp {{ number_format($worker->daily_salary, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="action-buttons">
                                    <a href="{{ route('workers.edit', $worker) }}" class="btn btn-sm btn-warning" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('workers.destroy', $worker) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus data pekerja ini?')" data-toggle="tooltip" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-users fa-2x mb-2"></i>
                                    <p class="mb-0">Belum ada data pekerja</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
