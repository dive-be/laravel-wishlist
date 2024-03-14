<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\InMemoryWishlist;

final class InMemoryWishlistTest extends TestCase
{
    use WishlistContractTests;

    protected function getInstance(): Wishlist
    {
        return new InMemoryWishlist();
    }
}
