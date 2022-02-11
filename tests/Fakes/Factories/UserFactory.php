<?php declare(strict_types=1);

namespace Tests\Fakes\Factories;

use Orchestra\Testbench\Factories\UserFactory as Factory;
use Tests\Fakes\User;

class UserFactory extends Factory
{
    protected $model = User::class;
}
