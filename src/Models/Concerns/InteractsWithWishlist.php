<?php

namespace Dive\Wishlist\Models\Concerns;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;

trait InteractsWithWishlist
{
    public function unwish(Wishable $wishable): bool
    {
        return wishlist()->forUser($this)->remove($wishable);
    }

    public function wish(Wishable $wishable): Wish
    {
        return wishlist()->forUser($this)->add($wishable);
    }

    public function wishes(): WishCollection
    {
        return wishlist()->forUser($this)->all();
    }
}
