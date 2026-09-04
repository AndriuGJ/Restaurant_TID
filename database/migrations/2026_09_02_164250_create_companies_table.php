<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('commercial_name', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('commercial_address')->nullable();
            $table->string('ruc', 20);
            $table->string('social_reason', 255);
            $table->text('fiscal_address')->nullable();
            $table->foreignId('ubigeo_id')->nullable()->constrained('ubigeos')->nullOnDelete();
            $table->string('logo')->nullable();
            $table->string('sol_user', 50)->nullable();
            $table->string('sol_password', 100)->nullable();
            $table->string('digital_certificate_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
