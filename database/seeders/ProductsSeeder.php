<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductsModel;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        // Código para generar datos de productos
        // Ejemplo:
        ProductsModel::factory(5)->create();
    }
}