@extends('layouts.admin')

@section('title', 'Dashboard Inventaris')

@section('styles')
<style>
    /* STATS GRID */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: var(--glass-card);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        border-color: var(--glass-border-hover);
        box-shadow: 0 12px 25px -10px rgba(0, 0, 0, 0.5);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
    }

    .stat-card.blue::before { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
    .stat-card.green::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .stat-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #c084fc); }
    .stat-card.amber::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.8rem;
    }

    .stat-title {
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .stat-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .stat-icon.blue { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
    .stat-icon.green { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .stat-icon.purple { background: rgba(139, 92, 246, 0.15); color: #c084fc; }
    .stat-icon.amber { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        color: var(--text-main);
        line-height: 1.1;
    }

    .stat-sub {
        margin-top: 0.4rem;
        font-size: 0.82rem;
        color: var(--text-dim);
    }

    /* TWO COLUMN LAYOUT */
    .dashboard-layout {
        display: grid;
        grid-template-columns: 1.7fr 1fr;
        gap: 1.8rem;
    }

    @media (max-width: 1050px) {
        .dashboard-layout { grid-template-columns: 1fr; }
    }

    .warning-banner {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(245, 158, 11, 0.1));
        border: 1px solid rgba(239, 68, 68, 0.35);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .warning-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .warning-title {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        font-weight: 700;
        color: var(--danger);
        font-size: 1.1rem;
    }

    /* CATEGORY PILL LIST */
    .category-list {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }

    .category-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.85rem 1rem;
        background: var(--surface-secondary);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        transition: background 0.2s ease;
    }

    .category-item:hover {
        background: var(--surface);
    }

    .category-name {
        font-weight: 600;
        color: var(--text-main);
    }

    .category-meta {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        font-size: 0.85rem;
        color: var(--text-muted);
    }
</style>
@endsection

@section('content')

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-title">
            <h2>Ringkasan Gudang & Inventaris</h2>
            <p>Pantau pergerakan stok suku cadang, valuasi aset, dan kesiapan operasional bengkel.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.spareparts.create') }}" class="btn btn-primary">
                ➕ Tambah Suku Cadang
            </a>
            <a href="{{ route('admin.services.create') }}" class="btn btn-secondary">
                ⚙️ Tambah Jasa Servis
            </a>
        </div>
    </div>

    <!-- METRICS GRID -->
    <div class="stats-grid">
        <div class="stat-card blue">
            <div class="stat-header">
                <span class="stat-title">Katalog Suku Cadang</span>
                <div class="stat-icon blue">📦</div>
            </div>
            <div class="stat-value">{{ $totalItems }}</div>
            <div class="stat-sub">Item sparepart terdaftar</div>
        </div>

        <div class="stat-card green">
            <div class="stat-header">
                <span class="stat-title">Total Stok Fisik</span>
                <div class="stat-icon green">🔢</div>
            </div>
            <div class="stat-value">{{ number_format($totalStockUnits, 0, ',', '.') }}</div>
            <div class="stat-sub">Unit suku cadang siap pakai</div>
        </div>

        <div class="stat-card purple">
            <div class="stat-header">
                <span class="stat-title">Valuasi Inventaris</span>
                <div class="stat-icon purple">💰</div>
            </div>
            <div class="stat-value" style="font-size: 1.65rem;">Rp {{ number_format($totalValuation, 0, ',', '.') }}</div>
            <div class="stat-sub">Estimasi nilai total aset barang</div>
        </div>

        <div class="stat-card amber">
            <div class="stat-header">
                <span class="stat-title">Perlu Perhatian</span>
                <div class="stat-icon amber">⚠️</div>
            </div>
            <div class="stat-value" style="color: {{ $lowStockCount > 0 ? '#f87171' : '#34d399' }};">
                {{ $lowStockCount }}
            </div>
            <div class="stat-sub">{{ $lowStockCount > 0 ? 'Item stok kritis / habis' : 'Semua stok dalam batas aman' }}</div>
        </div>
    </div>

    <!-- LOW STOCK WARNING SECTION -->
    @if($lowStockCount > 0)
        <div class="warning-banner">
            <div class="warning-header">
                <div class="warning-title">
                    <span>🚨</span>
                    <span>Peringatan: Terdapat {{ $lowStockCount }} suku cadang dengan stok menipis (≤ 5 unit)!</span>
                </div>
                <a href="{{ route('admin.spareparts.index', ['status' => 'low']) }}" class="btn btn-warning btn-sm">
                    Lihat Semua di Filter Stok
                </a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Suku Cadang</th>
                            <th>Kategori</th>
                            <th>Sisa Stok</th>
                            <th>Harga Jual</th>
                            <th style="text-align: right;">Aksi Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockItems as $item)
                            <tr>
                                <td><code style="color: #60a5fa; font-weight: 700;">{{ $item->kode_barang }}</code></td>
                                <td style="font-weight: 600;">{{ $item->nama_barang }}</td>
                                <td><span class="badge badge-category">{{ $item->kategori }}</span></td>
                                <td>
                                    @if($item->stok <= 0)
                                        <span class="badge badge-danger">Habis (0)</span>
                                    @else
                                        <span class="badge badge-warning">Kritis ({{ $item->stok }})</span>
                                    @endif
                                </td>
                                <td>Rp {{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td style="text-align: right;">
                                    <button 
                                        type="button" 
                                        class="btn btn-success btn-sm"
                                        onclick="openRestockModal('{{ $item->id }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->kode_barang }}', {{ $item->stok }})">
                                        ⚡ Restock Cepat
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- MAIN TWO-COLUMN DASHBOARD -->
    <div class="dashboard-layout">
        
        <!-- KOLOM KIRI: SUKU CADANG TERBARU -->
        <div class="card">
            <div class="card-header">
                <h3>📦 Suku Cadang Baru Ditambahkan</h3>
                <a href="{{ route('admin.spareparts.index') }}" class="btn btn-secondary btn-sm">
                    Katalog Lengkap →
                </a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Barang</th>
                            <th>Kategori</th>
                            <th>Harga Jual</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSpareparts as $sparepart)
                            <tr>
                                <td><code style="color: var(--primary); font-weight: 700; background: var(--primary-light); padding: 0.15rem 0.45rem; border-radius: 6px;">{{ $sparepart->kode_barang }}</code></td>
                                <td style="font-weight: 600;">{{ $sparepart->nama_barang }}</td>
                                <td><span class="badge badge-category">{{ $sparepart->kategori }}</span></td>
                                <td>Rp {{ number_format($sparepart->harga_jual, 0, ',', '.') }}</td>
                                <td>
                                    @if($sparepart->stok > 5)
                                        <span class="badge badge-safe">{{ $sparepart->stok }} Unit</span>
                                    @elseif($sparepart->stok > 0)
                                        <span class="badge badge-warning">{{ $sparepart->stok }} Unit</span>
                                    @else
                                        <span class="badge badge-danger">Habis</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                    Belum ada suku cadang yang terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- KOLOM KANAN: KATEGORI & QUICK OVERVIEW -->
        <div style="display: flex; flex-direction: column; gap: 1.8rem;">
            
            <!-- DISTRIBUSI KATEGORI -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h3>📂 Kategori Suku Cadang</h3>
                    <span class="badge badge-category">{{ $categories->count() }} Kategori</span>
                </div>

                <div class="category-list">
                    @forelse($categories as $cat)
                        <div class="category-item">
                            <span class="category-name">{{ $cat->kategori }}</span>
                            <div class="category-meta">
                                <span>{{ $cat->count }} jenis</span>
                                <span class="badge badge-safe">{{ $cat->total_stok ?? 0 }} pcs</span>
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--text-muted); font-size: 0.9rem;">Belum ada kategori suku cadang.</p>
                    @endforelse
                </div>
            </div>

            <!-- JASA SERVIS SHORTCUT -->
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h3>⚙️ Master Jasa Servis</h3>
                    <span class="badge badge-category">{{ $totalServices }} Layanan</span>
                </div>
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.2rem;">
                    Kelola daftar tindakan servis, estimasi waktu pengerjaan montir, dan tarif jasa servis bengkel.
                </p>
                <div style="display: flex; gap: 0.8rem;">
                    <a href="{{ route('admin.services.index') }}" class="btn btn-secondary btn-sm" style="flex: 1;">
                        Daftar Jasa Servis
                    </a>
                    <a href="{{ route('admin.services.create') }}" class="btn btn-primary btn-sm" style="flex: 1;">
                        ➕ Tambah Jasa
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- MODAL RESTOCK CEPAT -->
    <div class="modal-overlay" id="restockModal">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem;">
                <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--text-main);">⚡ Restock Cepat Suku Cadang</h3>
                <button type="button" class="alert-dismiss" onclick="closeRestockModal()">✕</button>
            </div>

            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.4rem;">
                Tambahkan stok barang yang baru masuk dari supplier ke sistem inventaris.
            </p>

            <form id="restockForm" method="POST" action="">
                @csrf
                <input type="hidden" name="adjustment_type" value="add">

                <div style="margin-bottom: 1.2rem; background: var(--surface-secondary); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <div style="font-size: 0.8rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700;">Barang yang dipilih:</div>
                    <div id="modalItemName" style="font-size: 1.05rem; font-weight: 700; color: var(--primary); margin-top: 0.2rem;"></div>
                    <div style="display: flex; gap: 1rem; margin-top: 0.4rem; font-size: 0.85rem;">
                        <span style="color: var(--primary);">Kode: <strong id="modalItemCode"></strong></span>
                        <span style="color: var(--warning);">Stok Saat Ini: <strong id="modalCurrentStock"></strong></span>
                    </div>
                </div>

                <div style="margin-bottom: 1.4rem;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem;">
                        Jumlah Unit yang Ditambahkan
                    </label>
                    <input 
                        type="number" 
                        name="amount" 
                        id="restockAmount" 
                        min="1" 
                        value="10" 
                        required
                        style="width: 100%; padding: 0.85rem 1.2rem; background: var(--input-bg); border: 1.5px solid var(--border-color); border-radius: 12px; color: var(--text-main); font-size: 1.1rem; font-weight: 700;">
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.8rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeRestockModal()">Batal</button>
                    <button type="submit" class="btn btn-success">✅ Simpan Tambahan Stok</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openRestockModal(id, name, code, currentStock) {
        // Buat URL action dinamis berbasis route helper
        const baseUrl = "{{ url('admin/spareparts') }}";
        document.getElementById('restockForm').action = baseUrl + '/' + id + '/adjust-stock';
        document.getElementById('modalItemName').textContent = name;
        document.getElementById('modalItemCode').textContent = code;
        document.getElementById('modalCurrentStock').textContent = currentStock + ' Unit';
        document.getElementById('restockAmount').value = 10;
        document.getElementById('restockModal').classList.add('active');
        document.getElementById('restockAmount').focus();
    }

    function closeRestockModal() {
        document.getElementById('restockModal').classList.remove('active');
    }

    // Close modal when clicked outside
    document.getElementById('restockModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRestockModal();
        }
    });
</script>
@endsection
