@extends('layouts.admin')

@section('title', 'Katalog Suku Cadang')

@section('styles')
<style>
    .filter-card {
        background: var(--glass-card);
        border: 1px solid var(--glass-border);
        border-radius: 16px;
        padding: 1.2rem 1.5rem;
        margin-bottom: 1.8rem;
    }

    .filter-form {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .search-box {
        flex: 2;
        min-width: 250px;
        position: relative;
    }

    .search-box input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 2.5rem;
        background: var(--input-bg);
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-main);
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .search-box input:focus {
        border-color: var(--primary);
    }

    .search-icon {
        position: absolute;
        left: 0.9rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-dim);
        font-size: 1rem;
    }

    .select-box {
        flex: 1;
        min-width: 160px;
    }

    .select-box select {
        width: 100%;
        padding: 0.75rem 1rem;
        background: var(--input-bg);
        border: 1.5px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-main);
        font-size: 0.95rem;
        outline: none;
        cursor: pointer;
    }

    .status-tabs {
        display: flex;
        gap: 0.4rem;
        background: var(--surface-secondary);
        padding: 0.3rem;
        border-radius: 12px;
        border: 1px solid var(--border-color);
    }

    .status-tab {
        padding: 0.45rem 0.9rem;
        border-radius: 9px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--text-muted);
        transition: all 0.2s ease;
    }

    .status-tab:hover {
        color: var(--text-main);
    }

    .status-tab.active {
        background: var(--primary);
        color: #fff;
    }

    .action-group {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* STOCK BAR */
    .stock-indicator {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        min-width: 100px;
    }

    .progress-bar {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 99px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        border-radius: 99px;
        transition: width 0.3s ease;
    }

    .pagination-wrapper {
        margin-top: 1.8rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .pagination-nav {
        display: flex;
        gap: 0.4rem;
    }

    .pagination-nav a, .pagination-nav span {
        padding: 0.5rem 0.9rem;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-main);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.88rem;
        border: 1px solid var(--glass-border);
    }

    .pagination-nav span.current {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }
</style>
@endsection

@section('content')

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-title">
            <h2>Katalog & Manajemen Suku Cadang</h2>
            <p>Kelola daftar inventaris suku cadang, update harga, serta kontrol keluar-masuk stok barang.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.spareparts.create') }}" class="btn btn-primary">
                ➕ Tambah Suku Cadang Baru
            </a>
        </div>
    </div>

    <!-- FILTER & SEARCH BAR -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.spareparts.index') }}" class="filter-form">
            
            <!-- Search -->
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input 
                    type="text" 
                    name="q" 
                    placeholder="Cari kode atau nama suku cadang..." 
                    value="{{ request('q') }}">
            </div>

            <!-- Kategori -->
            <div class="select-box">
                <select name="kategori" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($allCategories as $category)
                        <option value="{{ $category }}" {{ request('kategori') === $category ? 'selected' : '' }}>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Urutan -->
            <div class="select-box">
                <select name="sort" onchange="this.form.submit()">
                    <option value="nama" {{ request('sort') === 'nama' ? 'selected' : '' }}>Nama (A - Z)</option>
                    <option value="terbaru" {{ request('sort') === 'terbaru' ? 'selected' : '' }}>Terbaru Ditambahkan</option>
                    <option value="stok_asc" {{ request('sort') === 'stok_asc' ? 'selected' : '' }}>Stok Terendah</option>
                    <option value="stok_desc" {{ request('sort') === 'stok_desc' ? 'selected' : '' }}>Stok Tertinggi</option>
                    <option value="harga_asc" {{ request('sort') === 'harga_asc' ? 'selected' : '' }}>Harga Termurah</option>
                    <option value="harga_desc" {{ request('sort') === 'harga_desc' ? 'selected' : '' }}>Harga Termahal</option>
                </select>
            </div>

            <!-- Status Tabs -->
            <div class="status-tabs">
                <a href="{{ route('admin.spareparts.index', array_merge(request()->except('status'), [])) }}" 
                   class="status-tab {{ !request('status') ? 'active' : '' }}">
                    Semua
                </a>
                <a href="{{ route('admin.spareparts.index', array_merge(request()->except('status'), ['status' => 'safe'])) }}" 
                   class="status-tab {{ request('status') === 'safe' ? 'active' : '' }}">
                    Aman
                </a>
                <a href="{{ route('admin.spareparts.index', array_merge(request()->except('status'), ['status' => 'low'])) }}" 
                   class="status-tab {{ request('status') === 'low' ? 'active' : '' }}">
                    Menipis (≤5)
                </a>
                <a href="{{ route('admin.spareparts.index', array_merge(request()->except('status'), ['status' => 'out'])) }}" 
                   class="status-tab {{ request('status') === 'out' ? 'active' : '' }}">
                    Habis (0)
                </a>
            </div>

            <button type="submit" class="btn btn-secondary btn-sm" style="padding: 0.75rem 1.2rem;">
                Terapkan
            </button>
            @if(request()->hasAny(['q', 'kategori', 'sort', 'status']))
                <a href="{{ route('admin.spareparts.index') }}" class="btn btn-sm" style="color: var(--text-dim); text-decoration: underline;">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    <!-- DATA TABLE -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Kode Barang</th>
                        <th>Nama Suku Cadang</th>
                        <th>Kategori</th>
                        <th>Harga Jual (Rp)</th>
                        <th style="width: 170px;">Status & Jumlah Stok</th>
                        <th style="width: 200px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spareparts as $item)
                        <tr>
                            <td>
                                <code style="color: var(--primary); font-weight: 700; font-size: 0.95rem; background: var(--primary-light); padding: 0.2rem 0.5rem; border-radius: 6px;">
                                    {{ $item->kode_barang }}
                                </code>
                            </td>
                            <td>
                                <strong style="color: var(--text-main); font-size: 0.98rem;">{{ $item->nama_barang }}</strong>
                            </td>
                            <td>
                                <span class="badge badge-category">{{ $item->kategori }}</span>
                            </td>
                            <td>
                                <span style="font-weight: 700; color: var(--success); font-size: 1rem;">
                                    Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <div class="stock-indicator">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        @if($item->stok > 5)
                                            <span class="badge badge-safe">{{ $item->stok }} Unit</span>
                                        @elseif($item->stok > 0)
                                            <span class="badge badge-warning">Tersisa {{ $item->stok }}</span>
                                        @else
                                            <span class="badge badge-danger">Habis</span>
                                        @endif
                                    </div>
                                    <div class="progress-bar">
                                        @php
                                            $percent = min(100, max(0, ($item->stok / 25) * 100));
                                            $barColor = $item->stok > 5 ? '#10b981' : ($item->stok > 0 ? '#f59e0b' : '#ef4444');
                                        @endphp
                                        <div class="progress-fill" style="width: {{ $percent }}%; background: {{ $barColor }};"></div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <div class="action-group" style="justify-content: flex-end;">
                                    <!-- Tombol Restock Cepat -->
                                    <button 
                                        type="button" 
                                        class="btn btn-success btn-sm" 
                                        title="Tambah stok barang masuk"
                                        onclick="openRestockModal('{{ $item->id }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->kode_barang }}', {{ $item->stok }})">
                                        ⚡ Restock
                                    </button>

                                    <!-- Tombol Edit -->
                                    <a 
                                        href="{{ route('admin.spareparts.edit', $item) }}" 
                                        class="btn btn-secondary btn-sm" 
                                        title="Edit data suku cadang">
                                        ✏️ Edit
                                    </a>

                                    <!-- Tombol Hapus -->
                                    <form 
                                        method="POST" 
                                        action="{{ route('admin.spareparts.destroy', $item) }}" 
                                        style="margin: 0;"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus suku cadang {{ addslashes($item->nama_barang) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button 
                                            type="submit" 
                                            class="btn btn-danger btn-sm" 
                                            title="Hapus dari database">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3.5rem 1rem; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🔍</div>
                                <h4 style="color: #e2e8f0; font-size: 1.15rem; margin-bottom: 0.3rem;">Tidak ada suku cadang ditemukan</h4>
                                <p style="font-size: 0.9rem;">Coba sesuaikan kata kunci pencarian atau bersihkan filter yang aktif.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION -->
    @if($spareparts->hasPages())
        <div class="pagination-wrapper">
            <div>
                Menampilkan {{ $spareparts->firstItem() ?? 0 }} - {{ $spareparts->lastItem() ?? 0 }} dari {{ $spareparts->total() }} suku cadang
            </div>
            <div class="pagination-nav">
                @if ($spareparts->onFirstPage())
                    <span style="opacity: 0.5;">&laquo; Sebelumnya</span>
                @else
                    <a href="{{ $spareparts->previousPageUrl() }}">&laquo; Sebelumnya</a>
                @endif

                @foreach ($spareparts->getUrlRange(max(1, $spareparts->currentPage() - 2), min($spareparts->lastPage(), $spareparts->currentPage() + 2)) as $page => $url)
                    @if ($page == $spareparts->currentPage())
                        <span class="current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if ($spareparts->hasMorePages())
                    <a href="{{ $spareparts->nextPageUrl() }}">Selanjutnya &raquo;</a>
                @else
                    <span style="opacity: 0.5;">Selanjutnya &raquo;</span>
                @endif
            </div>
        </div>
    @endif

    <!-- MODAL RESTOCK CEPAT -->
    <div class="modal-overlay" id="restockModal">
        <div class="modal-box">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.2rem;">
                <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--text-main);">⚡ Penyesuaian / Restock Stok</h3>
                <button type="button" class="alert-dismiss" onclick="closeRestockModal()">✕</button>
            </div>

            <form id="restockForm" method="POST" action="">
                @csrf

                <div style="margin-bottom: 1.2rem; background: var(--surface-secondary); padding: 1rem; border-radius: 12px; border: 1px solid var(--border-color);">
                    <div style="font-size: 0.8rem; color: var(--text-dim); text-transform: uppercase; font-weight: 700;">Barang yang dipilih:</div>
                    <div id="modalItemName" style="font-size: 1.05rem; font-weight: 700; color: var(--primary); margin-top: 0.2rem;"></div>
                    <div style="display: flex; gap: 1rem; margin-top: 0.4rem; font-size: 0.85rem;">
                        <span style="color: var(--primary);">Kode: <strong id="modalItemCode"></strong></span>
                        <span style="color: var(--warning);">Stok Saat Ini: <strong id="modalCurrentStock"></strong></span>
                    </div>
                </div>

                <div style="margin-bottom: 1.2rem;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem;">
                        Jenis Penyesuaian
                    </label>
                    <select 
                        name="adjustment_type" 
                        id="modalAdjustmentType"
                        style="width: 100%; padding: 0.85rem 1.2rem; background: var(--input-bg); border: 1.5px solid var(--border-color); border-radius: 12px; color: var(--text-main); font-size: 0.95rem;">
                        <option value="add">➕ Tambah Stok Masuk (Restock Supplier)</option>
                        <option value="subtract">➖ Kurangi Stok (Barang Rusak / Retur)</option>
                        <option value="set">🔄 Setel Jumlah Stok Fisik Langsung (Opname)</option>
                    </select>
                </div>

                <div style="margin-bottom: 1.4rem;">
                    <label style="display: block; font-size: 0.9rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem;">
                        Jumlah Unit
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
                    <button type="submit" class="btn btn-success">✅ Simpan Penyesuaian</button>
                </div>
            </form>
        </div>
    </div>

@endsection

@section('scripts')
<script>
    function openRestockModal(id, name, code, currentStock) {
        const baseUrl = "{{ url('admin/spareparts') }}";
        document.getElementById('restockForm').action = baseUrl + '/' + id + '/adjust-stock';
        document.getElementById('modalItemName').textContent = name;
        document.getElementById('modalItemCode').textContent = code;
        document.getElementById('modalCurrentStock').textContent = currentStock + ' Unit';
        document.getElementById('restockAmount').value = 10;
        document.getElementById('modalAdjustmentType').value = 'add';
        document.getElementById('restockModal').classList.add('active');
        document.getElementById('restockAmount').focus();
    }

    function closeRestockModal() {
        document.getElementById('restockModal').classList.remove('active');
    }

    document.getElementById('restockModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeRestockModal();
        }
    });
</script>
@endsection
