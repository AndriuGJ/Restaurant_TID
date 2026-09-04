<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('cash_register_session_id')->nullable()->constrained('cash_register_sessions');
            $table->foreignId('table_id')->nullable()->constrained('tables');
            $table->integer('guests')->default(1);
            $table->enum('sale_type', ['pos', 'delivery', 'quick_sale'])->default('pos');
            $table->boolean('is_takeaway')->default(false);
            $table->foreignId('delivery_provider_id')->nullable()->constrained('delivery_providers');
            $table->string('delivery_person_name', 150)->nullable();
            $table->nullableMorphs('clientable');
            $table->foreignId('document_type_id')->nullable()->constrained('document_types');
            $table->string('series', 20)->nullable();
            $table->string('number', 50)->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax', 10, 2)->default(0.00);
            $table->decimal('total', 10, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled', 'preparing'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
