<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Tampilan Daftar Master Layanan Servis.
     */
    public function index(): View
    {
        $services = Service::withCount('queues')
            ->orderBy('nama_layanan', 'asc')
            ->get();

        return view('admin.services.index', compact('services'));
    }

    /**
     * Form Tambah Layanan Servis Baru.
     */
    public function create(): View
    {
        return view('admin.services.create');
    }

    /**
     * Simpan Layanan Servis Baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_layanan' => ['required', 'string', 'max:255'],
            'biaya' => ['required', 'numeric', 'min:0'],
            'durasi_estimasi' => ['required', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ], [
            'nama_layanan.required' => 'Nama layanan wajib diisi.',
            'biaya.min' => 'Biaya layanan tidak boleh bernilai negatif.',
            'durasi_estimasi.min' => 'Durasi estimasi minimal 1 menit.',
        ]);

        $service = Service::create($validated);

        return redirect()
            ->route('admin.services.index')
            ->with('success', "Layanan servis '{$service->nama_layanan}' berhasil ditambahkan!");
    }

    /**
     * Form Ubah Layanan Servis.
     */
    public function edit(Service $service): View
    {
        return view('admin.services.edit', compact('service'));
    }

    /**
     * Perbarui Data Layanan Servis.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        $validated = $request->validate([
            'nama_layanan' => ['required', 'string', 'max:255'],
            'biaya' => ['required', 'numeric', 'min:0'],
            'durasi_estimasi' => ['required', 'integer', 'min:1'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ]);

        $service->update($validated);

        return redirect()
            ->route('admin.services.index')
            ->with('success', "Layanan servis '{$service->nama_layanan}' berhasil diperbarui!");
    }

    /**
     * Hapus Layanan Servis.
     */
    public function destroy(Service $service): RedirectResponse
    {
        if ($service->queues()->exists()) {
            return back()->with('error', "Gagal menghapus: Layanan '{$service->nama_layanan}' masih terhubung dengan riwayat antrean servis.");
        }

        $nama = $service->nama_layanan;
        $service->delete();

        return redirect()
            ->route('admin.services.index')
            ->with('success', "Layanan servis '{$nama}' berhasil dihapus.");
    }
}
