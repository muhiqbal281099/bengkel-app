@extends('layouts.petugas')

@section('title', 'Detail Antrean ' . $queue->nomor_antrean)

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>🎫 Detail Antrean {{ $queue->nomor_antrean }}</h2>
        <p>Kelola status, penugasan mekanik, dan catatan servis.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary">← Kembali ke Daftar</a>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">

    <!-- INFO KENDARAAN & SERVIS -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3>🚗 Informasi Kendaraan</h3>
        </div>

        <table style="width: 100%; border-collapse: collapse;">
            @php
                $rows = [
                    ['No. Antrean',  $queue->nomor_antrean],
                    ['Pelanggan',    $queue->kendaraan->user->name ?? 'Walk-in'],
                    ['No. Polisi',   $queue->kendaraan->no_polisi],
                    ['Kendaraan',    $queue->kendaraan->merk . ' ' . $queue->kendaraan->model . ' (' . $queue->kendaraan->tahun . ')'],
                    ['Jenis Servis', $queue->service->nama_layanan],
                    ['Biaya Jasa',   'Rp ' . number_format($queue->service->biaya, 0, ',', '.')],
                    ['Mekanik',      $queue->montor->name ?? '— Belum ditugaskan'],
                    ['Didaftarkan',  $queue->created_at->format('d/m/Y H:i')],
                ];
            @endphp
            @foreach($rows as [$label, $value])
                <tr>
                    <td style="padding: 0.6rem 0; color: var(--text-dim); font-size: 0.88rem; font-weight: 700; width: 40%; text-transform: uppercase; letter-spacing: 0.5px; border-bottom: 1px solid var(--border-color);">
                        {{ $label }}
                    </td>
                    <td style="padding: 0.6rem 0; font-weight: 600; border-bottom: 1px solid var(--border-color);">
                        {{ $value }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td style="padding: 0.6rem 0; color: var(--text-dim); font-size: 0.88rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Status</td>
                <td style="padding: 0.6rem 0;">
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
            </tr>
        </table>

        @if($queue->catatan_servis)
            <div style="margin-top: 1.2rem; padding: 1rem; background: var(--surface-secondary); border-radius: 10px; border-left: 3px solid var(--accent);">
                <div style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-dim); margin-bottom: 0.4rem;">
                    📝 Catatan Keluhan
                </div>
                <p style="font-size: 0.95rem; color: var(--text-main);">{{ $queue->catatan_servis }}</p>
            </div>
        @endif

        @if($queue->transaction)
            <div style="margin-top: 1rem; padding: 0.8rem 1rem; background: #dcfce7; border-radius: 10px; border: 1px solid #86efac;">
                <span style="font-size: 0.88rem; font-weight: 700; color: #15803d;">
                    💳 Sudah dibayar — Total: Rp {{ number_format($queue->transaction->total_pembayaran, 0, ',', '.') }}
                </span>
            </div>
        @endif
    </div>

    <!-- FORM UPDATE STATUS -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header">
            <h3>✏️ Perbarui Status Antrean</h3>
        </div>

        @if($queue->status === 'completed' && $queue->transaction)
            <div style="text-align: center; padding: 2rem; color: var(--text-dim);">
                <div style="font-size: 2.5rem; margin-bottom: 0.8rem;">🎉</div>
                <p style="font-weight: 700; font-size: 1rem;">Servis dan Pembayaran Selesai</p>
                <p style="font-size: 0.9rem; margin-top: 0.3rem;">Antrean ini sudah lunas dan tidak bisa diubah.</p>
            </div>
        @else
            <form method="POST" action="{{ route('petugas.antrean.update-status', $queue) }}" id="form-update-status">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="status" class="form-label">Status Baru *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="waiting"    {{ $queue->status === 'waiting'    ? 'selected' : '' }}>⏳ Menunggu</option>
                        <option value="checking"   {{ $queue->status === 'checking'   ? 'selected' : '' }}>🔍 Pengecekan</option>
                        <option value="in_service" {{ $queue->status === 'in_service' ? 'selected' : '' }}>🔧 Dalam Servis</option>
                        <option value="completed"  {{ $queue->status === 'completed'  ? 'selected' : '' }}>✅ Selesai</option>
                        <option value="cancelled"  {{ $queue->status === 'cancelled'  ? 'selected' : '' }}>❌ Dibatalkan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="montor_id" class="form-label">Penugasan Mekanik</label>
                    <select name="montor_id" id="montor_id" class="form-control">
                        <option value="">-- Belum ditugaskan --</option>
                        @foreach($montirs as $montir)
                            <option value="{{ $montir->id }}" {{ $queue->montor_id == $montir->id ? 'selected' : '' }}>
                                {{ $montir->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="catatan_servis" class="form-label">Perbarui Catatan Servis</label>
                    <textarea name="catatan_servis" id="catatan_servis" class="form-control" rows="4"
                              placeholder="Tambah catatan teknis dari mekanik...">{{ $queue->catatan_servis }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-accent">💾 Simpan Perubahan</button>
                </div>
            </form>
        @endif
    </div>
</div>

@endsection

@section('styles')
<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection
