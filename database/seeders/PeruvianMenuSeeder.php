<?php

namespace Database\Seeders;

use App\Models\Inventory\Product;
use App\Models\Inventory\ProductCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PeruvianMenuSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Categorías del menú peruano. Se crean si no existen.
     *
     * @var array<int, array{name: string, description: string}>
     */
    private array $categories = [
        ['name' => 'Bebidas', 'description' => 'Gaseosas, jugos, aguas, refrescos y tragos'],
        ['name' => 'Postres', 'description' => 'Dulces, helados y postres peruanos'],
        ['name' => 'Comida Caliente', 'description' => 'Platos que se sirven calientes: pollo, saltados y chifa'],
        ['name' => 'Comida Criolla', 'description' => 'Platos de la cocina peruana criolla'],
        ['name' => 'Entradas', 'description' => 'Entradas, aperitivos y pasantes'],
        ['name' => 'Sopas', 'description' => 'Sopas, caldos y cremas'],
        ['name' => 'Ceviches', 'description' => 'Ceviches, tiraditos y platos fríos de pescado'],
        ['name' => 'Parrillas', 'description' => 'Carnes a la brasa y parrilla'],
    ];

    /**
     * Menú de productos. name debe ser único, se usa firstOrCreate.
     *
     * @var array<int, array{name: string, description: string, price: float, category: string, unit?: string}>
     */
    private array $menu = [
        // Entradas
        ['name' => 'Papa a la huancaína', 'description' => 'Papa sancochada con ají amarillo y queso fresco', 'price' => 18, 'category' => 'Entradas'],
        ['name' => 'Causa limeña', 'description' => 'Causa rellena de pollo y palta con mayonesa', 'price' => 22, 'category' => 'Entradas'],
        ['name' => 'Tamal criollo', 'description' => 'Tamal de maíz con carne de cerdo y aceituna', 'price' => 12, 'category' => 'Entradas'],
        ['name' => 'Anticuchos (4 und.)', 'description' => 'Corazón de res marinado con ají panca y papa dorada', 'price' => 24, 'category' => 'Entradas'],
        ['name' => 'Choritos a la chalaca', 'description' => 'Mejillones frescos con cebolla, tomate, limón y maíz', 'price' => 20, 'category' => 'Entradas'],
        ['name' => 'Pan con chicharrón', 'description' => 'Pan con chicharrón de cerdo, camote y salsa criolla', 'price' => 14, 'category' => 'Entradas'],
        ['name' => 'Choclo con queso', 'description' => 'Choclo serrano sancochado con queso fresco', 'price' => 12, 'category' => 'Entradas'],
        ['name' => 'Prove (ceviche de pallares)', 'description' => 'Ceviche de pallares con cancha y camote', 'price' => 14, 'category' => 'Entradas'],

        // Sopas
        ['name' => 'Sopa criolla', 'description' => 'Sopa de fideo con carne, ají panca, huevo y pan tostado', 'price' => 22, 'category' => 'Sopas'],
        ['name' => 'Caldo de gallina', 'description' => 'Caldo de gallina con fideo, papa, huevo y hierbabuena', 'price' => 24, 'category' => 'Sopas'],
        ['name' => 'Aguadito de pollo', 'description' => 'Sopa de arroz y pollo con culantro y limón', 'price' => 20, 'category' => 'Sopas'],
        ['name' => 'Chupe de camarones', 'description' => 'Chupe de camarones, papa, arroz, huevo y queso', 'price' => 45, 'category' => 'Sopas'],
        ['name' => 'Parihuela', 'description' => 'Sopa de mariscos y pescado con rocoto y ají amarillo', 'price' => 48, 'category' => 'Sopas'],
        ['name' => 'Menestrón', 'description' => 'Sopa espesa de menestras con fideo y albóndiga', 'price' => 18, 'category' => 'Sopas'],
        ['name' => 'Caldo de res', 'description' => 'Caldo de res con yuca, zapallo, papa y arroz', 'price' => 20, 'category' => 'Sopas'],

        // Ceviches
        ['name' => 'Ceviche clásico', 'description' => 'Pescado fresco en leche de tigre con cebolla, camote y cancha', 'price' => 38, 'category' => 'Ceviches'],
        ['name' => 'Ceviche mixto', 'description' => 'Pescado, calamar, pulpo y camarón en leche de tigre', 'price' => 46, 'category' => 'Ceviches'],
        ['name' => 'Ceviche de conchas negras', 'description' => 'Conchas negras frescas con limón y ají limo', 'price' => 55, 'category' => 'Ceviches'],
        ['name' => 'Ceviche de camarón', 'description' => 'Camarones frescos en leche de tigre con cebolla y cancha', 'price' => 52, 'category' => 'Ceviches'],
        ['name' => 'Tiradito de pescado', 'description' => 'Láminas de pescado con crema de ají amarillo y ají limo', 'price' => 34, 'category' => 'Ceviches'],
        ['name' => 'Leche de tigre', 'description' => 'Leche de tigre de pescado con canchita y choclo', 'price' => 28, 'category' => 'Ceviches'],
        ['name' => 'Pulpo al olivar', 'description' => 'Pulpo cocido con crema de aceitunas y camote', 'price' => 48, 'category' => 'Ceviches'],
        ['name' => 'Chicharrón de pescado', 'description' => 'Pescado frito crujiente con salsa tártara y camote', 'price' => 36, 'category' => 'Ceviches'],

        // Comida Criolla
        ['name' => 'Lomo saltado', 'description' => 'Lomo de res salteado con cebolla, tomate y papa frita', 'price' => 38, 'category' => 'Comida Criolla'],
        ['name' => 'Ají de gallina', 'description' => 'Gallina deshilachada en crema de ají amarillo con arroz y papa', 'price' => 32, 'category' => 'Comida Criolla'],
        ['name' => 'Arroz con pollo', 'description' => 'Arroz verde con pollo, culantro, chicha y papas', 'price' => 30, 'category' => 'Comida Criolla'],
        ['name' => 'Seco de res con frejoles', 'description' => 'Res guisada con culantro, ají verde, frejoles y arroz', 'price' => 36, 'category' => 'Comida Criolla'],
        ['name' => 'Cau cau de mondongo', 'description' => 'Mondongo guisado con papa amarilla y palillo', 'price' => 26, 'category' => 'Comida Criolla'],
        ['name' => 'Tallarines verdes con bistec', 'description' => 'Tallarines en salsa de albahaca y espinaca con bistec', 'price' => 28, 'category' => 'Comida Criolla'],
        ['name' => 'Carapulcra con sopa seca', 'description' => 'Carapulcra de papa seca con sopa seca de tallarín', 'price' => 32, 'category' => 'Comida Criolla'],
        ['name' => 'Arroz con mariscos', 'description' => 'Arroz guisado con mariscos, ají panca y culantro', 'price' => 42, 'category' => 'Comida Criolla'],
        ['name' => 'Escabeche de pescado', 'description' => 'Pescado marinado con cebolla, huevo y papa sancochada', 'price' => 35, 'category' => 'Comida Criolla'],

        // Comida Caliente
        ['name' => 'Pollo a la brasa 1/4', 'description' => 'Cuarto de pollo a la brasa con papas fritas y ensalada', 'price' => 28, 'category' => 'Comida Caliente'],
        ['name' => 'Pollo a la brasa 1/2', 'description' => 'Medio pollo a la brasa con papas fritas y ensalada', 'price' => 54, 'category' => 'Comida Caliente'],
        ['name' => 'Chaufa especial', 'description' => 'Arroz chaufa con pollo, cerdo, camarón y huevo', 'price' => 34, 'category' => 'Comida Caliente'],
        ['name' => 'Saltado de pollo', 'description' => 'Pollo salteado con verduras y papa frita', 'price' => 28, 'category' => 'Comida Caliente'],
        ['name' => 'Bistec a lo pobre', 'description' => 'Bistec de res con huevo frito, papa frita y arroz', 'price' => 40, 'category' => 'Comida Caliente'],
        ['name' => 'Chicharrón de cerdo', 'description' => 'Chicharrón de cerdo crocante con camote y salsa criolla', 'price' => 30, 'category' => 'Comida Caliente'],

        // Parrillas
        ['name' => 'Parrilla criolla (2 pers.)', 'description' => 'Lomo, pollo, churi, chorizo y papas a la parrilla', 'price' => 85, 'category' => 'Parrillas'],
        ['name' => 'Lomo fino a la parrilla', 'description' => 'Lomo fino a la parrilla con papas y ensalada', 'price' => 45, 'category' => 'Parrillas'],
        ['name' => 'Churrasco con chimichurri', 'description' => 'Churrasco a la parrilla con chimichurri y papas doradas', 'price' => 42, 'category' => 'Parrillas'],
        ['name' => 'Pechuga a la parrilla', 'description' => 'Pechuga de pollo a la parrilla con arroz y ensalada', 'price' => 30, 'category' => 'Parrillas'],
        ['name' => 'Chorizo a la parrilla', 'description' => 'Chorizo a la parrilla con papa dorada y salsa criolla', 'price' => 25, 'category' => 'Parrillas'],

        // Bebidas
        ['name' => 'Inca Kola personal', 'description' => 'Inca Kola helada en botella personal', 'price' => 5, 'category' => 'Bebidas', 'unit' => 'botella'],
        ['name' => 'Coca-Cola personal', 'description' => 'Coca-Cola helada en botella personal', 'price' => 5, 'category' => 'Bebidas', 'unit' => 'botella'],
        ['name' => 'Agua mineral', 'description' => 'Agua mineral sin gas', 'price' => 4, 'category' => 'Bebidas', 'unit' => 'botella'],
        ['name' => 'Chicha morada', 'description' => 'Chicha morada de maíz morado con piña', 'price' => 7, 'category' => 'Bebidas', 'unit' => 'vaso'],
        ['name' => 'Refresco de maracuyá', 'description' => 'Refresco natural de maracuyá', 'price' => 7, 'category' => 'Bebidas', 'unit' => 'vaso'],
        ['name' => 'Limonada frozen', 'description' => 'Limonada helada con hielo raspado y hierbabuena', 'price' => 10, 'category' => 'Bebidas', 'unit' => 'vaso'],
        ['name' => 'Café pasado', 'description' => 'Café peruano pasado en jarra', 'price' => 6, 'category' => 'Bebidas', 'unit' => 'taza'],
        ['name' => 'Pisco sour', 'description' => 'Pisco peruano con limón, jarabe y clara de huevo', 'price' => 18, 'category' => 'Bebidas', 'unit' => 'vaso'],

        // Postres
        ['name' => 'Suspiro a la limeña', 'description' => 'Manjarblanco con merengue dorado y canela', 'price' => 14, 'category' => 'Postres'],
        ['name' => 'Picarones (3 und.)', 'description' => 'Picarones de zapallo con miel de chancaca', 'price' => 10, 'category' => 'Postres'],
        ['name' => 'Mazamorra morada', 'description' => 'Mazamorra de maíz morado con frutas secas', 'price' => 9, 'category' => 'Postres'],
        ['name' => 'Arroz con leche', 'description' => 'Arroz con leche cremoso con canela', 'price' => 9, 'category' => 'Postres'],
        ['name' => 'Crema volteada', 'description' => 'Crema volteada de leche con caramelo', 'price' => 12, 'category' => 'Postres'],
        ['name' => 'Alfajor peruano', 'description' => 'Alfajor de manjar blanco espolvoreado con azúcar', 'price' => 6, 'category' => 'Postres'],
        ['name' => 'King Kong', 'description' => 'King Kong de manjar blanco con nueces y pasas', 'price' => 15, 'category' => 'Postres'],
        ['name' => 'Queso helado', 'description' => 'Queso helado arequipeño con canela y clavo', 'price' => 8, 'category' => 'Postres'],
        ['name' => 'Helado de lúcuma', 'description' => 'Helado artesanal de lúcuma', 'price' => 12, 'category' => 'Postres'],
        ['name' => 'Turrón de Doña Pepa', 'description' => 'Turrón de anís con miel de chancaca', 'price' => 12, 'category' => 'Postres'],
    ];

    public function run(): void
    {
        $categoryIds = [];

        foreach ($this->categories as $category) {
            $categoryIds[$category['name']] = ProductCategory::firstOrCreate(
                ['name' => $category['name']],
                $category
            )->id;
        }

        foreach ($this->menu as $item) {
            $categoryId = $categoryIds[$item['category']] ?? null;

            Product::firstOrCreate(
                ['name' => $item['name']],
                [
                    'name' => $item['name'],
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'cost_price' => round($item['price'] * 0.5, 2),
                    'sale_price' => $item['price'],
                    'image_url' => null,
                    'stock' => 0,
                    'unit_of_measure' => $item['unit'] ?? 'porción',
                    'product_category_id' => $categoryId,
                    'purchase_category_id' => null,
                    'type' => 'dish',
                    'is_pos_item' => true,
                    'status' => true,
                ]
            );
        }
    }
}
