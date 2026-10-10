@extends('layouts.admin')

@section('title', 'Tambah Suku Cadang')

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
    .form-group select {
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
    .form-group select:focus {
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
                <h2>➕ Tambah Suku Cadang Baru</h2>
                <p>Masukkan rincian data komponen suku cadang untuk ditambahkan ke inventaris bengkel.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.spareparts.index') }}" class="btn btn-secondary">
                    ← Kembali ke Katalog
                </a>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="card">
            <form method="POST" action="{{ route('admin.spareparts.store') }}">
                @csrf

                <div class="form-grid-2">
                    <!-- KODE BARANG -->
                    <div class="form-group">
                        <label for="kode_barang">Kode Suku Cadang <span style="color: var(--danger)">*</span></label>
                        <input 
                            type="text" 
                            id="kode_barang" 
                            name="kode_barang" 
                            value="{{ old('kode_barang', $suggestedCode) }}" 
                            placeholder="Contoh: SP-OLI-005"
                            required>
                        @error('kode_barang')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- KATEGORI -->
                    <div class="form-group">
                        <label for="kategori">Kategori Barang <span style="color: var(--danger)">*</span></label>
                        <input 
                            type="text" 
                            id="kategori" 
                            name="kategori" 
                            list="categoryList"
                            value="{{ old('kategori') }}" 
                            placeholder="Ketik atau pilih kategori..."
                            required>
                        <datalist id="categoryList">
                            @foreach($existingCategories as $cat)
                                <option value="{{ $cat }}">
                            @endforeach
                            <option value="Oli & Pelumas">
                            <option value="Pengereman">
                            <option value="Kelistrikan">
                            <option value="Transmisi">
                            <option value="Ban & Roda">
                            <option value="Mesin & Karbu">
                        </datalist>
                        @error('kategori')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- NAMA BARANG -->
                <div class="form-group">
                    <label for="nama_barang">Nama Suku Cadang Lengkap <span style="color: var(--danger)">*</span></label>
                    <input 
                        type="text" 
                        id="nama_barang" 
                        name="nama_barang" 
                        value="{{ old('nama_barang') }}" 
                        placeholder="Contoh: Oli Mesin Yamalube Silver 0.8L"
                        required>
                    @error('nama_barang')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-grid-2">
                    <!-- HARGA JUAL -->
                    <div class="form-group">
                        <label for="harga_jual">Harga Jual Konsumen (Rp) <span style="color: var(--danger)">*</span></label>
                        <div class="input-addon-wrapper">
                            <span class="input-addon">Rp</span>
                            <input 
                                type="number" 
                                id="harga_jual" 
                                name="harga_jual" 
                                value="{{ old('harga_jual') }}" 
                                min="0" 
                                step="500" 
                                placeholder="45000"
                                required>
                        </div>
                        @error('harga_jual')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- JUMLAH STOK AWAL -->
                    <div class="form-group">
                        <label for="stok">Jumlah Stok Awal (Unit) <span style="color: var(--danger)">*</span></label>
                        <input 
                            type="number" 
                            id="stok" 
                            name="stok" 
                            value="{{ old('stok', 10) }}" 
                            min="0" 
                            placeholder="10"
                            required>
                        @error('stok')
                            <div class="field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- SUBMIT ACTIONS -->
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--glass-border);">
                    <a href="{{ route('admin.spareparts.index') }}" class="btn btn-secondary">
                        Batal
                    </a>
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        💾 Simpan ke Inventaris
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
