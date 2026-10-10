@extends('layouts.montir')

@section('title', 'Panel Mekanik')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>🔧 Tugas Servis Saya</h2>
        <p>Halo, <strong>{{ auth()->user()->name }}</strong> &bull; {{ now()->isoFormat('dddd, D MMMM Y') }}</p>
    </div>
</div>

<!-- STATISTIK CEPAT -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-num" style="color: var(--accent);">{{ $activeQueues->count() }}</div>
        <div class="stat-label">🔧 Tugas Aktif</div>
    </div>
    <div class="stat-card">
        <div class="stat-num" style="color: var(--success);">{{ $completedToday }}</div>
        <div class="stat-label">✅ Selesai Hari Ini</div>
    </div>
</div>

<!-- TUGAS AKTIF HARI INI -->
<div class="card">
    <div class="card-header">
        <h3>⚡ Antrean Aktif Hari Ini</h3>
    </div>

    @if($activeQueues->isEmpty())
        <div style="text-align: center; padding: 3rem; color: var(--text-dim);">
            <div style="font-size: 3rem; margin-bottom: 1rem;">😊</div>
            <p style="font-size: 1.1rem; font-weight: 600;">Tidak ada tugas aktif saat ini.</p>
            <p style="font-size: 0.9rem; margin-top: 0.4rem;">Menunggu penugasan dari petugas frontdesk.</p>
        </div>
    @else
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($activeQueues as $queue)
            <div style="background: var(--surface-secondary); border: 1px solid var(--border-color); border-radius: 14px;
                        padding: 1.4rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 1.2rem; flex: 1;">
                    <!-- Nomor Antrean -->
                    <div style="background: var(--accent); color: white; border-radius: 12px; padding: 0.6rem 1rem;
                                font-size: 1.2rem; font-weight: 900; text-align: center; min-width: 80px; box-shadow: 0 3px 10px rgba(124,58,237,0.3);">
                        {{ $queue->nomor_antrean }}
                    </div>

                    <!-- Info -->
                    <div>
                        <div style="font-size: 1.05rem; font-weight: 800; margin-bottom: 0.2rem;">
                            {{ $queue->kendaraan->merk }} {{ $queue->kendaraan->model }}
                            <span style="font-size: 0.85rem; color: var(--text-dim); font-weight: 600;">
                                ({{ $queue->kendaraan->no_polisi }})
                            </span>
                        </div>
                        <div style="font-size: 0.9rem; color: var(--text-muted);">
                            🔧 {{ $queue->service->nama_layanan }}
                            &bull; Pelanggan: {{ $queue->kendaraan->user->name ?? 'Walk-in' }}
                        </div>
                        @if($queue->catatan_servis)
                            <div style="font-size: 0.85rem; color: var(--text-dim); margin-top: 0.3rem;">
                                📝 {{ Str::limit($queue->catatan_servis, 80) }}
                            </div>
                        @endif
                        <div style="margin-top: 0.5rem;">
                            @php
                                $badgeClass = match($queue->status) {
                                    'waiting'    => 'badge-waiting',
                                    'checking'   => 'badge-checking',
                                    'in_service' => 'badge-service',
                                    default      => '',
                                };
                                $statusLabel = match($queue->status) {
                                    'waiting'    => '⏳ Menunggu',
                                    'checking'   => '🔍 Pengecekan',
                                    'in_service' => '🔧 Dalam Servis',
                                    default      => $queue->status,
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                        </div>
                    </div>
                </div>

                <!-- AKSI UPDATE STATUS -->
                <div style="display: flex; flex-direction: column; gap: 0.5rem; min-width: 200px;">
                    @if($queue->status === 'waiting')
                        <form method="POST" action="{{ route('montir.antrean.update-status', $queue) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="checking">
                            <button type="submit" class="btn btn-info btn-sm" style="width: 100%; background: #0284c7; color: white;">
                                🔍 Mulai Pengecekan
                            </button>
                        </form>
                    @elseif($queue->status === 'checking')
                        <form method="POST" action="{{ route('montir.antrean.update-status', $queue) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="in_service">
                            <button type="submit" class="btn btn-warning btn-sm" style="width: 100%;">
                                🔧 Mulai Servis
                            </button>
                        </form>
                    @elseif($queue->status === 'in_service')
                        <button type="button" class="btn btn-success btn-sm" style="width: 100%;"
                                onclick="openCompleteModal('{{ $queue->id }}', '{{ $queue->nomor_antrean }}')">
                            ✅ Tandai Selesai
                        </button>
                    @endif
                    <a href="{{ route('montir.antrean.show', $queue) }}" class="btn btn-secondary btn-sm" style="text-align: center; width: 100%;">
                        📋 Detail & Catatan
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

<!-- RIWAYAT SERVIS SELESAI -->
@if($history->isNotEmpty())
<div class="card">
    <div class="card-header">
        <h3>📜 Riwayat Servis Selesai</h3>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>No. Antrean</th>
                    <th>Kendaraan</th>
                    <th>Jenis Servis</th>
                    <th>Pelanggan</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($history as $q)
                <tr>
                    <td><span style="font-weight: 800; color: var(--success);">{{ $q->nomor_antrean }}</span></td>
                    <td>
                        {{ $q->kendaraan->no_polisi }}<br>
                        <span style="font-size: 0.82rem; color: var(--text-dim);">{{ $q->kendaraan->merk }} {{ $q->kendaraan->model }}</span>
                    </td>
                    <td>{{ $q->service->nama_layanan }}</td>
                    <td>{{ $q->kendaraan->user->name ?? '—' }}</td>
                    <td style="color: var(--text-dim); font-size: 0.88rem;">{{ $q->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- MODAL KONFIRMASI SELESAI -->
<div class="modal-overlay" id="modal-complete">
    <div class="modal-box">
        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 0.5rem;">✅ Konfirmasi Servis Selesai</h3>
        <p style="color: var(--text-muted); font-size: 0.95rem; margin-bottom: 1.2rem;">
            Tandai antrean <strong id="modal-nomer"></strong> sebagai selesai?
            Petugas kasir akan dinotifikasi untuk memproses pembayaran.
        </p>

        <form method="POST" id="form-complete">
            @csrf @method('PATCH')
            <div class="form-group">
                <label for="catatan_selesai" class="form-label">Catatan Akhir Servis (opsional)</label>
                <textarea name="catatan_servis" id="catatan_selesai" class="form-control" rows="3"
                          placeholder="Contoh: Ganti oli mesin selesai, cek tekanan ban normal..."></textarea>
            </div>
            <input type="hidden" name="status" value="completed">
            <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 1rem;">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn btn-success">✅ Konfirmasi Selesai</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openCompleteModal(queueId, nomerAntrean) {
        document.getElementById('modal-nomer').textContent = nomerAntrean;
        document.getElementById('form-complete').action = `/montir/antrean/${queueId}/status`;
        document.getElementById('modal-complete').classList.add('active');
    }

    function closeModal() {
        document.getElementById('modal-complete').classList.remove('active');
    }

    // Close modal on overlay click
    document.getElementById('modal-complete').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
</script>
@endsection
