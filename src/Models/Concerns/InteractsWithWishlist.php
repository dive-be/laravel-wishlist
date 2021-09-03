<?php

namespace Dive\Wishlist\Models\Concerns;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;

trait InteractsWithWishlist
{
    public function unwish(Wishable $wishable): bool
    {
        return app('wishlist')->forUser($this)->remove($wishable);
    }

    public function wish(Wishable $wishable): Wish
    {
        return app('wishlist')->forUser($this)->add($wishable);
    }

    public function wishes(): WishCollection
    {
        return app('wishlist')->forUser($this)->all();
    }
}
