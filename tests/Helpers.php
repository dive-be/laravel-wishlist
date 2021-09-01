<?php

namespace Tests;

use Dive\Wishlist\Contracts\Wishable;
use Illuminate\Foundation\Auth\User;
use Orchestra\Testbench\Factories\UserFactory;
use Tests\Fakes\Factories\ProductFactory;
use Tests\Fakes\Factories\SampleFactory;
use Tests\Fakes\Product;
use Tests\Fakes\Sample;

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
