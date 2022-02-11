<?php declare(strict_types=1);

namespace Dive\Wishlist\Events;

use Dive\Wishlist\Support\Makeable;

class WishlistTouched
{
    use Makeable;

    public function __construct(
        public readonly int $count,
    ) {}
}
