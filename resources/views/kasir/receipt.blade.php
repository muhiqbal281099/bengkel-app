@extends('layouts.kasir')

@section('title', 'Nota Pembayaran - NOTA-' . str_pad($transaction->id, 5, '0', STR_PAD_LEFT))

@section('styles')
<style>
    .receipt-container {
        max-width: 680px;
        margin: 0 auto;
    }

    .receipt-card {
        background: #ffffff;
        color: #0f172a;
        border-radius: 18px;
        padding: 2.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        position: relative;
    }

    /* KOP BENGKEL */
    .receipt-header {
        display: flex;
        align-items: center;
        gap: 1.2rem;
        border-bottom: 2px dashed #cbd5e1;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .receipt-logo {
        width: 65px;
        height: 65px;
        object-fit: contain;
    }

    .receipt-title h2 {
        font-size: 1.5rem;
        font-weight: 900;
        color: #1e40af;
        margin: 0;
        line-height: 1.2;
    }

    .receipt-title p {
        margin: 0.2rem 0 0 0;
        font-size: 0.85rem;
        color: #475569;
        line-height: 1.3;
    }

    /* METADATA NOTA */
    .receipt-meta {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.8rem;
        font-size: 0.88rem;
        background: #f8fafc;
        padding: 1rem;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }

    .meta-row {
        display: flex;
        justify-content: space-between;
    }

    .meta-label { color: #64748b; font-weight: 500; }
    .meta-val { color: #0f172a; font-weight: 700; }

    /* TABLE ITEMS */
    .receipt-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
    }

    .receipt-table th {
        text-align: left;
        padding: 0.6rem 0.5rem;
        border-bottom: 2px solid #0f172a;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #334155;
    }

    .receipt-table td {
        padding: 0.7rem 0.5rem;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
    }

    /* SUMMARY TOTALS */
    .receipt-summary {
        margin-left: auto;
        width: 60%;
        font-size: 0.92rem;
        margin-bottom: 1.8rem;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0;
    }

    .summary-row.grand-total {
        border-top: 2px solid #0f172a;
        margin-top: 0.5rem;
        padding-top: 0.8rem;
        font-size: 1.15rem;
        font-weight: 900;
        color: #1e40af;
    }

    /* FOOTER NOTA */
    .receipt-footer {
        border-top: 2px dashed #cbd5e1;
        padding-top: 1.2rem;
        text-align: center;
        font-size: 0.82rem;
        color: #64748b;
        line-height: 1.4;
    }

    .sign-section {
        display: flex;
        justify-content: space-between;
        margin-top: 1.5rem;
        padding: 0 1.5rem;
        text-align: center;
        font-size: 0.85rem;
    }

    .sign-box {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .sign-space {
        height: 50px;
    }

    /* PRINT STYLES */
    @media print {
        body {
            background: #fff !important;
            color: #000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .navbar, .footer, .btn, .page-header, .alert, .btn-theme-toggle {
            display: none !important;
        }

        .container {
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .receipt-card {
            box-shadow: none !important;
            border: none !important;
            padding: 0 !important;
            width: 100% !important;
            max-width: 100% !important;
        }
    }
</style>
@endsection

@section('content')

    <div class="receipt-container">
        
        <!-- ACTION BAR -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;" class="no-print">
            <a href="{{ route('kasir.dashboard') }}" class="btn btn-secondary">
                ← Kembali ke Kasir
            </a>

            <div style="display: flex; gap: 0.8rem;">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    🖨️ Cetak Nota Pembayaran
                </button>
                <a href="{{ route('kasir.pos') }}" class="btn btn-success">
                    ➕ Transaksi Baru
                </a>
            </div>
        </div>

        <!-- NOTA PEMBAYARAN RESMI -->
        <div class="receipt-card">
            
            <!-- KOP BENGKEL -->
            <div class="receipt-header">
                @if($setting->logo_url)
                    <img src="{{ $setting->logo_url }}" alt="Logo" class="receipt-logo">
                @else
                    <div style="width: 55px; height: 55px; border-radius: 10px; background: #1e40af; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.6rem;">
                        🔧
                    </div>
                @endif

                <div class="receipt-title">
                    <h2>{{ $setting->nama_bengkel }}</h2>
                    <p>{{ $setting->alamat_bengkel ?? 'Sistem Bengkel Profesional' }}</p>
                    @if($setting->telepon_bengkel)
                        <p><strong>Telp:</strong> {{ $setting->telepon_bengkel }}</p>
                    @endif
                </div>
            </div>

            <!-- METADATA NOTA -->
            <div class="receipt-meta">
                <div class="meta-row">
                    <span class="meta-label">No. Nota:</span>
                    <span class="meta-val">NOTA-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">No. Antrean:</span>
                    <span class="meta-val">{{ $transaction->queue->nomor_antrean ?? '-' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Tanggal:</span>
                    <span class="meta-val">{{ $transaction->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">No. Polisi:</span>
                    <span class="meta-val" style="color: #1e40af;">{{ $transaction->queue->kendaraan->no_polisi ?? '-' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Kasir:</span>
                    <span class="meta-val">{{ $transaction->kasir->name ?? 'Kasir Utama' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Kendaraan:</span>
                    <span class="meta-val">{{ ($transaction->queue->kendaraan->merk ?? '') . ' ' . ($transaction->queue->kendaraan->model ?? '') }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Pelanggan:</span>
                    <span class="meta-val">{{ $transaction->queue->kendaraan->user->name ?? 'Pelanggan Umum' }}</span>
                </div>
                <div class="meta-row">
                    <span class="meta-label">Mekanik:</span>
                    <span class="meta-val">{{ $transaction->queue->montor->name ?? '-' }}</span>
                </div>
            </div>

            <!-- TABEL DETAIL TAGIHAN -->
            <table class="receipt-table">
                <thead>
                    <tr>
                        <th>Deskripsi Jasa / Suku Cadang</th>
                        <th style="text-align: center; width: 60px;">Qty</th>
                        <th style="text-align: right; width: 110px;">Harga (Rp)</th>
                        <th style="text-align: right; width: 120px;">Subtotal (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- JASA SERVIS -->
                    <tr>
                        <td>
                            <strong>[JASA] {{ $transaction->queue->service->nama_layanan ?? 'Servis Kendaraan' }}</strong>
                            <div style="font-size: 0.78rem; color: #64748b;">
                                Durasi estimasi: {{ $transaction->queue->service->durasi_estimasi ?? '-' }} Menit
                            </div>
                        </td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: right;">{{ number_format($transaction->total_biaya_jasa, 0, ',', '.') }}</td>
                        <td style="text-align: right; font-weight: 700;">{{ number_format($transaction->total_biaya_jasa, 0, ',', '.') }}</td>
                    </tr>

                    <!-- SPAREPART / OLI -->
                    @forelse($transaction->details as $item)
                        <tr>
                            <td>
                                <div>{{ $item->sparepart->nama_barang }}</div>
                                <div style="font-size: 0.75rem; color: #64748b;">Kode: {{ $item->sparepart->kode_barang }}</div>
                            </td>
                            <td style="text-align: center;">{{ $item->jumlah }}</td>
                            <td style="text-align: right;">{{ number_format($item->sparepart->harga_jual, 0, ',', '.') }}</td>
                            <td style="text-align: right; font-weight: 700;">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <!-- Tidak ada sparepart -->
                    @endforelse
                </tbody>
            </table>

            <!-- RINGKASAN PEMBAYARAN -->
            <div class="receipt-summary">
                <div class="summary-row">
                    <span style="color: #64748b;">Subtotal Biaya Jasa:</span>
                    <span>Rp {{ number_format($transaction->total_biaya_jasa, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row">
                    <span style="color: #64748b;">Subtotal Suku Cadang:</span>
                    <span>Rp {{ number_format($transaction->total_biaya_sparepart, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row grand-total">
                    <span>TOTAL BAYAR:</span>
                    <span>Rp {{ number_format($transaction->total_pembayaran, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row" style="font-size: 0.85rem; color: #64748b; margin-top: 0.2rem;">
                    <span>Metode Pembayaran:</span>
                    <strong style="color: #0f172a; text-transform: uppercase;">{{ $transaction->metode_pembayaran }} (LUNAS)</strong>
                </div>
            </div>

            <!-- TANDA TANGAN -->
            <div class="sign-section">
                <div class="sign-box">
                    <span>Pelanggan</span>
                    <div class="sign-space"></div>
                    <strong>( {{ $transaction->queue->kendaraan->user->name ?? 'Pelanggan' }} )</strong>
                </div>
                <div class="sign-box">
                    <span>Kasir / Petugas</span>
                    <div class="sign-space"></div>
                    <strong>( {{ $transaction->kasir->name ?? 'Kasir Bengkel' }} )</strong>
                </div>
            </div>

            <!-- FOOTER -->
            <div class="receipt-footer">
                <p><strong>GARANSI SERVIS:</strong> Berlaku selama 7 hari atau 500 KM sejak nota ini diterbitkan.</p>
                <p style="margin-top: 0.3rem;">Terima kasih telah mempercayakan perawatan kendaraan Anda di <strong>{{ $setting->nama_bengkel }}</strong>.</p>
            </div>

        </div>

    </div>

@endsection
