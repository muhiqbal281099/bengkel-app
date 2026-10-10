@extends('layouts.admin')

@section('title', 'Profil & Pengaturan Bengkel')

@section('styles')
<style>
    .form-container {
        max-width: 800px;
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
        border: 1px solid var(--border-color);
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

    .logo-preview-box {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        padding: 1.2rem;
        background: var(--surface-secondary);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        margin-bottom: 1.2rem;
    }

    .logo-img-wrapper {
        width: 90px;
        height: 90px;
        border-radius: 16px;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-color);
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .logo-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>
@endsection

@section('content')

    <div class="form-container">
        <!-- HEADER -->
        <div class="page-header" style="margin-bottom: 1.5rem;">
            <div class="page-title">
                <h2>🏢 Profil & Identitas Bengkel</h2>
                <p>Ubah nama, alamat, kontak, dan logo resmi yang ditampilkan pada sistem, nota transaksi, dan monitor antrean.</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('home') }}" target="_blank" class="btn btn-secondary">
                    🖥️ Pratinjau Layar Antrean ↗
                </a>
            </div>
        </div>

        <!-- FORM CARD -->
        <div class="card">
            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- PREVIEW LOGO -->
                <div class="form-group">
                    <label>Logo Bengkel Saat Ini</label>
                    <div class="logo-preview-box">
                        <div class="logo-img-wrapper">
                            @if($setting->logo_url)
                                <img src="{{ $setting->logo_url }}" alt="Logo Bengkel">
                            @else
                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%; background: linear-gradient(135deg, #1e40af, #2563eb); color: white;">
                                    <span style="font-size: 1.8rem;">🔧</span>
                                    <span style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Default</span>
                                </div>
                            @endif
                        </div>
                        <div>
                            <div style="font-weight: 700; color: var(--text-main); font-size: 1.05rem;">
                                {{ $setting->logo_path ? 'Logo Khusus Diunggah' : 'Menggunakan Logo Default Resmi' }}
                            </div>
                            <p style="color: var(--text-muted); font-size: 0.85rem; margin: 0.2rem 0 0.8rem 0;">
                                Format yang didukung: PNG, JPG, JPEG, SVG, WebP. Disarankan proporsi 1:1 (persegi).
                            </p>
                            @if($setting->logo_path)
                                <label style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--danger); cursor: pointer;">
                                    <input type="checkbox" name="hapus_logo" value="1">
                                    Hapus logo dan gunakan logo default
                                </label>
                            @endif
                        </div>
                    </div>

                    <input type="file" name="logo" id="logo" accept="image/*" style="padding: 0.5rem;">
                    @error('logo')
                        <div style="color: var(--danger); font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.8rem 0;">

                <!-- NAMA BENGKEL -->
                <div class="form-group">
                    <label for="nama_bengkel">Nama Bengkel <span style="color: var(--danger)">*</span></label>
                    <input 
                        type="text" 
                        id="nama_bengkel" 
                        name="nama_bengkel" 
                        value="{{ old('nama_bengkel', $setting->nama_bengkel) }}" 
                        placeholder="Contoh: Bengkel Pro Motor & Mobil"
                        required>
                    @error('nama_bengkel')
                        <div style="color: var(--danger); font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- ALAMAT BENGKEL -->
                <div class="form-group">
                    <label for="alamat_bengkel">Alamat Lengkap Bengkel</label>
                    <textarea 
                        id="alamat_bengkel" 
                        name="alamat_bengkel" 
                        rows="3" 
                        placeholder="Contoh: Jl. Raya Otomotif No. 88, Kel. Sukamaju, Kec. Cilandak, Jakarta Selatan">{{ old('alamat_bengkel', $setting->alamat_bengkel) }}</textarea>
                    @error('alamat_bengkel')
                        <div style="color: var(--danger); font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- NOMOR TELEPON -->
                <div class="form-group">
                    <label for="telepon_bengkel">Nomor Telepon / Kontak WhatsApp</label>
                    <input 
                        type="text" 
                        id="telepon_bengkel" 
                        name="telepon_bengkel" 
                        value="{{ old('telepon_bengkel', $setting->telepon_bengkel) }}" 
                        placeholder="Contoh: 0812-3456-7890 / (021) 7890123">
                    @error('telepon_bengkel')
                        <div style="color: var(--danger); font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- RUNNING TEXT -->
                <div class="form-group">
                    <label for="running_text">Teks Berjalan / Pengumuman (Tampil di Monitor Publik)</label>
                    <input 
                        type="text" 
                        id="running_text" 
                        name="running_text" 
                        value="{{ old('running_text', $setting->running_text) }}" 
                        placeholder="Contoh: Selamat datang! Harap menunggu nomor antrean Anda dipanggil.">
                    @error('running_text')
                        <div style="color: var(--danger); font-size: 0.85rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- SUBMIT ACTIONS -->
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.2rem;">
                        💾 Simpan Pengaturan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
