<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tbl_pelanggan', function (Blueprint $table) {
            $table->id('id_pelanggan');
            $table->string('nama_pelanggan', 100);
            $table->string('kota', 100)->nullable();
        });

        Schema::create('tbl_produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->string('nama_produk', 150);
            $table->string('kategori', 100)->nullable();
            $table->decimal('harga', 15, 2);
            $table->integer('stok')->default(0);
            $table->string('gambar')->nullable();
        });

        Schema::create('tbl_transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->date('tanggal');
            $table->unsignedBigInteger('id_pelanggan');
            $table->decimal('total_belanja', 15, 2)->default(0);

            $table->foreign('id_pelanggan')
                ->references('id_pelanggan')
                ->on('tbl_pelanggan')
                ->cascadeOnDelete();
        });

        Schema::create('tbl_detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_transaksi');
            $table->unsignedBigInteger('id_produk');
            $table->integer('qty')->default(1);
            $table->decimal('subtotal', 15, 2)->default(0);

            $table->foreign('id_transaksi')
                ->references('id_transaksi')
                ->on('tbl_transaksi')
                ->cascadeOnDelete();

            $table->foreign('id_produk')
                ->references('id_produk')
                ->on('tbl_produk')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_detail_transaksi');
        Schema::dropIfExists('tbl_transaksi');
        Schema::dropIfExists('tbl_produk');
        Schema::dropIfExists('tbl_pelanggan');
    }
};
