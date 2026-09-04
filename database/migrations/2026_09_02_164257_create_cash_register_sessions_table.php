<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_register_id')->constrained('cash_registers');
            $table->foreignId('shift_id')->constrained('shifts');
            $table->foreignId('user_opening_id')->constrained('users');
            $table->foreignId('user_closing_id')->nullable()->constrained('users');
            $table->decimal('opening_amount', 10, 2);
            $table->decimal('closing_amount', 10, 2)->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_sessions');
    }
};
