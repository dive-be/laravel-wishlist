<?php

namespace Tests\Fakes\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Tests\Fakes\Product;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'variant_id' => VariantFactory::new(),
            'name' => $this->faker->word(),
            'sku' => Str::random(),
        ];
    }
}
