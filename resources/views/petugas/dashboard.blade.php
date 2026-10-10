@extends('layouts.petugas')

@section('title', 'Dashboard Frontdesk')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>📋 Manajemen Antrean Hari Ini</h2>
        <p>{{ now()->isoFormat('dddd, D MMMM Y') }} &bull; Total: {{ $todayQueues->count() }} kendaraan terdaftar</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('petugas.antrean.create') }}" class="btn btn-accent" id="btn-daftar-kendaraan">
            ➕ Daftarkan Kendaraan Baru
        </a>
    </div>
</div>

<!-- STATISTIK CEPAT -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-num" style="color: var(--warning);">{{ $stats['waiting'] }}</div>
        <div class="stat-label">⏳ Menunggu</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color: var(--info, #0284c7);">{{ $stats['checking'] }}</div>
        <div class="stat-label">🔍 Pengecekan</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color: var(--accent);">{{ $stats['in_service'] }}</div>
        <div class="stat-label">🔧 Dalam Servis</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color: var(--success);">{{ $stats['completed'] }}</div>
        <div class="stat-label">✅ Selesai</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color: var(--text-dim);">{{ $stats['cancelled'] }}</div>
        <div class="stat-label">❌ Dibatalkan</div>
    </div>
</div>

<!-- DAFTAR ANTREAN -->
<div class="card">
    <div class="card-header">
        <h3>🚗 Daftar Antrean — {{ now()->format('d/m/Y') }}</h3>
        <span style="font-size: 0.85rem; color: var(--text-dim);">
            Klik baris untuk kelola antrean
        </span>
    </div>

    @if($todayQueues->isEmpty())
        <div style="text-align: center; padding: 3rem; color: var(--text-dim);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🏁</div>
            <p style="font-size: 1.1rem; font-weight: 600;">Belum ada antrean hari ini.</p>
            <p style="margin-top: 0.5rem;">Daftarkan kendaraan pertama menggunakan tombol di atas.</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>No. Antrean</th>
                        <th>Kendaraan</th>
                        <th>Pelanggan</th>
                        <th>Jenis Servis</th>
                        <th>Mekanik</th>
                        <th>Status</th>
                        <th>Waktu Daftar</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayQueues as $queue)
                    <tr>
                        <td>
                            <span style="font-size: 1.05rem; font-weight: 900; color: var(--accent); letter-spacing: 0.5px;">
                                {{ $queue->nomor_antrean }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight: 700;">{{ $queue->kendaraan->no_polisi }}</div>
                            <div style="font-size: 0.82rem; color: var(--text-dim);">
                                {{ $queue->kendaraan->merk }} {{ $queue->kendaraan->model }}
                            </div>
                        </td>
                        <td>{{ $queue->kendaraan->user->name ?? '—' }}</td>
                        <td>
                            <div style="font-weight: 600;">{{ $queue->service->nama_layanan }}</div>
                            <div style="font-size: 0.82rem; color: var(--text-dim);">
                                Rp {{ number_format($queue->service->biaya, 0, ',', '.') }}
                            </div>
                        </td>
                        <td>{{ $queue->montor->name ?? '<span style="color:var(--text-dim)">—</span>' }}</td>
                        <td>
                            @php
                                $badgeClass = match($queue->status) {
                                    'waiting'    => 'badge-waiting',
                                    'checking'   => 'badge-checking',
                                    'in_service' => 'badge-service',
                                    'completed'  => 'badge-completed',
                                    'cancelled'  => 'badge-cancelled',
                                    default      => '',
                                };
                                $statusLabel = match($queue->status) {
                                    'waiting'    => '⏳ Menunggu',
                                    'checking'   => '🔍 Pengecekan',
                                    'in_service' => '🔧 Dalam Servis',
                                    'completed'  => '✅ Selesai',
                                    'cancelled'  => '❌ Dibatalkan',
                                    default      => $queue->status,
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                        </td>
                        <td style="color: var(--text-dim); font-size: 0.88rem;">
                            {{ $queue->created_at->format('H:i') }}
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('petugas.antrean.show', $queue) }}" class="btn btn-secondary btn-sm">
                                ✏️ Kelola
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
