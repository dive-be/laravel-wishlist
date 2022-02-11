<?php declare(strict_types=1);

namespace Dive\Wishlist\Models\Concerns;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Facades\Wishlist;
use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;

trait InteractsWithWishlist
{
    public function unwish(Wishable $wishable): bool
    {
        return Wishlist::forUser($this)->remove($wishable);
    }

    public function wish(Wishable $wishable): Wish
    {
        return Wishlist::forUser($this)->add($wishable);
    }

    public function wishes(): WishCollection
    {
        return Wishlist::forUser($this)->all();
    }
}
