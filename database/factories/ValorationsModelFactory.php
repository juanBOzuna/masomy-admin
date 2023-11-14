<?php
namespace Database\Factories;

use App\Models\ValorationsModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class ValorationsModelFactory extends Factory
{
    protected $model = ValorationsModel::class;

    public function definition()
    {
        return [
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->text(200),
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'product_id' => function () {
                return \App\Models\ProductsModel::factory()->create()->id;
            },
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}