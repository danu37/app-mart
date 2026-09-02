<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$products = App\Models\Produk::all();
foreach ($products as $p) {
    echo $p->id_produk . ': ' . $p->nama_produk . ' -> ' . ($p->gambar ?: 'NONE') . "\n";
}
