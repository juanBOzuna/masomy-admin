<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ValorationsModel;

class ValorationsSeeder extends Seeder
{
    public function run()
    {
        // Código para generar datos de valoraciones
        // Ejemplo:
        ValorationsModel::factory(5)->create();
    }
}