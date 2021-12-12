<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Events\WishlistTouched;
use Dive\Wishlist\InMemoryWishlist;
use Dive\Wishlist\WishlistEventDecorator;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;

it('dispatches a touched event for dirty operations', function (string $method, $arg = null) {
    $dispatcher = Mockery::mock(Dispatcher::class);
    $dispatcher->shouldReceive('dispatch')
        ->once()
        ->withArgs(fn ($e) => $e instanceof WishlistTouched);

    WishlistEventDecorator::make(InMemoryWishlist::make(), $dispatcher)->{$method}(value($arg));
})->with([
    ['add', fn () => wishable()],
    ['purge'],
    ['remove', fn () => wishable()],
]);
