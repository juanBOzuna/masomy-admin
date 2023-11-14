<?php

namespace Database\Seeders;

use App\Models\CategoriesModel;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        CategoriesModel::factory(5)->create();
    }
}
