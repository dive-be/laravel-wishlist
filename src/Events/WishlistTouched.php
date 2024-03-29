<?php declare(strict_types=1);

namespace Dive\Wishlist\Events;

use Dive\Wishlist\Support\Makeable;

final readonly class WishlistTouched
{
    use Makeable;

    public function __construct(public int $count) {}
}
