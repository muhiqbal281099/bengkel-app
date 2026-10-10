<?php

namespace App\Http\Controllers;

use App\Models\BengkelSetting;
use App\Models\Queue;
use Illuminate\View\View;

class QueueController extends Controller
{
    /**
     * Tampilan Layar Monitor Antrean Publik.
     */
    public function publicDisplay(): View
    {
        $setting = BengkelSetting::getSettings();

        // Ambil antrean hari ini yang aktif & selesai servis (waiting, checking, in_service, completed)
        $queues = Queue::with(['kendaraan', 'service', 'montor'])
            ->whereDate('created_at', today())
            ->whereIn('status', ['waiting', 'checking', 'in_service', 'completed'])
            ->orderByRaw("CASE status WHEN 'in_service' THEN 1 WHEN 'checking' THEN 2 WHEN 'waiting' THEN 3 WHEN 'completed' THEN 4 ELSE 5 END")
            ->orderBy('id', 'asc')
            ->get();

        return view('public_queue', compact('queues', 'setting'));
    }
}
