<?php

namespace Dive\Wishlist\Models\Concerns;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;

trait InteractsWithWishlist
{
    public function unwish(Wishable $wishable): bool
    {
        return app('wishlist')->remove($wishable);
    }

    public function wish(Wishable $wishable): Wish
    {
        return app('wishlist')->add($wishable);
    }

    public function wishes(): WishCollection
    {
        return app('wishlist')->all();
    }
}
