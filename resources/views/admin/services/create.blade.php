@extends('layouts.admin')

@section('title', 'Tambah Layanan Servis')

@section('styles')
<style>
    .form-container {
        max-width: 720px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--text-muted);
    }

    .form-group input, 
    .form-group textarea {
        width: 100%;
        padding: 0.85rem 1.2rem;
        background: var(--input-bg);
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-main);
        font-size: 0.98rem;
        outline: none;
        transition: all 0.25s ease;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-group .field-error {
        margin-top: 0.4rem;
        color: var(--danger);
        font-size: 0.85rem;
        font-weight: 500;
    }

    .form-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 650px) {
        .form-grid-2 { grid-template-columns: 1fr; }
    }

    .input-addon-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-addon {
        position: absolute;
        left: 1.2rem;
        color: var(--text-dim);
        font-weight: 600;
        font-size: 0.95rem;
    }

    .input-addon-wrapper input {
        padding-left: 3.2rem;
    }
</style>
@endsection

@section('content')

    <div class="form-container">
        <!-- HEADER -->
        <div class="page-header" style="margin-bottom: 1.5rem;">
            <div class="page-title">
                <h2>➕ Tambah Layanan Servis Baru</h2>
                <p>Daftarkan tindakan servis baru beserta tarif ongkos kerja dan estimasi durasi.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                    ← Kembali ke Daftar Jasa
                </a>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="card">
            <form method="POST" action="{{ route('admin.services.store') }}">
                @csrf

                <!-- NAMA LAYANAN -->
                <div class="form-group">
                    <label for="nama_layanan">Nama Layanan Servis <span style="color: var(--danger)">*</span></label>
                    <input 
                        type="text" 
                        id="nama_layanan" 
                        name="nama_layanan" 
                        value="{{ old('nama_layanan') }}" 
                        placeholder="Contoh: Servis Injeksi & Kalibrasi Sensor"
                        required>
                    @error('nama_layanan')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid-2">
                    <!-- BIAYA JASA -->
                    <div class="form-group">
                        <label for="biaya">Biaya Ongkos Jasa (Rp) <span style="color: var(--danger)">*</span></label>
                        <div class="input-addon-wrapper">
                            <span class="input-addon">Rp</span>
                            <input 
                                type="number" 
                                id="biaya" 
                                name="biaya" 
                                value="{{ old('biaya') }}" 
                                min="0" 
                                step="1000" 
                                placeholder="50000"
                                required>
                        </div>
                        @error('biaya')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- ESTIMASI WAKTU -->
                    <div class="form-group">
                        <label for="durasi_estimasi">Estimasi Waktu Pengerjaan (Menit) <span style="color: var(--danger)">*</span></label>
                        <input 
                            type="number" 
                            id="durasi_estimasi" 
                            name="durasi_estimasi" 
                            value="{{ old('durasi_estimasi', 30) }}" 
                            min="1" 
                            placeholder="30"
                            required>
                        @error('durasi_estimasi')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- DESKRIPSI -->
                <div class="form-group">
                    <label for="deskripsi">Deskripsi / Ruang Lingkup Tindakan Servis</label>
                    <textarea 
                        id="deskripsi" 
                        name="deskripsi" 
                        rows="4" 
                        placeholder="Tuliskan tindakan yang akan dilakukan oleh montir dalam servis ini...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- SUBMIT ACTIONS -->
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--glass-border);">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        💾 Simpan Layanan Servis
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
