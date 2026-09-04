<?php

namespace Database\Factories\Sales;

use App\Models\Restaurant\Table;
use App\Models\Sales\Sale;
use App\Models\Sales\SaleTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SaleTable>
 */
class SaleTableFactory extends Factory
{
    protected $model = SaleTable::class;

    public function definition(): array
    {
        return [
            'sale_id' => Sale::factory(),
            'table_id' => Table::factory(),
        ];
    }
}
