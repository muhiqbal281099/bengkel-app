<?php

namespace App\Http\Controllers;

use App\Models\BengkelSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Tampilkan Form Pengaturan Profil Bengkel.
     */
    public function edit(): View
    {
        $setting = BengkelSetting::getSettings();

        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Simpan Perubahan Profil Bengkel.
     */
    public function update(Request $request): RedirectResponse
    {
        $setting = BengkelSetting::getSettings();

        $validated = $request->validate([
            'nama_bengkel' => ['required', 'string', 'max:255'],
            'alamat_bengkel' => ['nullable', 'string', 'max:500'],
            'telepon_bengkel' => ['nullable', 'string', 'max:100'],
            'running_text' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
        ], [
            'nama_bengkel.required' => 'Nama bengkel wajib diisi.',
            'logo.image' => 'File logo harus berupa gambar.',
            'logo.max' => 'Ukuran logo maksimal 2MB.',
        ]);

        // Cek jika pengguna ingin menghapus logo dan kembali ke default
        if ($request->boolean('hapus_logo') && $setting->logo_path) {
            $oldPath = public_path($setting->logo_path);
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
            $setting->logo_path = null;
        }

        // Handle upload logo baru
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($setting->logo_path && File::exists(public_path($setting->logo_path))) {
                File::delete(public_path($setting->logo_path));
            }

            $file = $request->file('logo');
            $filename = 'bengkel_logo_'.time().'.'.$file->getClientOriginalExtension();
            $destination = public_path('uploads/logos');

            if (! File::isDirectory($destination)) {
                File::makeDirectory($destination, 0755, true);
            }

            $file->move($destination, $filename);
            $setting->logo_path = 'uploads/logos/'.$filename;
        }

        $setting->nama_bengkel = $validated['nama_bengkel'];
        $setting->alamat_bengkel = $validated['alamat_bengkel'] ?? null;
        $setting->telepon_bengkel = $validated['telepon_bengkel'] ?? null;
        $setting->running_text = $validated['running_text'] ?? null;
        $setting->save();

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Profil dan pengaturan bengkel berhasil diperbarui!');
    }
}
