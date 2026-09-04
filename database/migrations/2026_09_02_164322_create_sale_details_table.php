<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->text('notes')->nullable();
            $table->enum('kitchen_status', ['pending', 'preparing', 'completed'])->default('pending');
            $table->timestamp('prep_started_at')->nullable();
            $table->timestamp('prep_completed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};
