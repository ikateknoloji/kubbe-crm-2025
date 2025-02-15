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
        Schema::create('invoice_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete(action: 'cascade'); 
            $table->enum('invoice_type', ['C', 'I'])->comment('C: Individual, I: Corporate');
            $table->string('company_name')->nullable();
            $table->string('name')->nullable();
            $table->string('surname')->nullable();
            $table->string('tc_number', 11)->nullable();
            $table->string('address', 500)->nullable();
            $table->string('tax_office')->nullable();
            $table->string('tax_number', 20)->nullable();
            $table->string('email')->nullable();            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_infos');
    }
};
