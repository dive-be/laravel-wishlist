<?php declare(strict_types=1);

namespace Dive\Wishlist\Facades;

use Dive\Wishlist\InMemoryWishlist;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Dive\Wishlist\Wish             add(\Dive\Wishlist\Contracts\Wishable $wishable)
 * @method static \Dive\Wishlist\WishCollection   all()
 * @method static int                             count()
 * @method static \Dive\Wishlist\WishlistManager  extend(string $driver, \Closure $callback)
 * @method static \Dive\Wishlist\EloquentWishlist forUser(\Illuminate\Contracts\Auth\Authenticatable $user)
 * @method static \Dive\Wishlist\Wish|null        find(string|\Dive\Wishlist\Contracts\Wishable $id)
 * @method static bool                            has(\Dive\Wishlist\Contracts\Wishable $wishable)
 * @method static bool                            isEmpty()
 * @method static bool                            isNotEmpty()
 * @method static int                             purge()
 * @method static bool                            remove(string|\Dive\Wishlist\Wish|\Dive\Wishlist\Contracts\Wishable $id)
 */
final class Wishlist extends Facade
{
    public static function fake(): InMemoryWishlist
    {
        self::swap($fake = InMemoryWishlist::make());

        return $fake;
    }

    protected static function getFacadeAccessor(): string
    {
        return 'wishlist';
    }
}
