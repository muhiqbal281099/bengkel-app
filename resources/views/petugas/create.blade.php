@extends('layouts.petugas')

@section('title', 'Daftarkan Kendaraan')

@section('styles')
<style>
    .tab-switcher {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        background: var(--surface-secondary);
        padding: 0.35rem;
        border-radius: 12px;
        width: fit-content;
    }

    .tab-btn {
        padding: 0.55rem 1.25rem;
        border-radius: 9px;
        border: none;
        font-size: 0.92rem;
        font-weight: 700;
        cursor: pointer;
        background: transparent;
        color: var(--text-muted);
        transition: all 0.2s ease;
    }

    .tab-btn.active {
        background: var(--surface);
        color: var(--accent);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }

    @media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

    .hint { font-size: 0.8rem; color: var(--text-dim); margin-top: 0.3rem; }
</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-title">
        <h2>➕ Daftarkan Kendaraan ke Antrean</h2>
        <p>Pilih kendaraan yang sudah terdaftar, atau daftarkan kendaraan baru.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary">← Kembali</a>
    </div>
</div>

<div class="card">
    <!-- TAB SWITCHER -->
    <div class="tab-switcher">
        <button type="button" class="tab-btn active" id="tab-existing-btn" onclick="switchTab('existing')">
            🔍 Cari Kendaraan Terdaftar
        </button>
        <button type="button" class="tab-btn" id="tab-new-btn" onclick="switchTab('new')">
            🆕 Kendaraan Baru / Walk-in
        </button>
    </div>

    <form id="form-antrean" method="POST" action="{{ route('petugas.antrean.store') }}">
        @csrf
        <input type="hidden" name="mode" id="input-mode" value="existing">

        <!-- PANEL: KENDARAAN TERDAFTAR -->
        <div class="tab-panel active" id="panel-existing">
            <div class="form-group">
                <label for="kendaraan_id" class="form-label">Pilih Kendaraan *</label>
                <select name="kendaraan_id" id="kendaraan_id"
                        class="form-control {{ $errors->has('kendaraan_id') ? 'is-invalid' : '' }}">
                    <option value="">-- Pilih kendaraan yang terdaftar --</option>
                    @foreach($kendaraans as $kendaraan)
                        <option value="{{ $kendaraan->id }}" {{ old('kendaraan_id') == $kendaraan->id ? 'selected' : '' }}>
                            {{ $kendaraan->no_polisi }} — {{ $kendaraan->merk }} {{ $kendaraan->model }}
                            ({{ $kendaraan->user->name ?? 'Walk-in' }})
                        </option>
                    @endforeach
                </select>
                @error('kendaraan_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- PANEL: KENDARAAN BARU -->
        <div class="tab-panel" id="panel-new">
            <div class="form-row">
                <div class="form-group">
                    <label for="pelanggan_nama" class="form-label">Nama Pelanggan *</label>
                    <input type="text" name="pelanggan_nama" id="pelanggan_nama"
                           class="form-control {{ $errors->has('pelanggan_nama') ? 'is-invalid' : '' }}"
                           value="{{ old('pelanggan_nama') }}" placeholder="Contoh: Budi Santoso">
                    @error('pelanggan_nama')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="no_polisi" class="form-label">Nomor Polisi *</label>
                    <input type="text" name="no_polisi" id="no_polisi"
                           class="form-control {{ $errors->has('no_polisi') ? 'is-invalid' : '' }}"
                           value="{{ old('no_polisi') }}" placeholder="Contoh: B 1234 ABC" style="text-transform: uppercase;">
                    @error('no_polisi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="merk" class="form-label">Merek Kendaraan *</label>
                    <input type="text" name="merk" id="merk"
                           class="form-control {{ $errors->has('merk') ? 'is-invalid' : '' }}"
                           value="{{ old('merk') }}" placeholder="Contoh: Honda, Yamaha, Suzuki">
                    @error('merk')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="model_kendaraan" class="form-label">Model Kendaraan *</label>
                    <input type="text" name="model_kendaraan" id="model_kendaraan"
                           class="form-control {{ $errors->has('model_kendaraan') ? 'is-invalid' : '' }}"
                           value="{{ old('model_kendaraan') }}" placeholder="Contoh: Vario 160 CBS">
                    @error('model_kendaraan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group" style="max-width: 200px;">
                <label for="tahun" class="form-label">Tahun Kendaraan *</label>
                <input type="number" name="tahun" id="tahun"
                       class="form-control {{ $errors->has('tahun') ? 'is-invalid' : '' }}"
                       value="{{ old('tahun', date('Y')) }}" min="1970" max="{{ date('Y') }}">
                @error('tahun')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- SERVIS & MEKANIK (selalu tampil) -->
        <hr style="border: none; border-top: 1.5px solid var(--border-color); margin: 1.5rem 0;">

        <div class="form-row">
            <div class="form-group">
                <label for="service_id" class="form-label">Jenis Layanan Servis *</label>
                <select name="service_id" id="service_id"
                        class="form-control {{ $errors->has('service_id') ? 'is-invalid' : '' }}" required>
                    <option value="">-- Pilih jenis servis --</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                            {{ $service->nama_layanan }} — Rp {{ number_format($service->biaya, 0, ',', '.') }}
                            (± {{ $service->durasi_estimasi }} menit)
                        </option>
                    @endforeach
                </select>
                @error('service_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label for="montor_id" class="form-label">Penugasan Mekanik</label>
                <select name="montor_id" id="montor_id" class="form-control">
                    <option value="">-- Belum ditugaskan --</option>
                    @foreach($montirs as $montir)
                        <option value="{{ $montir->id }}" {{ old('montor_id') == $montir->id ? 'selected' : '' }}>
                            {{ $montir->name }}
                        </option>
                    @endforeach
                </select>
                <div class="hint">💡 Bisa ditugaskan belakangan setelah pengecekan.</div>
            </div>
        </div>

        <div class="form-group">
            <label for="catatan_servis" class="form-label">Keluhan / Catatan Servis</label>
            <textarea name="catatan_servis" id="catatan_servis" class="form-control" rows="3"
                      placeholder="Contoh: Pelanggan mengeluhkan suara mesin kasar dan tarikan berat saat akselerasi...">{{ old('catatan_servis') }}</textarea>
        </div>

        <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 0.5rem;">
            <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-accent" id="btn-submit-antrean">
                🎫 Daftarkan ke Antrean
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Restore tab state jika ada validation error
    @if(old('mode') === 'new')
        switchTab('new');
    @endif

    function switchTab(tab) {
        document.getElementById('input-mode').value = tab;

        document.getElementById('tab-existing-btn').classList.toggle('active', tab === 'existing');
        document.getElementById('tab-new-btn').classList.toggle('active', tab === 'new');
        document.getElementById('panel-existing').classList.toggle('active', tab === 'existing');
        document.getElementById('panel-new').classList.toggle('active', tab === 'new');
    }

    // Auto uppercase nomor polisi
    const nopol = document.getElementById('no_polisi');
    if (nopol) {
        nopol.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
</script>
@endsection
