<?php

namespace Tests;

use Dive\Wishlist\CookieWishlist;
use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;

it('retrieves existing wishes from the correct cookie', function () {
    expect(
        wishlist([
            Wish::make(1, wishable()),
            Wish::make(2, wishable()),
        ])->count()
    )->toBe(2);
});

it('hydrates the collection when necessary', function () {
    expect(
        wishlist([
            Wish::make(1, wishable()),
            Wish::make(2, wishable()),
        ])->all()
    )->each->toBeInstanceOf(Wish::class);
});

it('enqueues a new cookie when modifications happen', function () {
    $wishlist = wishlist();

    expect($this->jar->getQueuedCookies())->toBeEmpty();

    $wishlist->add($wishable = wishable());

    expect($this->jar->getQueuedCookies())->toHaveCount(1);

    $this->jar->flushQueuedCookies();

    $wishlist->add($wishable);

    expect($this->jar->getQueuedCookies())->toHaveCount(0);

    $this->jar->flushQueuedCookies();

    $wishlist->remove($wishable);

    expect($this->jar->getQueuedCookies())->toHaveCount(1);

    $this->jar->flushQueuedCookies();

    $wishlist->remove($wishable);

    expect($this->jar->getQueuedCookies())->toHaveCount(0);
});

function wishlist(array $state = []): CookieWishlist
{
    return CookieWishlist::make(
        test()->jar = new CookieJar(),
        test()->request = new Request(cookies: [
            'wishlist' => serialize(WishCollection::make($state)),
        ]),
        [
            'domain' => '.localhost',
            'max_age' => 1337,
            'name' => 'wishlist',
        ]
    );
}
