<?php declare(strict_types=1);

use Dive\Wishlist\WishlistManager;

if (! function_exists('wishlist')) {
    function wishlist(): WishlistManager
    {
        return app(__FUNCTION__);
    }
}
