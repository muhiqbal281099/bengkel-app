@extends('layouts.kasir')

@section('title', 'Riwayat Transaksi Kasir')

@section('styles')
<style>
    .filter-card {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.2rem 1.5rem;
        margin-bottom: 1.8rem;
        box-shadow: var(--shadow-sm);
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 2;
        min-width: 220px;
    }

    .select-box {
        flex: 1;
        min-width: 150px;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 10px;
        color: var(--text-main);
        font-size: 0.92rem;
        outline: none;
    }

    .form-control:focus {
        border-color: var(--primary-blue);
    }

    .omzet-banner {
        background: var(--primary-light);
        border: 1px solid var(--border-color);
        padding: 1rem 1.5rem;
        border-radius: 14px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
</style>
@endsection

@section('content')

    <!-- HEADER -->
    <div class="page-header">
        <div class="page-title">
            <h2>📑 Riwayat & Laporan Transaksi Kasir</h2>
            <p>Daftar seluruh kuitansi transaksi yang telah diproses di meja kasir bengkel.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('kasir.pos') }}" class="btn btn-primary">
                💳 Buka Kasir Baru
            </a>
        </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-card">
        <form method="GET" action="{{ route('kasir.transactions.index') }}" class="filter-form">
            <div class="select-box">
                <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-dim); display: block; margin-bottom: 0.2rem;">Pilih Tanggal</label>
                <input type="date" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}" class="form-control" onchange="this.form.submit()">
            </div>

            <div class="search-box">
                <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-dim); display: block; margin-bottom: 0.2rem;">Pencarian Kendaraan</label>
                <input type="text" name="q" placeholder="Cari No. Polisi atau No. Antrean..." value="{{ request('q') }}" class="form-control">
            </div>

            <div class="select-box">
                <label style="font-size: 0.8rem; font-weight: 700; color: var(--text-dim); display: block; margin-bottom: 0.2rem;">Metode Bayar</label>
                <select name="metode" class="form-control" onchange="this.form.submit()">
                    <option value="">Semua Metode</option>
                    <option value="cash" {{ request('metode') === 'cash' ? 'selected' : '' }}>Tunai (Cash)</option>
                    <option value="transfer" {{ request('metode') === 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="qris" {{ request('metode') === 'qris' ? 'selected' : '' }}>QRIS</option>
                </select>
            </div>

            <div style="align-self: flex-end;">
                <button type="submit" class="btn btn-secondary">Filter</button>
                @if(request()->hasAny(['tanggal', 'q', 'metode']))
                    <a href="{{ route('kasir.transactions.index') }}" class="btn btn-sm" style="color: var(--text-dim); text-decoration: underline;">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- BANNER TOTAL OMZET -->
    <div class="omzet-banner">
        <div>
            <span style="font-size: 0.88rem; color: var(--text-muted); font-weight: 600;">Total Omzet Terfilter:</span>
            <div style="font-size: 1.6rem; font-weight: 900; color: var(--primary);">
                Rp {{ number_format($totalOmzet, 0, ',', '.') }}
            </div>
        </div>
        <div>
            <span class="badge badge-safe" style="font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                {{ $transactions->total() }} Transaksi Tercatat
            </span>
        </div>
    </div>

    <!-- DATA TABLE -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Nota</th>
                        <th>Waktu Transaksi</th>
                        <th>No. Antrean</th>
                        <th>No. Polisi</th>
                        <th>Pelanggan</th>
                        <th>Layanan Servis</th>
                        <th>Metode</th>
                        <th>Total Tagihan</th>
                        <th style="text-align: right; width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td>
                                <code>NOTA-{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</code>
                            </td>
                            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <strong style="color: var(--primary);">{{ $t->queue->nomor_antrean ?? '-' }}</strong>
                            </td>
                            <td>
                                <span style="font-weight: 800; background: var(--primary-light); color: var(--primary-blue); padding: 0.2rem 0.6rem; border-radius: 6px;">
                                    {{ $t->queue->kendaraan->no_polisi ?? '-' }}
                                </span>
                            </td>
                            <td>{{ $t->queue->kendaraan->user->name ?? '-' }}</td>
                            <td>{{ $t->queue->service->nama_layanan ?? '-' }}</td>
                            <td>
                                <span class="badge badge-category" style="text-transform: uppercase;">
                                    {{ $t->metode_pembayaran }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--success); font-size: 1rem;">
                                    Rp {{ number_format($t->total_pembayaran, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('kasir.transactions.receipt', $t) }}" target="_blank" class="btn btn-secondary btn-sm">
                                    🖨️ Cetak Nota
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                                Tidak ada transaksi yang sesuai dengan filter tanggal atau kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION -->
    @if($transactions->hasPages())
        <div style="margin-top: 1.5rem;">
            {{ $transactions->links() }}
        </div>
    @endif

@endsection
