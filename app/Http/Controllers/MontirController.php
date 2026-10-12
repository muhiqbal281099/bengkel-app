<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MontirController extends Controller
{
    /**
     * Dashboard Montir: Antrean yang ditugaskan padanya.
     */
    public function dashboard(): View
    {
        $montirId = auth()->id();

        // Antrean aktif yang ditugaskan ke montir ini (atau belum ditugaskan mekanik)
        $activeQueues = Queue::with(['kendaraan.user', 'service'])
            ->where(function ($q) use ($montirId) {
                $q->where('montor_id', $montirId)
                    ->orWhereNull('montor_id');
            })
            ->whereIn('status', ['waiting', 'checking', 'in_service'])
            ->orderByRaw("CASE status WHEN 'in_service' THEN 1 WHEN 'checking' THEN 2 WHEN 'waiting' THEN 3 ELSE 4 END")
            ->orderBy('id', 'asc')
            ->get();

        // Antrean yang selesai hari ini oleh montir ini
        $completedToday = Queue::where('montor_id', $montirId)
            ->where('status', 'completed')
            ->where(function ($q) {
                $q->whereDate('updated_at', today())
                    ->orWhereDate('created_at', today());
            })
            ->count();

        // Riwayat servis selesai oleh montir ini (terbaru berdasarkan waktu selesai)
        $history = Queue::with(['kendaraan.user', 'service'])
            ->where('montor_id', $montirId)
            ->where('status', 'completed')
            ->latest('updated_at')
            ->take(20)
            ->get();

        return view('montir.dashboard', compact('activeQueues', 'completedToday', 'history'));
    }

    /**
     * Update Status Servis oleh Montir.
     */
    public function updateStatus(Request $request, Queue $queue): RedirectResponse
    {
        // Pastikan montir hanya bisa update antrean miliknya (atau yang belum memiliki mekanik)
        if ($queue->montor_id && (int) $queue->montor_id !== (int) auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah antrean ini.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:checking,in_service,completed'],
            'catatan_servis' => ['nullable', 'string', 'max:1000'],
        ]);

        // Jika antrean belum memiliki mekanik, otomatis tugaskan ke montir yang memproses
        if (! $queue->montor_id) {
            $validated['montor_id'] = auth()->id();
        }

        $queue->update($validated);

        $statusLabel = match ($validated['status']) {
            'checking' => 'Pengecekan Awal',
            'in_service' => 'Dalam Proses Servis',
            'completed' => 'Servis Selesai',
            default => $validated['status'],
        };

        return redirect()
            ->route('montir.dashboard')
            ->with('success', "Status antrean {$queue->nomor_antrean} diperbarui: {$statusLabel}.");
    }

    /**
     * Detail Antrean untuk Montir (read & update catatan).
     */
    public function show(Queue $queue): View
    {
        $queue->load(['kendaraan.user', 'service']);

        return view('montir.show', compact('queue'));
    }
}
