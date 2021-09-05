<?php

namespace Tests;

use Dive\Wishlist\Actions\MigrateWishesAction;
use Dive\Wishlist\Listeners;
use Dive\Wishlist\Middleware;
use Dive\Wishlist\WishlistManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\spy;

it('can migrate the wishes from the cookie driver to the eloquent driver', function () {
    actingAs(user());

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

it('can be invoked from a listener', function () {
    $action = spy(MigrateWishesAction::class);

    (new Listeners\MigrateWishes(new Request(), $wishlist = app('wishlist')))
        ->handle($event = new Login('web', user(), true));

    $action->shouldNotHaveReceived('execute');

    (new Listeners\MigrateWishes(request(), $wishlist))->handle($event);

    $action->shouldHaveReceived('execute');
});

it('can be invoked from middleware', function () {
    $action = spy(MigrateWishesAction::class);
    $middleware = app(Middleware\MigrateWishes::class);
    $next = fn () => 'next';

    expect($middleware->handle($emptyRequest = new Request(), $next))->toBe('next');

    $action->shouldNotHaveReceived('execute');

    expect($middleware->handle($cookieRequest = request(), $next))->toBe('next');

    $action->shouldNotHaveReceived('execute');

    actingAs(user());

    expect($middleware->handle($emptyRequest, $next))->toBe('next');

    $action->shouldNotHaveReceived('execute');

    expect($middleware->handle($cookieRequest, $next))->toBe('next');

    $action->shouldHaveReceived('execute');
});

function request(): Request
{
    return new Request(cookies: [app('wishlist')->config('cookie.name') => 'Tasteful cookie']);
}
