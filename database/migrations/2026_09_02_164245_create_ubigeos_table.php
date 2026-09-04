<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ubigeos', function (Blueprint $table) {
            $table->id();
            $table->string('department', 100);
            $table->string('province', 100);
            $table->string('district', 100);
            $table->string('code', 10)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ubigeos');
    }
};
