<?php

namespace Database\Seeders;

use App\Models\Configuration\DocumentType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $documentTypes = [
            // Documentos de identificación (Perú)
            ['name' => 'DNI', 'nomenclature' => 'DNI', 'character_limit' => 8, 'type' => 'identification'],
            ['name' => 'RUC', 'nomenclature' => 'RUC', 'character_limit' => 11, 'type' => 'identification'],
            ['name' => 'Carnet de Extranjería', 'nomenclature' => 'CE', 'character_limit' => 12, 'type' => 'identification'],
            // Comprobantes (invoice)
            ['name' => 'Boleta', 'nomenclature' => 'B', 'character_limit' => null, 'type' => 'invoice'],
            ['name' => 'Factura', 'nomenclature' => 'F', 'character_limit' => null, 'type' => 'invoice'],
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::firstOrCreate(
                ['name' => $documentType['name']],
                $documentType
            );
        }
    }
}
