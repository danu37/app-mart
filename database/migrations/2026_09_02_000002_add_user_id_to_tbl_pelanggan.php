<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_pelanggan', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id_pelanggan');
            $table->string('alamat', 255)->nullable()->after('kota');
            $table->string('telepon', 20)->nullable()->after('alamat');

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_pelanggan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'alamat', 'telepon']);
        });
    }
};
