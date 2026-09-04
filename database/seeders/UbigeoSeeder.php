<?php

namespace Database\Seeders;

use App\Models\Configuration\Ubigeo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UbigeoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Catálogo de departamentos y provincias principales del Perú (código ubigeo de 6 dígitos).
        // Cada entrada: [code, department, province, district]
        $ubigeos = [
            // --- LIMA (lima) ---
            ['150101', 'Lima', 'Lima', 'Lima'],
            ['150102', 'Lima', 'Lima', 'Ancón'],
            ['150103', 'Lima', 'Lima', 'Ate'],
            ['150104', 'Lima', 'Lima', 'Barranco'],
            ['150105', 'Lima', 'Lima', 'Breña'],
            ['150106', 'Lima', 'Lima', 'Carabayllo'],
            ['150107', 'Lima', 'Lima', 'Chaclacayo'],
            ['150108', 'Lima', 'Lima', 'Chorrillos'],
            ['150109', 'Lima', 'Lima', 'Cieneguilla'],
            ['150110', 'Lima', 'Lima', 'Comas'],
            ['150111', 'Lima', 'Lima', 'El Agustino'],
            ['150112', 'Lima', 'Lima', 'Independencia'],
            ['150113', 'Lima', 'Lima', 'Jesús María'],
            ['150114', 'Lima', 'Lima', 'La Molina'],
            ['150115', 'Lima', 'Lima', 'La Victoria'],
            ['150116', 'Lima', 'Lima', 'Lince'],
            ['150117', 'Lima', 'Lima', 'Los Olivos'],
            ['150118', 'Lima', 'Lima', 'Lurigancho'],
            ['150119', 'Lima', 'Lima', 'Lurín'],
            ['150120', 'Lima', 'Lima', 'Magdalena del Mar'],
            ['150121', 'Lima', 'Lima', 'Miraflores'],
            ['150122', 'Lima', 'Lima', 'Pachacámac'],
            ['150123', 'Lima', 'Lima', 'Pucusana'],
            ['150124', 'Lima', 'Lima', 'Pueblo Libre'],
            ['150125', 'Lima', 'Lima', 'Puente Piedra'],
            ['150126', 'Lima', 'Lima', 'Punta Hermosa'],
            ['150127', 'Lima', 'Lima', 'Punta Negra'],
            ['150128', 'Lima', 'Lima', 'Rímac'],
            ['150129', 'Lima', 'Lima', 'San Bartolo'],
            ['150130', 'Lima', 'Lima', 'San Borja'],
            ['150131', 'Lima', 'Lima', 'San Isidro'],
            ['150132', 'Lima', 'Lima', 'San Juan de Lurigancho'],
            ['150133', 'Lima', 'Lima', 'San Juan de Miraflores'],
            ['150134', 'Lima', 'Lima', 'San Luis'],
            ['150135', 'Lima', 'Lima', 'San Martín de Porres'],
            ['150136', 'Lima', 'Lima', 'San Miguel'],
            ['150137', 'Lima', 'Lima', 'Santa Anita'],
            ['150138', 'Lima', 'Lima', 'Santa María del Mar'],
            ['150139', 'Lima', 'Lima', 'Santa Rosa'],
            ['150140', 'Lima', 'Lima', 'Santiago de Surco'],
            ['150141', 'Lima', 'Lima', 'Surquillo'],
            ['150142', 'Lima', 'Lima', 'Villa El Salvador'],
            ['150143', 'Lima', 'Lima', 'Villa María del Triunfo'],
            // Lima provincias
            ['150201', 'Lima', 'Barranca', 'Barranca'],
            ['150301', 'Lima', 'Cajatambo', 'Cajatambo'],
            ['150401', 'Lima', 'Canta', 'Canta'],
            ['150501', 'Lima', 'Cañete', 'San Vicente de Cañete'],
            ['150601', 'Lima', 'Huaral', 'Huaral'],
            ['150701', 'Lima', 'Huarochirí', 'Matucana'],
            ['150801', 'Lima', 'Huaura', 'Huacho'],
            ['150901', 'Lima', 'Oyón', 'Oyón'],
            ['151001', 'Lima', 'Yauyos', 'Yauyos'],

            // --- Amazonas ---
            ['010101', 'Amazonas', 'Chachapoyas', 'Chachapoyas'],
            ['010201', 'Amazonas', 'Bagua', 'Bagua'],
            ['010301', 'Amazonas', 'Bongará', 'Jumbilla'],
            ['010401', 'Amazonas', 'Condorcanqui', 'Nieva'],
            ['010501', 'Amazonas', 'Luya', 'Lámud'],
            ['010601', 'Amazonas', 'Rodríguez de Mendoza', 'San Nicolás'],
            ['010701', 'Amazonas', 'Utcubamba', 'Bagua Grande'],

            // --- Ancash ---
            ['020101', 'Ancash', 'Huaraz', 'Huaraz'],
            ['020201', 'Ancash', 'Aija', 'Aija'],
            ['020301', 'Ancash', 'Antonio Raymondi', 'Llamellín'],
            ['020401', 'Ancash', 'Asunción', 'Chacas'],
            ['020501', 'Ancash', 'Bolognesi', 'Chiquián'],
            ['020601', 'Ancash', 'Carhuaz', 'Carhuaz'],
            ['020701', 'Ancash', 'Carlos Fermín Fitzcarrald', 'San Luis'],
            ['020801', 'Ancash', 'Casma', 'Casma'],
            ['020901', 'Ancash', 'Corongo', 'Corongo'],
            ['021001', 'Ancash', 'Huari', 'Huari'],
            ['021101', 'Ancash', 'Huarmey', 'Huarmey'],
            ['021201', 'Ancash', 'Huaylas', 'Caraz'],
            ['021301', 'Ancash', 'Mariscal Luzuriaga', 'Piscobamba'],
            ['021401', 'Ancash', 'Ocros', 'Ocros'],
            ['021501', 'Ancash', 'Pallasca', 'Cabana'],
            ['021601', 'Ancash', 'Pomabamba', 'Pomabamba'],
            ['021701', 'Ancash', 'Recuay', 'Recuay'],
            ['021801', 'Ancash', 'Santa', 'Chimbote'],
            ['021901', 'Ancash', 'Sihuas', 'Sihuas'],
            ['022001', 'Ancash', 'Yungay', 'Yungay'],

            // --- Apurímac ---
            ['030101', 'Apurímac', 'Abancay', 'Abancay'],
            ['030201', 'Apurímac', 'Andahuaylas', 'Andahuaylas'],
            ['030301', 'Apurímac', 'Antabamba', 'Antabamba'],
            ['030401', 'Apurímac', 'Aymaraes', 'Chalhuanca'],
            ['030501', 'Apurímac', 'Cotabambas', 'Tambobamba'],
            ['030601', 'Apurímac', 'Chincheros', 'Chincheros'],
            ['030701', 'Apurímac', 'Grau', 'Chuquibambilla'],

            // --- Arequipa ---
            ['040101', 'Arequipa', 'Arequipa', 'Arequipa'],
            ['040201', 'Arequipa', 'Camaná', 'Camaná'],
            ['040301', 'Arequipa', 'Caravelí', 'Caravelí'],
            ['040401', 'Arequipa', 'Castilla', 'Aplao'],
            ['040501', 'Arequipa', 'Caylloma', 'Chivay'],
            ['040601', 'Arequipa', 'Condesuyos', 'Chuquibamba'],
            ['040701', 'Arequipa', 'Islay', 'Mollendo'],
            ['040801', 'Arequipa', 'La Unión', 'Cotahuasi'],

            // --- Ayacucho ---
            ['050101', 'Ayacucho', 'Huamanga', 'Ayacucho'],
            ['050201', 'Ayacucho', 'Cangallo', 'Cangallo'],
            ['050301', 'Ayacucho', 'Huanca Sancos', 'Sancos'],
            ['050401', 'Ayacucho', 'Huanta', 'Huanta'],
            ['050501', 'Ayacucho', 'La Mar', 'San Miguel'],
            ['050601', 'Ayacucho', 'Lucanas', 'Puquio'],
            ['050701', 'Ayacucho', 'Parinacochas', 'Coracora'],
            ['050801', 'Ayacucho', 'Páucar del Sara Sara', 'Pausa'],
            ['050901', 'Ayacucho', 'Sucre', 'Querobamba'],
            ['051001', 'Ayacucho', 'Víctor Fajardo', 'Huancapi'],
            ['051101', 'Ayacucho', 'Vilcas Huamán', 'Vilcas Huamán'],

            // --- Cajamarca ---
            ['060101', 'Cajamarca', 'Cajamarca', 'Cajamarca'],
            ['060201', 'Cajamarca', 'Cajabamba', 'Cajabamba'],
            ['060301', 'Cajamarca', 'Celendín', 'Celendín'],
            ['060401', 'Cajamarca', 'Chota', 'Chota'],
            ['060501', 'Cajamarca', 'Contumazá', 'Contumazá'],
            ['060601', 'Cajamarca', 'Cutervo', 'Cutervo'],
            ['060701', 'Cajamarca', 'Hualgayoc', 'Bambamarca'],
            ['060801', 'Cajamarca', 'Jaén', 'Jaén'],
            ['060901', 'Cajamarca', 'San Ignacio', 'San Ignacio'],
            ['061001', 'Cajamarca', 'San Marcos', 'Pedro Gálvez'],
            ['061101', 'Cajamarca', 'San Miguel', 'San Miguel'],
            ['061201', 'Cajamarca', 'San Pablo', 'San Pablo'],
            ['061301', 'Cajamarca', 'Santa Cruz', 'Santa Cruz'],

            // --- Callao ---
            ['070101', 'Callao', 'Callao', 'Callao'],
            ['070102', 'Callao', 'Callao', 'Bellavista'],
            ['070103', 'Callao', 'Callao', 'Carmen de la Legua'],
            ['070104', 'Callao', 'Callao', 'La Perla'],
            ['070105', 'Callao', 'Callao', 'La Punta'],
            ['070106', 'Callao', 'Callao', 'Mi Perú'],
            ['070107', 'Callao', 'Callao', 'Ventanilla'],

            // --- Cusco ---
            ['080101', 'Cusco', 'Cusco', 'Cusco'],
            ['080201', 'Cusco', 'Acomayo', 'Acomayo'],
            ['080301', 'Cusco', 'Anta', 'Anta'],
            ['080401', 'Cusco', 'Calca', 'Calca'],
            ['080501', 'Cusco', 'Canas', 'Yanaoca'],
            ['080601', 'Cusco', 'Canchis', 'Sicuani'],
            ['080701', 'Cusco', 'Chumbivilcas', 'Santo Tomás'],
            ['080801', 'Cusco', 'Espinar', 'Yauri'],
            ['080901', 'Cusco', 'La Convención', 'Quillabamba'],
            ['081001', 'Cusco', 'Paruro', 'Paruro'],
            ['081101', 'Cusco', 'Paucartambo', 'Paucartambo'],
            ['081201', 'Cusco', 'Quispicanchi', 'Urcos'],
            ['081301', 'Cusco', 'Urubamba', 'Urubamba'],

            // --- Huancavelica ---
            ['090101', 'Huancavelica', 'Huancavelica', 'Huancavelica'],
            ['090201', 'Huancavelica', 'Acobamba', 'Acobamba'],
            ['090301', 'Huancavelica', 'Angaraes', 'Lircay'],
            ['090401', 'Huancavelica', 'Castrovirreyna', 'Castrovirreyna'],
            ['090501', 'Huancavelica', 'Churcampa', 'Churcampa'],
            ['090601', 'Huancavelica', 'Huaytará', 'Huaytará'],
            ['090701', 'Huancavelica', 'Tayacaja', 'Pampas'],

            // --- Huánuco ---
            ['100101', 'Huánuco', 'Huánuco', 'Huánuco'],
            ['100201', 'Huánuco', 'Ambo', 'Ambo'],
            ['100301', 'Huánuco', 'Dos de Mayo', 'La Unión'],
            ['100401', 'Huánuco', 'Huacaybamba', 'Huacaybamba'],
            ['100501', 'Huánuco', 'Huamalíes', 'Llata'],
            ['100601', 'Huánuco', 'Leoncio Prado', 'Tingo María'],
            ['100701', 'Huánuco', 'Marañón', 'Huacrachuco'],
            ['100801', 'Huánuco', 'Pachitea', 'Panao'],
            ['100901', 'Huánuco', 'Puerto Inca', 'Puerto Inca'],
            ['101001', 'Huánuco', 'Lauricocha', 'Jesús'],
            ['101101', 'Huánuco', 'Yarowilca', 'Chavinillo'],

            // --- Ica ---
            ['110101', 'Ica', 'Ica', 'Ica'],
            ['110201', 'Ica', 'Chincha', 'Chincha Alta'],
            ['110301', 'Ica', 'Nazca', 'Nazca'],
            ['110401', 'Ica', 'Palpa', 'Palpa'],
            ['110501', 'Ica', 'Pisco', 'Pisco'],

            // --- Junín ---
            ['120101', 'Junín', 'Huancayo', 'Huancayo'],
            ['120201', 'Junín', 'Chanchamayo', 'Chanchamayo'],
            ['120301', 'Junín', 'Chupaca', 'Chupaca'],
            ['120401', 'Junín', 'Concepción', 'Concepción'],
            ['120501', 'Junín', 'Jauja', 'Jauja'],
            ['120601', 'Junín', 'Junín', 'Junín'],
            ['120701', 'Junín', 'Satipo', 'Satipo'],
            ['120801', 'Junín', 'Tarma', 'Tarma'],
            ['120901', 'Junín', 'Yauli', 'La Oroya'],

            // --- La Libertad ---
            ['130101', 'La Libertad', 'Trujillo', 'Trujillo'],
            ['130201', 'La Libertad', 'Ascope', 'Ascope'],
            ['130301', 'La Libertad', 'Bolívar', 'Bolívar'],
            ['130401', 'La Libertad', 'Chepén', 'Chepén'],
            ['130501', 'La Libertad', 'Julcán', 'Julcán'],
            ['130601', 'La Libertad', 'Otuzco', 'Otuzco'],
            ['130701', 'La Libertad', 'Pacasmayo', 'San Pedro de Lloc'],
            ['130801', 'La Libertad', 'Pataz', 'Tayabamba'],
            ['130901', 'La Libertad', 'Sánchez Carrión', 'Huamachuco'],
            ['131001', 'La Libertad', 'Santiago de Chuco', 'Santiago de Chuco'],
            ['131101', 'La Libertad', 'Gran Chimú', 'Cascas'],
            ['131201', 'La Libertad', 'Virú', 'Virú'],

            // --- Lambayeque ---
            ['140101', 'Lambayeque', 'Chiclayo', 'Chiclayo'],
            ['140201', 'Lambayeque', 'Ferreñafe', 'Ferreñafe'],
            ['140301', 'Lambayeque', 'Lambayeque', 'Lambayeque'],

            // --- Loreto ---
            ['160101', 'Loreto', 'Maynas', 'Iquitos'],
            ['160201', 'Loreto', 'Alto Amazonas', 'Yurimaguas'],
            ['160301', 'Loreto', 'Loreto', 'Nauta'],
            ['160401', 'Loreto', 'Mariscal Ramón Castilla', 'Ramón Castilla'],
            ['160501', 'Loreto', 'Requena', 'Requena'],
            ['160601', 'Loreto', 'Ucayali', 'Contamana'],
            ['160701', 'Loreto', 'Datem del Marañón', 'Barranca'],
            ['160801', 'Loreto', 'Putumayo', 'San Antonio del Estrecho'],

            // --- Madre de Dios ---
            ['170101', 'Madre de Dios', 'Tambopata', 'Tambopata'],
            ['170201', 'Madre de Dios', 'Manu', 'Manu'],
            ['170301', 'Madre de Dios', 'Tahuamanu', 'Iñapari'],

            // --- Moquegua ---
            ['180101', 'Moquegua', 'Mariscal Nieto', 'Moquegua'],
            ['180201', 'Moquegua', 'General Sánchez Cerro', 'Omate'],
            ['180301', 'Moquegua', 'Ilo', 'Ilo'],

            // --- Pasco ---
            ['190101', 'Pasco', 'Pasco', 'Chaupimarca'],
            ['190201', 'Pasco', 'Daniel Alcides Carrión', 'Yanahuanca'],
            ['190301', 'Pasco', 'Oxapampa', 'Oxapampa'],

            // --- Piura ---
            ['200101', 'Piura', 'Piura', 'Piura'],
            ['200201', 'Piura', 'Ayabaca', 'Ayabaca'],
            ['200301', 'Piura', 'Huancabamba', 'Huancabamba'],
            ['200401', 'Piura', 'Morropón', 'Chulucanas'],
            ['200501', 'Piura', 'Paita', 'Paita'],
            ['200601', 'Piura', 'Sullana', 'Sullana'],
            ['200701', 'Piura', 'Talara', 'Talara'],
            ['200801', 'Piura', 'Sechura', 'Sechura'],

            // --- Puno ---
            ['210101', 'Puno', 'Puno', 'Puno'],
            ['210201', 'Puno', 'Azángaro', 'Azángaro'],
            ['210301', 'Puno', 'Carabaya', 'Macusani'],
            ['210401', 'Puno', 'Chucuito', 'Juli'],
            ['210501', 'Puno', 'El Collao', 'Ilave'],
            ['210601', 'Puno', 'Huancané', 'Huancané'],
            ['210701', 'Puno', 'Lampa', 'Lampa'],
            ['210801', 'Puno', 'Melgar', 'Ayaviri'],
            ['210901', 'Puno', 'Moho', 'Moho'],
            ['211001', 'Puno', 'San Antonio de Putina', 'Putina'],
            ['211101', 'Puno', 'San Román', 'Juliaca'],
            ['211201', 'Puno', 'Sandia', 'Sandia'],
            ['211301', 'Puno', 'Yunguyo', 'Yunguyo'],

            // --- San Martín ---
            ['220101', 'San Martín', 'Moyobamba', 'Moyobamba'],
            ['220201', 'San Martín', 'Bellavista', 'Bellavista'],
            ['220301', 'San Martín', 'El Dorado', 'San José de Sisa'],
            ['220401', 'San Martín', 'Huallaga', 'Saposoa'],
            ['220501', 'San Martín', 'Lamas', 'Lamas'],
            ['220601', 'San Martín', 'Mariscal Cáceres', 'Juanjuí'],
            ['220701', 'San Martín', 'Picota', 'Picota'],
            ['220801', 'San Martín', 'Rioja', 'Rioja'],
            ['220901', 'San Martín', 'San Martín', 'Tarapoto'],
            ['221001', 'San Martín', 'Tocache', 'Tocache'],

            // --- Tacna ---
            ['230101', 'Tacna', 'Tacna', 'Tacna'],
            ['230201', 'Tacna', 'Candarave', 'Candarave'],
            ['230301', 'Tacna', 'Jorge Basadre', 'Locumba'],
            ['230401', 'Tacna', 'Tarata', 'Tarata'],

            // --- Tumbes ---
            ['240101', 'Tumbes', 'Tumbes', 'Tumbes'],
            ['240201', 'Tumbes', 'Contralmirante Villar', 'Zorritos'],
            ['240301', 'Tumbes', 'Zarumilla', 'Zarumilla'],

            // --- Ucayali ---
            ['250101', 'Ucayali', 'Coronel Portillo', 'Callería'],
            ['250201', 'Ucayali', 'Atalaya', 'Raymondi'],
            ['250301', 'Ucayali', 'Padre Abad', 'Padre Abad'],
            ['250401', 'Ucayali', 'Purús', 'Purús'],
        ];

        foreach ($ubigeos as [$code, $department, $province, $district]) {
            Ubigeo::firstOrCreate(
                ['code' => $code],
                [
                    'code' => $code,
                    'department' => $department,
                    'province' => $province,
                    'district' => $district,
                ]
            );
        }
    }
}
