<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FrontdeskController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\MontirController;
use App\Http\Controllers\POSController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QueueController::class, 'publicDisplay'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Halaman Dashboard Umum (Mengalihkan ke panel sesuai role)
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;

        return match ($role) {
            'admin_gudang' => redirect()->route('admin.dashboard'),
            'kasir' => redirect()->route('kasir.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            'montor' => redirect()->route('montir.dashboard'),
            default => view('dashboard'),
        };
    })->name('dashboard');

    // ─── GROUP: Admin Gudang ─────────────────────────────────────────────────
    Route::middleware('role:admin_gudang')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [InventoryController::class, 'dashboard'])->name('dashboard');

        // Manajemen Suku Cadang (Spareparts)
        Route::resource('spareparts', InventoryController::class)->except(['show']);
        Route::post('/spareparts/{sparepart}/adjust-stock', [InventoryController::class, 'adjustStock'])->name('spareparts.adjust-stock');

        // Master Jasa & Layanan Servis (Services)
        Route::resource('services', ServiceController::class)->except(['show']);

        // Profil & Pengaturan Bengkel
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // ─── GROUP: Kasir / POS ──────────────────────────────────────────────────
    Route::middleware('role:kasir')->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/dashboard', [POSController::class, 'dashboard'])->name('dashboard');
        Route::get('/pos', [POSController::class, 'create'])->name('pos');
        Route::post('/pos', [POSController::class, 'store'])->name('pos.store');
        Route::get('/transactions', [POSController::class, 'index'])->name('transactions.index');
        Route::get('/transactions/{transaction}/receipt', [POSController::class, 'receipt'])->name('transactions.receipt');
    });

    // ─── GROUP: Petugas Frontdesk (Manajemen Antrean) ────────────────────────
    Route::middleware('role:petugas')->prefix('petugas')->name('petugas.')->group(function () {
        Route::get('/dashboard', [FrontdeskController::class, 'dashboard'])->name('dashboard');
        Route::get('/antrean/create', [FrontdeskController::class, 'create'])->name('antrean.create');
        Route::post('/antrean', [FrontdeskController::class, 'store'])->name('antrean.store');
        Route::get('/antrean/{queue}', [FrontdeskController::class, 'show'])->name('antrean.show');
        Route::patch('/antrean/{queue}/status', [FrontdeskController::class, 'updateStatus'])->name('antrean.update-status');
    });

    // ─── GROUP: Montir / Mekanik ─────────────────────────────────────────────
    Route::middleware('role:montor')->prefix('montir')->name('montir.')->group(function () {
        Route::get('/dashboard', [MontirController::class, 'dashboard'])->name('dashboard');
        Route::get('/antrean/{queue}', [MontirController::class, 'show'])->name('antrean.show');
        Route::patch('/antrean/{queue}/status', [MontirController::class, 'updateStatus'])->name('antrean.update-status');
    });
});
