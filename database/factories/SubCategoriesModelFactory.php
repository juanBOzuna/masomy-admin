<?php

namespace Database\Factories;

use App\Models\CategoriesModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubCategoriesModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'categoria_id' => function () {
                return CategoriesModel::factory()->create()->id;
            },
        ];
    }
}
