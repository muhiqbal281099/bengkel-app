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

        // Antrean aktif yang ditugaskan ke montir ini (hari ini)
        $activeQueues = Queue::with(['kendaraan.user', 'service'])
            ->where('montor_id', $montirId)
            ->whereDate('created_at', today())
            ->whereIn('status', ['waiting', 'checking', 'in_service'])
            ->orderByRaw("CASE status WHEN 'in_service' THEN 1 WHEN 'checking' THEN 2 WHEN 'waiting' THEN 3 END")
            ->get();

        // Antrean yang sudah selesai hari ini
        $completedToday = Queue::where('montor_id', $montirId)
            ->whereDate('created_at', today())
            ->where('status', 'completed')
            ->count();

        // Riwayat 7 hari terakhir
        $history = Queue::with(['kendaraan.user', 'service'])
            ->where('montor_id', $montirId)
            ->where('status', 'completed')
            ->latest()
            ->take(20)
            ->get();

        return view('montir.dashboard', compact('activeQueues', 'completedToday', 'history'));
    }

    /**
     * Update Status Servis oleh Montir.
     */
    public function updateStatus(Request $request, Queue $queue): RedirectResponse
    {
        // Pastikan montir hanya bisa update antrean yang ditugaskan padanya
        if ($queue->montor_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah antrean ini.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:checking,in_service,completed'],
            'catatan_servis' => ['nullable', 'string', 'max:1000'],
        ]);

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
