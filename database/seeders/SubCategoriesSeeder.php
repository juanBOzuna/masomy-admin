<?php

namespace Database\Seeders;

use App\Models\SubCategoriesModel;
use Illuminate\Database\Seeder;

class SubCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        SubCategoriesModel::factory(10)->create();
    }
}
