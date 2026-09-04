<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_clients', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 20);
            $table->string('social_reason', 255);
            $table->string('phone', 20)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->foreignId('document_type_id')->nullable()->constrained('document_types');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_clients');
    }
};
