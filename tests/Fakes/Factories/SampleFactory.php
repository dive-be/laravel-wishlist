<?php declare(strict_types=1);

namespace Tests\Fakes\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\Fakes\Sample;

final class SampleFactory extends Factory
{
    protected $model = Sample::class;

    public function definition(): array
    {
        return [
            'purveyor_id' => PurveyorFactory::new(),
            'name' => $this->faker->word(),
        ];
    }
}
