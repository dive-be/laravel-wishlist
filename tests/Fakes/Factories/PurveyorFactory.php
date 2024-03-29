<?php declare(strict_types=1);

namespace Tests\Fakes\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Fakes\Purveyor;

final class PurveyorFactory extends Factory
{
    protected $model = Purveyor::class;

    public function definition(): array
    {
        return [];
    }
}
