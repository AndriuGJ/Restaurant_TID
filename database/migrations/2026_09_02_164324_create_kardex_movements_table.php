<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kardex_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products');
            $table->enum('movement_type', ['purchase', 'sale', 'adjustment']);
            $table->decimal('quantity_in', 10, 2)->default(0.00);
            $table->decimal('quantity_out', 10, 2)->default(0.00);
            $table->decimal('balance', 10, 2);
            $table->nullableMorphs('related_document');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kardex_movements');
    }
};
