<?php declare(strict_types=1);

namespace Tests;

use Closure;
use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\CookieWishlist;
use Dive\Wishlist\EloquentWishlist;
use Dive\Wishlist\InMemoryWishlist;
use Dive\Wishlist\Models\Wish as Model;
use Dive\Wishlist\Wish;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

it('can add a new wish', function (Wishlist $wishlist) {
    expect($wishlist->isEmpty())->toBeTrue();

    $wish = $wishlist->add($wishable = wishable());

    expect($wishlist->isNotEmpty())->toBeTrue();
    expect($wish)->toBeInstanceOf(Wish::class);
    expect($wish->id)->not->toBeNull();
    expect($wish->wishable->getKey())->toBe($wishable->getKey());
    expect($wish->wishable->getMorphClass())->toBe($wishable->getMorphClass());
})->with('wishlists');

it('does not create a new wish if it already exists', function (Wishlist $wishlist) {
    $wishA = $wishlist->add(wishable());
    $wishB = $wishlist->add($wishA->wishable);

    expect($wishB->id)->toBe($wishA->id);
})->with('wishlists');

it('can retrieve all wishes', function (Wishlist $wishlist) {
    $wishes = Collection::make([
        $wishlist->add(wishable()),
        $wishlist->add(wishable()),
        $wishlist->add(wishable()),
    ]);

    $wishlist->all()->each(static function (Wish $wish, int $idx) use ($wishes) {
        expect($wish->id)->toBe($wishes->get($idx)->id);
        expect($wish->wishable->getKey())->toBe($wishes->get($idx)->wishable->getKey());
    });
})->with('wishlists');

it('can retrieve the count', function (Wishlist $wishlist) {
    expect($wishlist->count())->toBe(0);

    $wishlist->add(wishable());

    expect($wishlist->count())->toBe(1);
})->with('wishlists');

it('can determine emptiness', function (Wishlist $wishlist) {
    expect($wishlist->isNotEmpty())->toBeFalse();
    expect($wishlist->isEmpty())->toBeTrue();

    $wishlist->add(wishable());

    expect($wishlist->isNotEmpty())->toBeTrue();
    expect($wishlist->isEmpty())->toBeFalse();
})->with('wishlists');

it('can determine if a wishable has already been wished for', function (Wishlist $wishlist) {
    expect($wishlist->has($wishable = wishable()))->toBeFalse();

    $wishlist->add($wishable);

    expect($wishlist->has($wishable))->toBeTrue();
})->with('wishlists');

it('can be purged', function (Wishlist $wishlist) {
    $wishlist->add(wishable());
    $wishlist->add(wishable());

    expect($wishlist->count())->toBe(2);

    $purged = $wishlist->purge();

    expect($wishlist->count())->toBe(0);
    expect($purged)->toBe(2);
})->with('wishlists');

it('can remove a wish', function (Wishlist $wishlist, Closure $valueRetriever) {
    $wish = $wishlist->add(wishable());

    $resultA = $wishlist->remove($valueRetriever($wish));
    $resultB = $wishlist->remove(wishable());

    expect($resultA)->toBeTrue();
    expect($resultB)->toBeFalse();
    expect($wishlist->isEmpty())->toBeTrue();
})->with('wishlists')->with([
    [fn ($wish) => $wish],
    [fn ($wish) => $wish->id],
    [fn ($wish) => $wish->wishable],
]);

dataset('wishlists', [
    [fn () => InMemoryWishlist::make()],
    [fn () => EloquentWishlist::make(new Model(), user()->getKey(), '*')],
    [fn () => CookieWishlist::make(new CookieJar(), new Request(), [
        'domain' => '.localhost',
        'max_age' => 1337,
        'name' => 'wishlist',
    ])],
]);
