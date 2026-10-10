<?php

namespace App\Http\Controllers;

use App\Models\Kendaraan;
use App\Models\Queue;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontdeskController extends Controller
{
    /**
     * Dashboard Petugas Frontdesk: Daftar antrean hari ini.
     */
    public function dashboard(): View
    {
        $todayQueues = Queue::with(['kendaraan.user', 'service', 'montor'])
            ->whereDate('created_at', today())
            ->orderBy('id', 'asc')
            ->get();

        $stats = [
            'waiting' => $todayQueues->where('status', 'waiting')->count(),
            'checking' => $todayQueues->where('status', 'checking')->count(),
            'in_service' => $todayQueues->where('status', 'in_service')->count(),
            'completed' => $todayQueues->where('status', 'completed')->count(),
            'cancelled' => $todayQueues->where('status', 'cancelled')->count(),
        ];

        return view('petugas.dashboard', compact('todayQueues', 'stats'));
    }

    /**
     * Form Daftarkan Kendaraan & Antrean Baru.
     */
    public function create(): View
    {
        $services = Service::orderBy('nama_layanan')->get();
        $montirs = User::where('role', 'montor')->orderBy('name')->get();
        $kendaraans = Kendaraan::with('user')->orderBy('no_polisi')->get();

        return view('petugas.create', compact('services', 'montirs', 'kendaraans'));
    }

    /**
     * Simpan Antrean Baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mode' => ['required', 'in:existing,new'],
            // Mode: pilih kendaraan yang sudah ada
            'kendaraan_id' => ['required_if:mode,existing', 'nullable', 'exists:kendaraans,id'],
            // Mode: daftarkan kendaraan baru
            'pelanggan_nama' => ['required_if:mode,new', 'nullable', 'string', 'max:100'],
            'no_polisi' => ['required_if:mode,new', 'nullable', 'string', 'max:20'],
            'merk' => ['required_if:mode,new', 'nullable', 'string', 'max:60'],
            'model_kendaraan' => ['required_if:mode,new', 'nullable', 'string', 'max:60'],
            'tahun' => ['required_if:mode,new', 'nullable', 'integer', 'min:1970', 'max:'.date('Y')],
            // Servis & Mekanik
            'service_id' => ['required', 'exists:services,id'],
            'montor_id' => ['nullable', 'exists:users,id'],
            'catatan_servis' => ['nullable', 'string', 'max:500'],
        ], [
            'service_id.required' => 'Pilih jenis layanan servis.',
            'no_polisi.required_if' => 'Nomor polisi kendaraan wajib diisi.',
            'pelanggan_nama.required_if' => 'Nama pelanggan wajib diisi.',
            'merk.required_if' => 'Merek kendaraan wajib diisi.',
            'model_kendaraan.required_if' => 'Model kendaraan wajib diisi.',
            'tahun.required_if' => 'Tahun kendaraan wajib diisi.',
        ]);

        // Tentukan kendaraan yang digunakan
        if ($validated['mode'] === 'new') {
            // Cari atau buat user pelanggan walk-in
            $pelangganUser = User::firstOrCreate(
                ['email' => 'walkin_'.strtolower(str_replace(' ', '', $validated['pelanggan_nama'])).'@bengkel.local'],
                [
                    'name' => $validated['pelanggan_nama'],
                    'password' => bcrypt('password123'),
                    'role' => 'pelanggan',
                ]
            );

            // Daftarkan kendaraan baru
            $kendaraan = Kendaraan::create([
                'user_id' => $pelangganUser->id,
                'no_polisi' => strtoupper(trim($validated['no_polisi'])),
                'merk' => $validated['merk'],
                'model' => $validated['model_kendaraan'],
                'tahun' => $validated['tahun'],
            ]);
        } else {
            $kendaraan = Kendaraan::findOrFail($validated['kendaraan_id']);
        }

        // Generate nomor antrean otomatis (A-001, A-002, ...)
        $todayCount = Queue::whereDate('created_at', today())->count();
        $nomorAntrean = 'A-'.str_pad($todayCount + 1, 3, '0', STR_PAD_LEFT);

        Queue::create([
            'nomor_antrean' => $nomorAntrean,
            'kendaraan_id' => $kendaraan->id,
            'service_id' => $validated['service_id'],
            'montor_id' => $validated['montor_id'] ?? null,
            'status' => 'waiting',
            'catatan_servis' => $validated['catatan_servis'] ?? null,
        ]);

        return redirect()
            ->route('petugas.dashboard')
            ->with('success', "Antrean {$nomorAntrean} berhasil didaftarkan untuk kendaraan {$kendaraan->no_polisi}!");
    }

    /**
     * Update Status Antrean oleh Petugas.
     */
    public function updateStatus(Request $request, Queue $queue): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:waiting,checking,in_service,completed,cancelled'],
            'montor_id' => ['nullable', 'exists:users,id'],
            'catatan_servis' => ['nullable', 'string', 'max:500'],
        ]);

        $queue->update($validated);

        $statusLabel = match ($validated['status']) {
            'waiting' => 'Menunggu',
            'checking' => 'Pengecekan',
            'in_service' => 'Dalam Servis',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $validated['status'],
        };

        return redirect()
            ->route('petugas.dashboard')
            ->with('success', "Status antrean {$queue->nomor_antrean} diperbarui menjadi '{$statusLabel}'.");
    }

    /**
     * Detail & Edit Antrean.
     */
    public function show(Queue $queue): View
    {
        $queue->load(['kendaraan.user', 'service', 'montor', 'transaction']);
        $services = Service::orderBy('nama_layanan')->get();
        $montirs = User::where('role', 'montor')->orderBy('name')->get();

        return view('petugas.show', compact('queue', 'services', 'montirs'));
    }
}
