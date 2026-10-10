@extends('layouts.admin')

@section('title', 'Master Jasa Servis')

@section('content')

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-title">
            <h2>⚙️ Master Jasa & Layanan Servis</h2>
            <p>Atur katalog tindakan servis kendaraan, durasi estimasi pengerjaan montir, dan tarif ongkos kerja.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.services.create') }}" class="btn btn-primary">
                ➕ Tambah Layanan Servis Baru
            </a>
        </div>
    </div>

    <!-- SERVICES TABLE -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Layanan Servis</th>
                        <th>Tarif Ongkos Jasa</th>
                        <th>Estimasi Waktu</th>
                        <th>Deskripsi Tindakan Servis</th>
                        <th>Penggunaan</th>
                        <th style="text-align: right; width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $service)
                        <tr>
                            <td>
                                <strong style="color: var(--text-main); font-size: 1.02rem;">{{ $service->nama_layanan }}</strong>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--success); font-size: 1.05rem;">
                                    Rp {{ number_format($service->biaya, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-warning">
                                    ⏱️ {{ $service->durasi_estimasi }} Menit
                                </span>
                            </td>
                            <td style="color: var(--text-muted); font-size: 0.9rem; max-width: 320px;">
                                {{ $service->deskripsi ?? '-' }}
                            </td>
                            <td>
                                <span class="badge badge-category">
                                    {{ $service->queues_count }}x Antrean
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                    <a 
                                        href="{{ route('admin.services.edit', $service) }}" 
                                        class="btn btn-secondary btn-sm"
                                        title="Ubah data jasa">
                                        ✏️ Edit
                                    </a>
                                    <form 
                                        method="POST" 
                                        action="{{ route('admin.services.destroy', $service) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus layanan {{ addslashes($service->nama_layanan) }}?');"
                                        style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="btn btn-danger btn-sm"
                                            title="Hapus layanan">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem 1rem; color: var(--text-muted);">
                                Belum ada jenis layanan servis yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
