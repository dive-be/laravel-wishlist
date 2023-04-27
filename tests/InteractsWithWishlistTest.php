<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Models\Wish;

test('user can (un)wish something', function () {
    $this->assertDatabaseCount(Wish::class, 0);

    ($user = user())->wish($product = product());

    expect($wish = Wish::first())->toBeInstanceOf(Wish::class);

    $user->unwish($product);

    expect($wish->refresh()->trashed())->toBeTrue();
});

test('user can retrieve his/her own wishlist', function () {
    ($user = user())->wish(product());
    $user->wish($product = product());
    $user->wish(sample());
    $user->wish(product());
    $user->unwish($product);

    expect($user->wishes())->toHaveCount(3);
});
