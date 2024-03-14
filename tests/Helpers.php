<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Contracts\Wishable;
use Tests\Fakes\Factories\ProductFactory;
use Tests\Fakes\Factories\SampleFactory;
use Tests\Fakes\Factories\UserFactory;
use Tests\Fakes\Product;
use Tests\Fakes\Sample;
use Tests\Fakes\User;

trait Helpers
{
    protected static function product(): Product
    {
        return ProductFactory::new()->create();
    }

    protected static function sample(): Sample
    {
        return SampleFactory::new()->create();
    }

    protected static function user(): User
    {
        return UserFactory::new()->create();
    }

    protected static function wishable(): Wishable
    {
        return call_user_func([self::product(...), self::sample(...)][rand(0, 1)]);
    }
}
