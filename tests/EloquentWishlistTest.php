<?php

namespace Tests;

use Dive\Wishlist\EloquentWishlist;
use Dive\Wishlist\Models\Wish as Model;
use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;

it('can merge a collection of wishes with its own', function () {
    $wishlist = EloquentWishlist::make(new Model(), user()->getKey(), '*');

    $wishlist->add(product());
    $sampleA = $wishlist->add(sample());
    $productA = $wishlist->add(product());

    $wishlist->merge(WishCollection::make([
        $sampleA, // duplicate
        Wish::make('1234', sample()),
        $productA, // duplicate
        Wish::make('5678', product()),
    ]));

    expect($wishlist->count())->toBe(5);
});
