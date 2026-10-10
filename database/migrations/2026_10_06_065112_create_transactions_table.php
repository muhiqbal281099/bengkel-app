<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->constrained('queues')->onDelete('cascade');
            $table->foreignId('kasir_id')->constrained('users')->onDelete('restrict');
            $table->decimal('total_biaya_jasa', 12, 2)->default(0);
            $table->decimal('total_biaya_sparepart', 12, 2)->default(0);
            $table->decimal('total_pembayaran', 12, 2)->default(0);
            $table->enum('metode_pembayaran', ['cash', 'transfer', 'qris']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
