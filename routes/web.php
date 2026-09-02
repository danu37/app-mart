<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\CheckoutController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PelangganController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Models\Produk;
use Illuminate\Support\Facades\Schema;

// ─── Public Routes ─────────────────────────────────────

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

Route::get('/about', function () {
    $categories = Schema::hasTable('tbl_produk')
        ? Produk::select('kategori')->distinct()->pluck('kategori')->filter()->values()
        : collect([]);
    return Inertia::render('About', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'categories' => $categories,
    ]);
})->name('about');

Route::get('/keunggulan', function () {
    $categories = Schema::hasTable('tbl_produk')
        ? Produk::select('kategori')->distinct()->pluck('kategori')->filter()->values()
        : collect([]);
    return Inertia::render('Advantages', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'categories' => $categories,
    ]);
})->name('advantages');

Route::get('/contact', function () {
    $categories = Schema::hasTable('tbl_produk')
        ? Produk::select('kategori')->distinct()->pluck('kategori')->filter()->values()
        : collect([]);
    return Inertia::render('Contact', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'categories' => $categories,
    ]);
})->name('contact');

// ─── Authenticated (Shared) ─────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Legacy dashboard redirect (based on role)
Route::get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ─── Admin Routes ───────────────────────────────────────

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Products CRUD
    Route::get('/products', [AdminProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/products/{id}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{id}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy'])->name('products.destroy');

    // Orders Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/export-pdf', [AdminOrderController::class, 'exportPdf'])->name('orders.exportPdf');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Legacy: Pelanggan (Admin Only)
    Route::get('/pelanggan', [PelangganController::class, 'index'])->name('pelanggan.index');
    Route::get('/pelanggan/export-pdf', [PelangganController::class, 'exportPdf'])->name('pelanggan.exportPdf');
    Route::get('/pelanggan/{id}/riwayat', [PelangganController::class, 'riwayat'])->name('pelanggan.riwayat');
    Route::get('/pelanggan/transaksi/{id}/struk', [PelangganController::class, 'struk'])->name('pelanggan.struk');

    // Backup & Restore (Admin Only)
    Route::get('/backup-restore', [BackupController::class, 'index'])->name('backup.index');
    Route::get('/backup-restore/download', [BackupController::class, 'backup'])->name('backup.download');
    Route::post('/backup-restore/upload', [BackupController::class, 'restore'])->name('backup.upload');
});

// ─── Laporan Routes (Auth required) ────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/export-api', [LaporanController::class, 'exportApi'])->name('laporan.exportApi');
    Route::get('/laporan/riwayat', [LaporanController::class, 'riwayatExport'])->name('laporan.riwayatExport');
});

// ─── Customer Routes ────────────────────────────────────

Route::middleware(['auth', 'verified'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

    // Cart & Checkout
    Route::post('/cart/add', [CheckoutController::class, 'addToCart'])->name('cart.add');
    Route::post('/cart/update', [CheckoutController::class, 'updateCart'])->name('cart.update');
    Route::post('/cart/remove', [CheckoutController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders
    Route::get('/orders', [CustomerOrderController::class, 'index'])->name('orders');
    Route::get('/orders/{id}', [CustomerOrderController::class, 'show'])->name('orders.show');
});


require __DIR__ . '/auth.php';
