<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Contracts\Wishable;
use Tests\Fakes\Factories\ProductFactory;
use Tests\Fakes\Factories\SampleFactory;
use Tests\Fakes\Factories\UserFactory;
use Tests\Fakes\Product;
use Tests\Fakes\Sample;
use Tests\Fakes\User;

function product(): Product
{
    return ProductFactory::new()->create();
}

function sample(): Sample
{
    return SampleFactory::new()->create();
}

function user(): User
{
    return UserFactory::new()->create();
}

function wishable(): Wishable
{
    return call_user_func(['\Tests\product', '\Tests\sample'][rand(0, 1)]);
}
