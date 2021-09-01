<?php

namespace Dive\Wishlist\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Dive\Wishlist\Wish add(\Dive\Wishlist\Contracts\Wishable $wishable)
 * @method static \Dive\Wishlist\WishCollection all()
 * @method static int count()
 * @method static bool has(\Dive\Wishlist\Contracts\Wishable $wishable)
 * @method static bool isEmpty()
 * @method static bool isNotEmpty()
 * @method static int purge()
 * @method static bool remove(int|string|\Dive\Wishlist\Contracts\Wishable $id)
 *
 * @see \Dive\Wishlist\WishlistManager
 */
class Wishlist extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'wishlist';
    }
}
