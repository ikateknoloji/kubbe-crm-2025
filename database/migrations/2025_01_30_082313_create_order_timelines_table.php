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
        Schema::create('order_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
            $table->timestamp('approved_at')->nullable()->comment('Siparişin onaylandığı tarih');
            $table->timestamp('production_started_at')->nullable()->comment('Üretime başlama tarihi');
            $table->timestamp('production_completed_at')->nullable()->comment('Üretimin tamamlandığı tarih'); 
            $table->timestamp('shipped_at')->nullable()->comment('Kargoya veriliş tarihi');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_timelines');
    }
};
