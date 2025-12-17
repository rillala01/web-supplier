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
    Schema::create('reseps', function (Blueprint $table) {
        $table->id();
        $table->foreignId('produk_id')->constrained('produks')->cascadeOnDelete();
        $table->foreignId('barang_id')->constrained('barangs')->cascadeOnDelete(); // Ini bahan baku
        $table->decimal('jumlah_pemakaian', 10, 2); // Misal: 0.5 (untuk setengah kg/liter/pcs)
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};
