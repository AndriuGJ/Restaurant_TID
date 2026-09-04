<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hall_id')->constrained('halls')->cascadeOnDelete();
            $table->string('name', 50);
            $table->enum('shape', ['square', 'round', 'rectangular'])->default('square');
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
