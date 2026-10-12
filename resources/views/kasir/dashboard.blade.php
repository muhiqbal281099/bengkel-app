@extends('layouts.kasir')

@section('title', 'Dashboard Kasir')

@section('styles')
<style>
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow-md);
        display: flex;
        align-items: center;
        gap: 1.2rem;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-icon.blue { background: #dbeafe; color: #1d4ed8; }
    [data-theme="dark"] .stat-icon.blue { background: rgba(37, 99, 235, 0.25); color: #93c5fd; }

    .stat-icon.green { background: #dcfce7; color: #15803d; }
    [data-theme="dark"] .stat-icon.green { background: rgba(22, 163, 74, 0.25); color: #86efac; }

    .stat-icon.amber { background: #fef3c7; color: #b45309; }
    [data-theme="dark"] .stat-icon.amber { background: rgba(217, 119, 6, 0.25); color: #fde68a; }

    .stat-info {
        flex: 1;
    }

    .stat-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-dim);
        letter-spacing: 0.5px;
    }

    .stat-val {
        font-size: 1.85rem;
        font-weight: 900;
        color: var(--text-main);
        line-height: 1.1;
        margin-top: 0.2rem;
    }

    .stat-sub {
        font-size: 0.82rem;
        color: var(--text-muted);
        margin-top: 0.25rem;
    }
</style>
@endsection

@section('content')

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-title">
            <h2>Loket Pembayaran & Kasir Bengkel</h2>
            <p>Kelola pembayaran tagihan jasa servis dan suku cadang serta cetak nota kuitansi pelanggan.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('kasir.pos') }}" class="btn btn-primary" style="padding: 0.75rem 1.6rem; font-size: 1rem;">
                💳 Buka Kasir / Transaksi Baru
            </a>
        </div>
    </div>

    <!-- METRICS -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon green">💰</div>
            <div class="stat-info">
                <div class="stat-title">Omzet Hari Ini</div>
                <div class="stat-val" style="color: var(--success);">
                    Rp {{ number_format($totalIncomeToday, 0, ',', '.') }}
                </div>
                <div class="stat-sub">{{ $totalCountToday }} pembayaran berhasil lunas</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon blue">🧾</div>
            <div class="stat-info">
                <div class="stat-title">Transaksi Berhasil</div>
                <div class="stat-val">{{ $totalCountToday }}</div>
                <div class="stat-sub">Kuitansi dikeluarkan hari ini</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon amber">⏳</div>
            <div class="stat-info">
                <div class="stat-title">Menunggu Pembayaran</div>
                <div class="stat-val" style="color: {{ $unpaidQueues->count() > 0 ? 'var(--warning)' : 'var(--text-main)' }};">
                    {{ $unpaidQueues->count() }}
                </div>
                <div class="stat-sub">Kendaraan aktif di bengkel</div>
            </div>
        </div>
    </div>

    <!-- DAFTAR ANTREAN SIAP BAYAR -->
    <div class="card">
        <div class="card-header">
            <h3>🚗 Antrean Kendaraan yang Belum Diproses Kasir</h3>
            <span class="badge badge-warning">{{ $unpaidQueues->count() }} Menunggu</span>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Antrean</th>
                        <th>Nomor Polisi</th>
                        <th>Kendaraan & Pelanggan</th>
                        <th>Layanan Servis</th>
                        <th>Tarif Jasa</th>
                        <th>Mekanik</th>
                        <th>Status Servis</th>
                        <th style="text-align: right; width: 170px;">Aksi Kasir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($unpaidQueues as $q)
                        <tr>
                            <td>
                                <strong style="font-size: 1.15rem; color: var(--primary);">{{ $q->nomor_antrean }}</strong>
                            </td>
                            <td>
                                <span style="font-weight: 800; background: var(--primary-light); color: var(--primary-blue); padding: 0.2rem 0.6rem; border-radius: 6px; font-size: 0.95rem;">
                                    {{ $q->kendaraan->no_polisi ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--text-main);">{{ $q->kendaraan->merk ?? '' }} {{ $q->kendaraan->model ?? '' }}</strong>
                                <div style="font-size: 0.82rem; color: var(--text-muted);">
                                    👤 {{ $q->kendaraan->user->name ?? 'Pelanggan Umum' }}
                                </div>
                            </td>
                            <td>
                                <span style="font-weight: 600;">{{ $q->service->nama_layanan ?? 'Servis Umum' }}</span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--success);">
                                    Rp {{ number_format($q->service->biaya ?? 0, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>{{ $q->montor->name ?? '-' }}</td>
                            <td>
                                @if($q->status === 'completed')
                                    <span class="badge badge-safe" style="background:#dcfce7; color:#15803d; border:1px solid #86efac;">✅ Siap Bayar</span>
                                @elseif($q->status === 'in_service')
                                    <span class="badge badge-safe">Dikerjakan</span>
                                @elseif($q->status === 'checking')
                                    <span class="badge badge-warning">Pengecekan</span>
                                @else
                                    <span class="badge badge-warning">Menunggu</span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('kasir.pos', ['queue_id' => $q->id]) }}" class="btn btn-primary btn-sm">
                                    💳 Proses Bayar →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                ✅ Seluruh antrean hari ini telah lunas dibayar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- TRANSAKSI TERAKHIR HARI INI -->
    <div class="card">
        <div class="card-header">
            <h3>📑 Transaksi Selesai Hari Ini</h3>
            <a href="{{ route('kasir.transactions.index') }}" class="btn btn-secondary btn-sm">
                Lihat Semua Riwayat →
            </a>
        </div>

        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Nota</th>
                        <th>Waktu</th>
                        <th>Nomor Polisi</th>
                        <th>Pemilik Kendaraan</th>
                        <th>Layanan Servis</th>
                        <th>Metode</th>
                        <th>Total Pembayaran</th>
                        <th style="text-align: right; width: 140px;">Cetak Nota</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTransactions as $trans)
                        <tr>
                            <td>
                                <code>NOTA-{{ str_pad($trans->id, 5, '0', STR_PAD_LEFT) }}</code>
                            </td>
                            <td style="font-size: 0.88rem; color: var(--text-muted);">
                                {{ $trans->created_at->format('H:i') }} WIB
                            </td>
                            <td>
                                <strong style="color: var(--text-main);">
                                    {{ $trans->queue->kendaraan->no_polisi ?? '-' }}
                                </strong>
                            </td>
                            <td>{{ $trans->queue->kendaraan->user->name ?? '-' }}</td>
                            <td>{{ $trans->queue->service->nama_layanan ?? '-' }}</td>
                            <td>
                                <span class="badge badge-category" style="text-transform: uppercase;">
                                    {{ $trans->metode_pembayaran }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--success); font-size: 1rem;">
                                    Rp {{ number_format($trans->total_pembayaran, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td style="text-align: right;">
                                <a href="{{ route('kasir.transactions.receipt', $trans) }}" target="_blank" class="btn btn-secondary btn-sm">
                                    🖨️ Cetak Nota
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                                Belum ada transaksi yang diselesaikan hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection
