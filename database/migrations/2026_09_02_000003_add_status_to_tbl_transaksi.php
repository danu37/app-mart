<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_transaksi', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->after('total_belanja');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_transaksi', function (Blueprint $table) {
            $table->dropColumn(['status', 'created_at', 'updated_at']);
        });
    }
};
