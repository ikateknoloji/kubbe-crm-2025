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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_name');
            $table->string('order_code');

            $table->enum('status', ['OC', 'PRP', 'RFP', 'P', 'SHP', 'PD'])->default('OC');
            $table->enum('is_rejected', ['A', 'R', 'C', 'P'])->default('A');
            $table->text('note')->nullable();

            $table->enum('shipping_type', allowed: ['A', 'G', 'T'])->nullable();
            $table->enum('invoice_status', ['P', 'C'])->default('P'); 

            $table->decimal('paid_amount', 8, 2)->default(0);
            $table->decimal('offer_price', 8, 2);

            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('manufacturer_id')->nullable()->constrained('manufacturers')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
