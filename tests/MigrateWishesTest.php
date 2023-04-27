<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Actions\MigrateWishesAction;
use Dive\Wishlist\WishlistManager;

it('can migrate the wishes from the cookie driver to the eloquent driver', function () {
    $this->actingAs(user());

    $manager = app(WishlistManager::class);

    $cookie = $manager->driver(WishlistManager::COOKIE);
    $cookie->add(product());
    $cookie->add(sample());
    $cookie->add(product());

    $eloquent = $manager->driver(WishlistManager::ELOQUENT);

    expect($cookie->count())->toBe(3);
    expect($eloquent->count())->toBe(0);

    (new MigrateWishesAction($manager))->execute();

    expect($cookie->count())->toBe(0);
    expect($eloquent->count())->toBe(3);
});
