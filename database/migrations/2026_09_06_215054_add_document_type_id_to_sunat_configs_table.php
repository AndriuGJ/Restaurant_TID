<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sunat_configs', function (Blueprint $table) {
            $table->unsignedBigInteger('document_type_id')->nullable()->after('company_id');

            $table->foreign('document_type_id')
                ->references('id')
                ->on('document_types')
                ->nullOnDelete();
        });

        $facturaId = DB::table('document_types')
            ->where('type', 'invoice')
            ->where('nomenclature', 'F')
            ->value('id');

        if ($facturaId) {
            DB::table('sunat_configs')->update(['document_type_id' => $facturaId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sunat_configs', function (Blueprint $table) {
            $table->dropForeign(['document_type_id']);
            $table->dropColumn('document_type_id');
        });
    }
};
