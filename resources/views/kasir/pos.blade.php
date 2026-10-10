@extends('layouts.kasir')

@section('title', 'Point of Sales / Kasir')

@section('styles')
<style>
    .pos-layout {
        display: grid;
        grid-template-columns: 1.6fr 1.1fr;
        gap: 1.8rem;
    }

    @media (max-width: 1024px) {
        .pos-layout { grid-template-columns: 1fr; }
    }

    .form-group {
        margin-bottom: 1.3rem;
    }

    .form-group label {
        display: block;
        margin-bottom: 0.45rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--text-muted);
    }

    .form-control {
        width: 100%;
        padding: 0.8rem 1.1rem;
        background: var(--input-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        color: var(--text-main);
        font-size: 0.95rem;
        outline: none;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .vehicle-info-card {
        background: var(--surface-secondary);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.2rem;
        margin-bottom: 1.4rem;
    }

    .vehicle-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.8rem;
        font-size: 0.9rem;
    }

    .info-label {
        color: var(--text-dim);
        font-size: 0.82rem;
        font-weight: 600;
    }

    .info-val {
        font-weight: 700;
        color: var(--text-main);
        margin-top: 0.1rem;
    }

    /* SPAREPART ROW */
    .part-row {
        display: grid;
        grid-template-columns: 2.5fr 1fr 1.5fr 40px;
        gap: 0.8rem;
        align-items: center;
        background: var(--surface-secondary);
        border: 1px solid var(--border-color);
        padding: 0.8rem 1rem;
        border-radius: 12px;
        margin-bottom: 0.8rem;
        animation: fadeIn 0.2s ease;
    }

    .btn-remove-part {
        background: rgba(220, 38, 38, 0.1);
        border: 1px solid rgba(220, 38, 38, 0.2);
        color: var(--danger);
        width: 36px;
        height: 36px;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .btn-remove-part:hover {
        background: var(--danger);
        color: #fff;
    }

    /* SUMMARY PANEL */
    .summary-box {
        background: var(--surface);
        border: 2px solid var(--primary-blue);
        border-radius: 18px;
        padding: 1.8rem;
        box-shadow: var(--shadow-lg);
        position: sticky;
        top: 90px;
    }

    .bill-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px dashed var(--border-color);
        font-size: 0.95rem;
    }

    .bill-item.total {
        border-bottom: none;
        border-top: 2px solid var(--border-color);
        margin-top: 0.8rem;
        padding-top: 1rem;
    }

    .grand-total-val {
        font-size: 1.85rem;
        font-weight: 900;
        color: var(--primary);
    }

    /* PAYMENT METHOD TABS */
    .pay-methods {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 0.6rem;
        margin: 1.2rem 0;
    }

    .pay-method-btn {
        padding: 0.75rem 0.5rem;
        border-radius: 10px;
        border: 2px solid var(--border-color);
        background: var(--surface-secondary);
        color: var(--text-main);
        font-weight: 700;
        font-size: 0.88rem;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s ease;
    }

    .pay-method-btn.active {
        border-color: var(--primary-blue);
        background: var(--primary-light);
        color: var(--primary-blue);
    }

    .quick-cash-chips {
        display: flex;
        gap: 0.4rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }

    .chip-btn {
        background: var(--surface-secondary);
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        padding: 0.35rem 0.7rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
    }

    .chip-btn:hover {
        background: var(--primary-light);
        color: var(--primary-blue);
        border-color: var(--primary-blue);
    }
</style>
@endsection

@section('content')

    <!-- HEADER -->
    <div class="page-header">
        <div class="page-title">
            <h2>💳 Kasir / Point of Sales (POS)</h2>
            <p>Pilih antrean kendaraan, tambahkan suku cadang yang terpakai, dan proses pembayaran tagihan.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('kasir.dashboard') }}" class="btn btn-secondary">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('kasir.pos.store') }}" id="posForm">
        @csrf

        <div class="pos-layout">
            
            <!-- KOLOM KIRI: PILIH ANTREAN & SUKU CADANG -->
            <div>
                
                <!-- PILIH ANTREAN -->
                <div class="card">
                    <div class="card-header" style="margin-bottom: 1rem;">
                        <h3>1. Pilih Antrean Kendaraan</h3>
                    </div>

                    <div class="form-group">
                        <label for="queueSelect">Pilih Antrean yang Akan Dibayar</label>
                        <select name="queue_id" id="queueSelect" class="form-control" onchange="changeQueue(this.value)" required>
                            <option value="">-- Pilih Antrean Kendaraan --</option>
                            @foreach($unpaidQueues as $q)
                                <option 
                                    value="{{ $q->id }}" 
                                    {{ (old('queue_id', $selectedQueue?->id) == $q->id) ? 'selected' : '' }}
                                    data-police="{{ $q->kendaraan->no_polisi ?? '-' }}"
                                    data-vehicle="{{ ($q->kendaraan->merk ?? '') . ' ' . ($q->kendaraan->model ?? '') }}"
                                    data-owner="{{ $q->kendaraan->user->name ?? 'Pelanggan Umum' }}"
                                    data-service="{{ $q->service->nama_layanan ?? '-' }}"
                                    data-cost="{{ $q->service->biaya ?? 0 }}"
                                    data-mechanic="{{ $q->montor->name ?? '-' }}"
                                    data-notes="{{ $q->catatan_servis ?? '-' }}">
                                    [{{ $q->nomor_antrean }}] {{ $q->kendaraan->no_polisi ?? '-' }} &bull; {{ $q->kendaraan->model ?? '' }} ({{ $q->service->nama_layanan ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- RINCIAN KENDARAAN TERPILIH -->
                    <div class="vehicle-info-card" id="vehicleInfoBox" style="display: {{ $selectedQueue ? 'block' : 'none' }};">
                        <div class="vehicle-grid">
                            <div>
                                <div class="info-label">Nomor Polisi</div>
                                <div class="info-val" id="dispPolice">{{ $selectedQueue?->kendaraan->no_polisi ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="info-label">Kendaraan</div>
                                <div class="info-val" id="dispVehicle">{{ ($selectedQueue?->kendaraan->merk ?? '') . ' ' . ($selectedQueue?->kendaraan->model ?? '') }}</div>
                            </div>
                            <div>
                                <div class="info-label">Pemilik</div>
                                <div class="info-val" id="dispOwner">{{ $selectedQueue?->kendaraan->user->name ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="info-label">Mekanik yang Menangani</div>
                                <div class="info-val" id="dispMechanic">{{ $selectedQueue?->montor->name ?? '-' }}</div>
                            </div>
                            <div style="grid-column: span 2;">
                                <div class="info-label">Tindakan Jasa Servis</div>
                                <div class="info-val" style="color: var(--primary);">
                                    <span id="dispService">{{ $selectedQueue?->service->nama_layanan ?? '-' }}</span> 
                                    &bull; 
                                    <strong style="color: var(--success);" id="dispServiceCostText">
                                        Rp {{ number_format($selectedQueue?->service->biaya ?? 0, 0, ',', '.') }}
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SUKU CADANG YANG DIGUNAKAN -->
                <div class="card">
                    <div class="card-header" style="margin-bottom: 1rem;">
                        <h3>2. Suku Cadang yang Digunakan</h3>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addPartRow()">
                            ➕ Tambah Suku Cadang
                        </button>
                    </div>

                    <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 1.2rem;">
                        Jika servis kendaraan membutuhkan penggantian sparepart/oli, tambahkan item di bawah ini:
                    </p>

                    <div id="partRowsContainer">
                        <!-- Dynamic Rows Injected Here -->
                    </div>

                    <div id="noPartsNotice" style="text-align: center; padding: 1.5rem; background: var(--surface-secondary); border-radius: 12px; border: 1px dashed var(--border-color); color: var(--text-dim); font-size: 0.9rem;">
                        Belum ada suku cadang yang ditambahkan (hanya biaya jasa servis).
                    </div>
                </div>

            </div>

            <!-- KOLOM KANAN: RINGKASAN TAGIHAN & PEMBAYARAN -->
            <div>
                <div class="summary-box">
                    <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 1.2rem; color: var(--text-main);">
                        🧾 Ringkasan Pembayaran
                    </h3>

                    <div class="bill-item">
                        <span style="color: var(--text-muted);">Biaya Jasa Servis:</span>
                        <strong style="color: var(--text-main);" id="sumServiceCost">Rp 0</strong>
                    </div>

                    <div class="bill-item">
                        <span style="color: var(--text-muted);">Biaya Suku Cadang:</span>
                        <strong style="color: var(--text-main);" id="sumPartsCost">Rp 0</strong>
                    </div>

                    <div class="bill-item total">
                        <span style="font-size: 1.05rem; font-weight: 800; color: var(--text-main);">TOTAL TAGIHAN:</span>
                        <span class="grand-total-val" id="sumGrandTotal">Rp 0</span>
                    </div>

                    <hr style="border: none; border-top: 1px solid var(--border-color); margin: 1.4rem 0;">

                    <!-- METODE PEMBAYARAN -->
                    <div class="form-group" style="margin-bottom: 0.8rem;">
                        <label>Metode Pembayaran <span style="color: var(--danger)">*</span></label>
                        <input type="hidden" name="metode_pembayaran" id="metodeInput" value="cash">
                        <div class="pay-methods">
                            <button type="button" class="pay-method-btn active" id="btnPayCash" onclick="setPaymentMethod('cash')">
                                💵 Tunai (Cash)
                            </button>
                            <button type="button" class="pay-method-btn" id="btnPayTransfer" onclick="setPaymentMethod('transfer')">
                                🏦 Transfer
                            </button>
                            <button type="button" class="pay-method-btn" id="btnPayQris" onclick="setPaymentMethod('qris')">
                                📱 QRIS
                            </button>
                        </div>
                    </div>

                    <!-- INPUT UANG TUNAI (JIKA CASH) -->
                    <div id="cashInputArea">
                        <div class="form-group">
                            <label for="cashAmount">Uang Diterima dari Pelanggan (Rp)</label>
                            <input 
                                type="number" 
                                name="nominal_bayar" 
                                id="cashAmount" 
                                class="form-control" 
                                style="font-size: 1.2rem; font-weight: 800;"
                                placeholder="0" 
                                oninput="calculateChange()">
                            
                            <div class="quick-cash-chips">
                                <button type="button" class="chip-btn" onclick="setQuickCash('exact')">Uang Pas</button>
                                <button type="button" class="chip-btn" onclick="setQuickCash(50000)">50.000</button>
                                <button type="button" class="chip-btn" onclick="setQuickCash(100000)">100.000</button>
                                <button type="button" class="chip-btn" onclick="setQuickCash(200000)">200.000</button>
                                <button type="button" class="chip-btn" onclick="setQuickCash(500000)">500.000</button>
                            </div>
                        </div>

                        <div class="bill-item" style="border-bottom: none; background: var(--surface-secondary); padding: 0.8rem 1rem; border-radius: 10px; margin-bottom: 1.4rem;">
                            <span style="font-weight: 700; color: var(--text-muted);">Uang Kembalian:</span>
                            <strong style="font-size: 1.3rem; color: var(--success);" id="changeDisplay">Rp 0</strong>
                        </div>
                    </div>

                    <!-- TOMBOL PROSES -->
                    <button type="submit" class="btn btn-primary" id="btnSubmitPayment" style="width: 100%; padding: 1rem; font-size: 1.05rem; font-weight: 800;">
                        ✅ Selesaikan Pembayaran & Cetak Nota
                    </button>
                </div>
            </div>

        </div>
    </form>

    <!-- DATA SPAREPART UNTUK JAVASCRIPT -->
    <script>
        const sparepartsData = @json($spareparts);
        let currentServiceCost = {{ $selectedQueue?->service->biaya ?? 0 }};
        let rowCounter = 0;

        function formatRupiah(num) {
            return 'Rp ' + Number(num).toLocaleString('id-ID');
        }

        function changeQueue(queueId) {
            const select = document.getElementById('queueSelect');
            const option = select.options[select.selectedIndex];
            const infoBox = document.getElementById('vehicleInfoBox');

            if (!queueId) {
                infoBox.style.display = 'none';
                currentServiceCost = 0;
            } else {
                infoBox.style.display = 'block';
                document.getElementById('dispPolice').textContent = option.dataset.police;
                document.getElementById('dispVehicle').textContent = option.dataset.vehicle;
                document.getElementById('dispOwner').textContent = option.dataset.owner;
                document.getElementById('dispService').textContent = option.dataset.service;
                document.getElementById('dispMechanic').textContent = option.dataset.mechanic;
                
                currentServiceCost = parseFloat(option.dataset.cost) || 0;
                document.getElementById('dispServiceCostText').textContent = formatRupiah(currentServiceCost);
            }

            recalculateAll();
        }

        function addPartRow() {
            rowCounter++;
            const container = document.getElementById('partRowsContainer');
            const rowId = 'partRow_' + rowCounter;

            let optionsHtml = '<option value="">-- Pilih Suku Cadang --</option>';
            sparepartsData.forEach(sp => {
                optionsHtml += `<option value="${sp.id}" data-price="${sp.harga_jual}" data-stock="${sp.stok}">
                    ${sp.nama_barang} (Stok: ${sp.stok}) - ${formatRupiah(sp.harga_jual)}
                </option>`;
            });

            const row = document.createElement('div');
            row.className = 'part-row';
            row.id = rowId;
            row.innerHTML = `
                <div>
                    <select name="items[${rowCounter}][sparepart_id]" class="form-control" onchange="onPartSelected(${rowCounter})" required>
                        ${optionsHtml}
                    </select>
                </div>
                <div>
                    <input type="number" name="items[${rowCounter}][qty]" id="qty_${rowCounter}" class="form-control" value="1" min="1" max="99" oninput="onQtyChanged(${rowCounter})" required placeholder="Qty">
                </div>
                <div style="font-weight: 700; color: var(--success); font-size: 0.95rem; text-align: right;" id="subtotal_${rowCounter}">
                    Rp 0
                </div>
                <div>
                    <button type="button" class="btn-remove-part" onclick="removePartRow('${rowId}')" title="Hapus">✕</button>
                </div>
            `;

            container.appendChild(row);
            document.getElementById('noPartsNotice').style.display = 'none';
        }

        function removePartRow(rowId) {
            const el = document.getElementById(rowId);
            if (el) el.remove();

            const container = document.getElementById('partRowsContainer');
            if (container.children.length === 0) {
                document.getElementById('noPartsNotice').style.display = 'block';
            }
            recalculateAll();
        }

        function onPartSelected(idx) {
            const row = document.getElementById('partRow_' + idx);
            const select = row.querySelector('select');
            const opt = select.options[select.selectedIndex];
            const qtyInput = document.getElementById('qty_' + idx);

            if (opt.value) {
                qtyInput.max = opt.dataset.stock;
            }
            onQtyChanged(idx);
        }

        function onQtyChanged(idx) {
            const row = document.getElementById('partRow_' + idx);
            if (!row) return;

            const select = row.querySelector('select');
            const opt = select.options[select.selectedIndex];
            const qtyInput = document.getElementById('qty_' + idx);
            const subtotalEl = document.getElementById('subtotal_' + idx);

            if (opt && opt.value) {
                const price = parseFloat(opt.dataset.price) || 0;
                const qty = parseInt(qtyInput.value) || 0;
                subtotalEl.textContent = formatRupiah(price * qty);
            } else {
                subtotalEl.textContent = 'Rp 0';
            }

            recalculateAll();
        }

        function recalculateAll() {
            let totalParts = 0;
            const container = document.getElementById('partRowsContainer');
            const rows = container.getElementsByClassName('part-row');

            for (let r of rows) {
                const select = r.querySelector('select');
                const opt = select.options[select.selectedIndex];
                const qtyInput = r.querySelector('input[type="number"]');

                if (opt && opt.value) {
                    const price = parseFloat(opt.dataset.price) || 0;
                    const qty = parseInt(qtyInput.value) || 0;
                    totalParts += (price * qty);
                }
            }

            const grandTotal = currentServiceCost + totalParts;

            document.getElementById('sumServiceCost').textContent = formatRupiah(currentServiceCost);
            document.getElementById('sumPartsCost').textContent = formatRupiah(totalParts);
            document.getElementById('sumGrandTotal').textContent = formatRupiah(grandTotal);

            calculateChange();
        }

        function setPaymentMethod(method) {
            document.getElementById('metodeInput').value = method;
            document.getElementById('btnPayCash').classList.toggle('active', method === 'cash');
            document.getElementById('btnPayTransfer').classList.toggle('active', method === 'transfer');
            document.getElementById('btnPayQris').classList.toggle('active', method === 'qris');

            const cashArea = document.getElementById('cashInputArea');
            cashArea.style.display = method === 'cash' ? 'block' : 'none';
        }

        function calculateChange() {
            const grandTotalText = document.getElementById('sumGrandTotal').textContent;
            const grandTotal = parseFloat(grandTotalText.replace(/[^0-9]/g, '')) || 0;
            const cashVal = parseFloat(document.getElementById('cashAmount').value) || 0;
            const change = Math.max(0, cashVal - grandTotal);

            document.getElementById('changeDisplay').textContent = formatRupiah(change);
        }

        function setQuickCash(amount) {
            const grandTotalText = document.getElementById('sumGrandTotal').textContent;
            const grandTotal = parseFloat(grandTotalText.replace(/[^0-9]/g, '')) || 0;

            if (amount === 'exact') {
                document.getElementById('cashAmount').value = grandTotal;
            } else {
                document.getElementById('cashAmount').value = amount;
            }
            calculateChange();
        }

        // Jalankan kalkulasi awal saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            recalculateAll();
        });
    </script>

@endsection
