<?php declare(strict_types=1);

namespace Tests\Fakes\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Fakes\Variant;

final class VariantFactory extends Factory
{
    protected $model = Variant::class;

    public function definition(): array
    {
        return [];
    }
}
