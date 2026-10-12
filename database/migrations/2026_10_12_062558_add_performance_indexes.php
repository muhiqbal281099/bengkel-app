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
        Schema::table('queues', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('status');
            $table->index(['created_at', 'status']);
            $table->index('nomor_antrean');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('metode_pembayaran');
        });

        Schema::table('spareparts', function (Blueprint $table) {
            $table->index('stok');
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at', 'status']);
            $table->dropIndex(['nomor_antrean']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['metode_pembayaran']);
        });

        Schema::table('spareparts', function (Blueprint $table) {
            $table->dropIndex(['stok']);
            $table->dropIndex(['kategori']);
        });
    }
};
