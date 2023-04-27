<?php declare(strict_types=1);

namespace Dive\Wishlist\Actions;

use Dive\Wishlist\WishlistManager;

final readonly class MigrateWishesAction
{
    public function __construct(private WishlistManager $wishlist) {}

    public function execute(): void
    {
        $cookie = $this->wishlist->driver(WishlistManager::COOKIE);

        if ($cookie->isNotEmpty()) {
            $this->wishlist->driver(WishlistManager::ELOQUENT)->merge($cookie->all());

            $cookie->purge();
        }
    }
}
