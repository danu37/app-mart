<?php

use App\Http\Controllers\BackupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PelangganController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\Produk;
use Illuminate\Support\Facades\Schema;

Route::get('/', function () {
    $produk = Schema::hasTable('tbl_produk') ? Produk::all() : collect([]);
    $categories = Schema::hasTable('tbl_produk')
        ? Produk::select('kategori')->distinct()->pluck('kategori')->filter()->values()
        : collect([]);

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'products' => $produk,
        'categories' => $categories,
    ]);
})->name('home');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Route Pelanggan
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('/pelanggan/{id}/riwayat', [PelangganController::class, 'riwayat'])->name('pelanggan.riwayat');
    Route::get('/pelanggan/transaksi/{id}/struk', [PelangganController::class, 'struk'])->name('pelanggan.struk');

    // Route Backup & Restore
    Route::get('/backup-restore', [BackupController::class, 'index'])->name('backup.index');
    Route::get('/backup-restore/download', [BackupController::class, 'backup'])->name('backup.download');
    Route::post('/backup-restore/upload', [BackupController::class, 'restore'])->name('backup.upload');
});

require __DIR__.'/auth.php';
