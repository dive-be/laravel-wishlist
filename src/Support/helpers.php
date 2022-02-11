<?php declare(strict_types=1);

use Dive\Wishlist\WishlistManager;

if (! function_exists('wishlist')) {
    function wishlist(?string $config = null): array|string|WishlistManager
    {
        if (is_string($config)) {
            return app('wishlist')->config($config);
        }

        return app('wishlist');
    }
}
