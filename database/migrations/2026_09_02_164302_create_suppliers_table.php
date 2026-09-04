<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 20);
            $table->string('social_reason', 255);
            $table->string('phone', 20)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->boolean('status')->default(true);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
