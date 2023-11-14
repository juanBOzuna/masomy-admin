<?php

namespace Database\Factories;

use App\Models\ProductsModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductsModelFactory extends Factory
{
    protected $model = ProductsModel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->text(50),
            'price' => $this->faker->randomFloat(2, 10, 100),
            'description' => $this->faker->text(200),
            'stock' => $this->faker->numberBetween(0, 100),
            'sub_categorie_id' => $this->faker->text(10),
            'picture' => $this->faker->imageUrl(),
            'discount' => $this->faker->randomFloat(2, 0, 20),
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}