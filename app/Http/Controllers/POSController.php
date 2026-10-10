<?php

namespace App\Http\Controllers;

use App\Models\BengkelSetting;
use App\Models\Queue;
use App\Models\Sparepart;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class POSController extends Controller
{
    /**
     * Dashboard Kasir: Ringkasan Transaksi, Antrean Siap Bayar, & Omzet Hari Ini.
     */
    public function dashboard(): View
    {
        $todayTransactions = Transaction::whereDate('created_at', today())->get();
        $totalCountToday = $todayTransactions->count();
        $totalIncomeToday = $todayTransactions->sum('total_pembayaran');

        // Antrean hari ini yang belum diproses transaksinya
        $unpaidQueues = Queue::with(['kendaraan.user', 'service', 'montor'])
            ->whereDate('created_at', today())
            ->whereDoesntHave('transaction')
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 WHEN status = 'in_service' THEN 2 WHEN status = 'checking' THEN 3 WHEN status = 'waiting' THEN 4 ELSE 5 END")
            ->orderBy('id', 'asc')
            ->get();

        // 10 transaksi terakhir hari ini
        $recentTransactions = Transaction::with(['queue.kendaraan.user', 'queue.service', 'kasir'])
            ->whereDate('created_at', today())
            ->latest()
            ->take(10)
            ->get();

        return view('kasir.dashboard', compact(
            'totalCountToday',
            'totalIncomeToday',
            'unpaidQueues',
            'recentTransactions'
        ));
    }

    /**
     * Tampilan Point of Sales (POS): Form Pembayaran Antrean & Pemilihan Sparepart.
     */
    public function create(Request $request): View
    {
        // Antrean yang bisa dipilih (yang belum dibayar)
        $unpaidQueues = Queue::with(['kendaraan.user', 'service', 'montor'])
            ->whereDate('created_at', today())
            ->whereDoesntHave('transaction')
            ->orderByRaw("CASE WHEN status = 'completed' THEN 1 WHEN status = 'in_service' THEN 2 WHEN status = 'checking' THEN 3 WHEN status = 'waiting' THEN 4 ELSE 5 END")
            ->orderBy('id', 'asc')
            ->get();

        // Antrean yang sedang aktif dipilih (jika ada param queue_id)
        $selectedQueue = null;
        if ($request->filled('queue_id')) {
            $selectedQueue = Queue::with(['kendaraan.user', 'service', 'montor'])
                ->find($request->queue_id);
        }

        // Daftar suku cadang yang tersedia stoknya
        $spareparts = Sparepart::where('stok', '>', 0)
            ->orderBy('nama_barang', 'asc')
            ->get();

        return view('kasir.pos', compact('unpaidQueues', 'selectedQueue', 'spareparts'));
    }

    /**
     * Proses Simpan Transaksi Kasir & Potong Stok Sparepart.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'queue_id' => ['required', 'exists:queues,id'],
            'metode_pembayaran' => ['required', 'in:cash,transfer,qris'],
            'nominal_bayar' => ['nullable', 'numeric', 'min:0'],
            'items' => ['nullable', 'array'],
            'items.*.sparepart_id' => ['required_with:items', 'exists:spareparts,id'],
            'items.*.qty' => ['required_with:items', 'integer', 'min:1'],
        ], [
            'queue_id.required' => 'Pilih antrean kendaraan yang akan diproses pembayarannya.',
            'metode_pembayaran.required' => 'Pilih metode pembayaran.',
        ]);

        $queue = Queue::with('service')->findOrFail($validated['queue_id']);

        // Pastikan antrean belum pernah dibayar sebelumnya
        if ($queue->transaction()->exists()) {
            return back()->with('error', 'Antrean ini sudah selesai dibayar sebelumnya!');
        }

        $biayaJasa = $queue->service->biaya ?? 0;
        $totalSparepart = 0;
        $itemsToProcess = [];

        // Validasi stok suku cadang
        if (! empty($validated['items'])) {
            foreach ($validated['items'] as $itemData) {
                $sp = Sparepart::findOrFail($itemData['sparepart_id']);
                $qty = (int) $itemData['qty'];

                if ($sp->stok < $qty) {
                    return back()->with('error', "Stok untuk '{$sp->nama_barang}' tidak mencukupi! Tersisa: {$sp->stok} unit, diminta: {$qty} unit.");
                }

                $subtotal = $sp->harga_jual * $qty;
                $totalSparepart += $subtotal;

                $itemsToProcess[] = [
                    'sparepart' => $sp,
                    'qty' => $qty,
                    'subtotal' => $subtotal,
                ];
            }
        }

        $grandTotal = $biayaJasa + $totalSparepart;

        // Validasi uang tunai
        if ($validated['metode_pembayaran'] === 'cash') {
            $nominalBayar = (float) ($validated['nominal_bayar'] ?? 0);
            if ($nominalBayar < $grandTotal) {
                return back()->with('error', 'Nominal uang tunai yang diterima kurang dari total tagihan!');
            }
        }

        // Eksekusi transaksi database secara atomik
        $transaction = DB::transaction(function () use ($queue, $biayaJasa, $totalSparepart, $grandTotal, $validated, $itemsToProcess) {
            // 1. Simpan Transaksi Utama
            $trans = Transaction::create([
                'queue_id' => $queue->id,
                'kasir_id' => auth()->id(),
                'total_biaya_jasa' => $biayaJasa,
                'total_biaya_sparepart' => $totalSparepart,
                'total_pembayaran' => $grandTotal,
                'metode_pembayaran' => $validated['metode_pembayaran'],
            ]);

            // 2. Simpan Detail Suku Cadang & Kurangi Stok
            foreach ($itemsToProcess as $item) {
                TransactionDetail::create([
                    'transaction_id' => $trans->id,
                    'sparepart_id' => $item['sparepart']->id,
                    'jumlah' => $item['qty'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Kurangi stok barang di gudang
                $item['sparepart']->decrement('stok', $item['qty']);
            }

            // 3. Update status antrean menjadi 'completed' (selesai lunas)
            $queue->update(['status' => 'completed']);

            return $trans;
        });

        return redirect()
            ->route('kasir.transactions.receipt', $transaction)
            ->with('success', 'Transaksi berhasil disimpan dan pembayaran lunas!');
    }

    /**
     * Tampilan Cetak Struk / Nota Pembayaran Resmi.
     */
    public function receipt(Transaction $transaction): View
    {
        $transaction->load([
            'queue.kendaraan.user',
            'queue.service',
            'queue.montor',
            'details.sparepart',
            'kasir',
        ]);

        $setting = BengkelSetting::getSettings();

        return view('kasir.receipt', compact('transaction', 'setting'));
    }

    /**
     * Riwayat Seluruh Transaksi Pembayaran Kasir.
     */
    public function index(Request $request): View
    {
        $query = Transaction::with(['queue.kendaraan.user', 'queue.service', 'kasir']);

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        } else {
            // Default: hari ini
            $query->whereDate('created_at', today());
        }

        // Filter pencarian
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->whereHas('queue.kendaraan', function ($q) use ($keyword) {
                $q->where('no_polisi', 'like', "%{$keyword}%");
            })->orWhereHas('queue', function ($q) use ($keyword) {
                $q->where('nomor_antrean', 'like', "%{$keyword}%");
            });
        }

        // Filter metode
        if ($request->filled('metode')) {
            $query->where('metode_pembayaran', $request->metode);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();
        $totalOmzet = $transactions->sum('total_pembayaran');

        return view('kasir.transactions.index', compact('transactions', 'totalOmzet'));
    }
}
