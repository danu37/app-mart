<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$products = App\Models\Produk::all();
foreach ($products as $p) {
    if (!$p->gambar) {
        echo "No image: " . $p->nama_produk . PHP_EOL;
    }
}
