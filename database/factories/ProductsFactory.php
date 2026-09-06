<?php

namespace Database\Factories;

use App\Models\Products;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductsFactory extends Factory
{
    protected $model = Products::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => $name,
            'sku' => strtoupper($this->faker->bothify('SKU-####-??')),
            'price' => $this->faker->numberBetween(500, 20000),
            'stock' => $this->faker->numberBetween(0, 200),
            'category' => $this->faker->randomElement(['Office', 'Tech', 'Food', 'Furniture']),
            'brand' => $this->faker->company(),
            'on_sale' => $this->faker->boolean(20),
            'description' => $this->faker->sentence(12),
        ];
    }
}
