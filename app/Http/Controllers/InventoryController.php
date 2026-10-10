<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Sparepart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class InventoryController extends Controller
{
    /**
     * Tampilan Dashboard Ringkasan Inventaris Gudang.
     */
    public function dashboard(): View
    {
        $totalItems = Sparepart::count();
        $totalStockUnits = Sparepart::sum('stok');
        $totalValuation = Sparepart::selectRaw('SUM(stok * harga_jual) as val')->value('val') ?? 0;

        $lowStockItems = Sparepart::where('stok', '<=', 5)
            ->orderBy('stok', 'asc')
            ->get();
        $lowStockCount = $lowStockItems->count();

        $totalServices = Service::count();

        $recentSpareparts = Sparepart::latest()
            ->take(5)
            ->get();

        $categories = Sparepart::select('kategori', DB::raw('count(*) as count'), DB::raw('sum(stok) as total_stok'))
            ->groupBy('kategori')
            ->get();

        return view('admin.dashboard', compact(
            'totalItems',
            'totalStockUnits',
            'totalValuation',
            'lowStockItems',
            'lowStockCount',
            'totalServices',
            'recentSpareparts',
            'categories'
        ));
    }

    /**
     * Tampilan Katalog / Daftar Suku Cadang (dengan Search & Filter).
     */
    public function index(Request $request): View
    {
        $query = Sparepart::query();

        // Filter kata kunci pencarian
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($q) use ($keyword) {
                $q->where('nama_barang', 'like', "%{$keyword}%")
                    ->orWhere('kode_barang', 'like', "%{$keyword}%");
            });
        }

        // Filter kategori
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter status stok
        if ($request->filled('status')) {
            if ($request->status === 'safe') {
                $query->where('stok', '>', 5);
            } elseif ($request->status === 'low') {
                $query->where('stok', '>', 0)->where('stok', '<=', 5);
            } elseif ($request->status === 'out') {
                $query->where('stok', '<=', 0);
            }
        }

        // Sorting
        $sort = $request->get('sort', 'nama');
        if ($sort === 'stok_asc') {
            $query->orderBy('stok', 'asc');
        } elseif ($sort === 'stok_desc') {
            $query->orderBy('stok', 'desc');
        } elseif ($sort === 'harga_asc') {
            $query->orderBy('harga_jual', 'asc');
        } elseif ($sort === 'harga_desc') {
            $query->orderBy('harga_jual', 'desc');
        } elseif ($sort === 'terbaru') {
            $query->latest();
        } else {
            $query->orderBy('nama_barang', 'asc');
        }

        $spareparts = $query->paginate(12)->withQueryString();

        // Daftar kategori untuk dropdown
        $allCategories = Sparepart::select('kategori')->distinct()->pluck('kategori');

        return view('admin.spareparts.index', compact('spareparts', 'allCategories'));
    }

    /**
     * Form Tambah Suku Cadang Baru.
     */
    public function create(): View
    {
        $existingCategories = Sparepart::select('kategori')->distinct()->pluck('kategori');

        // Buat estimasi kode barang otomatis berikutnya
        $lastId = Sparepart::max('id') ?? 0;
        $suggestedCode = 'SP-'.str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        return view('admin.spareparts.create', compact('existingCategories', 'suggestedCode'));
    }

    /**
     * Simpan Suku Cadang Baru ke Database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', 'unique:spareparts,kode_barang'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
        ], [
            'kode_barang.unique' => 'Kode suku cadang sudah digunakan oleh barang lain.',
            'harga_jual.min' => 'Harga jual tidak boleh kurang dari 0.',
            'stok.min' => 'Stok minimal adalah 0.',
        ]);

        $sparepart = Sparepart::create($validated);

        return redirect()
            ->route('admin.spareparts.index')
            ->with('success', "Suku cadang '{$sparepart->nama_barang}' ({$sparepart->kode_barang}) berhasil ditambahkan ke inventaris!");
    }

    /**
     * Form Ubah Data Suku Cadang.
     */
    public function edit(Sparepart $sparepart): View
    {
        $existingCategories = Sparepart::select('kategori')->distinct()->pluck('kategori');

        return view('admin.spareparts.edit', compact('sparepart', 'existingCategories'));
    }

    /**
     * Perbarui Data Suku Cadang.
     */
    public function update(Request $request, Sparepart $sparepart): RedirectResponse
    {
        $validated = $request->validate([
            'kode_barang' => ['required', 'string', 'max:50', 'unique:spareparts,kode_barang,'.$sparepart->id],
            'nama_barang' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:100'],
            'harga_jual' => ['required', 'numeric', 'min:0'],
            'stok' => ['required', 'integer', 'min:0'],
        ], [
            'kode_barang.unique' => 'Kode suku cadang sudah digunakan oleh barang lain.',
            'harga_jual.min' => 'Harga jual tidak boleh kurang dari 0.',
            'stok.min' => 'Stok minimal adalah 0.',
        ]);

        $sparepart->update($validated);

        return redirect()
            ->route('admin.spareparts.index')
            ->with('success', "Data suku cadang '{$sparepart->nama_barang}' berhasil diperbarui!");
    }

    /**
     * Penyesuaian / Restock Stok Cepat (Quick Stock Adjustment).
     */
    public function adjustStock(Request $request, Sparepart $sparepart): RedirectResponse
    {
        $validated = $request->validate([
            'adjustment_type' => ['required', 'in:add,subtract,set'],
            'amount' => ['required', 'integer', 'min:1'],
        ]);

        $prevStock = $sparepart->stok;

        if ($validated['adjustment_type'] === 'add') {
            $sparepart->increment('stok', $validated['amount']);
            $msg = "Berhasil menambah {$validated['amount']} unit stok untuk {$sparepart->nama_barang}.";
        } elseif ($validated['adjustment_type'] === 'subtract') {
            $newStock = max(0, $sparepart->stok - $validated['amount']);
            $sparepart->update(['stok' => $newStock]);
            $msg = "Berhasil mengurangi stok {$sparepart->nama_barang} sebanyak {$validated['amount']} unit.";
        } else {
            $sparepart->update(['stok' => $validated['amount']]);
            $msg = "Stok {$sparepart->nama_barang} berhasil diubah dari {$prevStock} menjadi {$validated['amount']} unit.";
        }

        return back()->with('success', $msg." Stok saat ini: {$sparepart->fresh()->stok} unit.");
    }

    /**
     * Hapus Suku Cadang dari Inventaris.
     */
    public function destroy(Sparepart $sparepart): RedirectResponse
    {
        // Cegah penghapusan jika sudah pernah digunakan dalam transaksi
        if ($sparepart->transactionDetails()->exists()) {
            return back()->with('error', "Gagal menghapus: Suku cadang '{$sparepart->nama_barang}' telah tercatat dalam riwayat transaksi kasir.");
        }

        $nama = $sparepart->nama_barang;
        $sparepart->delete();

        return redirect()
            ->route('admin.spareparts.index')
            ->with('success', "Suku cadang '{$nama}' berhasil dihapus dari sistem.");
    }
}
