@extends('layouts.montir')

@section('title', 'Detail Antrean ' . $queue->nomor_antrean)

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>📋 Detail Antrean {{ $queue->nomor_antrean }}</h2>
        <p>Lihat detail kendaraan, servis, dan perbarui catatan teknis.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('montir.dashboard') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>🚗 Informasi Kendaraan & Servis</h3>
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
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div>
            @foreach([
                ['Nomor Antrean', $queue->nomor_antrean],
                ['Pelanggan', $queue->kendaraan->user->name ?? 'Walk-in'],
                ['No. Polisi', $queue->kendaraan->no_polisi],
                ['Kendaraan', $queue->kendaraan->merk . ' ' . $queue->kendaraan->model . ' (' . $queue->kendaraan->tahun . ')'],
            ] as [$label, $value])
                <div style="display: flex; gap: 1rem; padding: 0.65rem 0; border-bottom: 1px solid var(--border-color); align-items: center;">
                    <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.5px; width: 45%; flex-shrink: 0;">{{ $label }}</span>
                    <span style="font-weight: 600;">{{ $value }}</span>
                </div>
            @endforeach
        </div>
        <div>
            @foreach([
                ['Jenis Servis', $queue->service->nama_layanan],
                ['Biaya Jasa', 'Rp ' . number_format($queue->service->biaya, 0, ',', '.')],
                ['Estimasi', $queue->service->durasi_estimasi . ' menit'],
                ['Didaftarkan', $queue->created_at->format('d/m/Y H:i')],
            ] as [$label, $value])
                <div style="display: flex; gap: 1rem; padding: 0.65rem 0; border-bottom: 1px solid var(--border-color); align-items: center;">
                    <span style="font-size: 0.82rem; font-weight: 700; color: var(--text-dim); text-transform: uppercase; letter-spacing: 0.5px; width: 45%; flex-shrink: 0;">{{ $label }}</span>
                    <span style="font-weight: 600;">{{ $value }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CATATAN SERVIS -->
    <div style="margin-top: 1.5rem;">
        @if(in_array($queue->status, ['waiting', 'checking', 'in_service']))
            <form method="POST" action="{{ route('montir.antrean.update-status', $queue) }}">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="{{ $queue->status }}">

                <div class="form-group">
                    <label for="catatan_servis" class="form-label">📝 Catatan & Temuan Teknis</label>
                    <textarea name="catatan_servis" id="catatan_servis" class="form-control" rows="5"
                              placeholder="Tulis temuan teknis, kondisi komponen, rekomendasi penggantian sparepart...">{{ $queue->catatan_servis }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-accent">💾 Simpan Catatan</button>
                </div>
            </form>
        @else
            <div style="padding: 1rem; background: var(--surface-secondary); border-radius: 10px; border-left: 3px solid var(--accent);">
                <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-dim); margin-bottom: 0.5rem;">
                    📝 Catatan Servis
                </div>
                <p style="color: var(--text-main);">{{ $queue->catatan_servis ?? '— Tidak ada catatan —' }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    @media (max-width: 700px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
