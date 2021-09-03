<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Contracts\Wishlist;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Manager;

class WishlistManager extends Manager implements Wishlist
{
    public const ARRAY = 'array';
    public const COOKIE = 'cookie';
    public const ELOQUENT = 'eloquent';
    public const UPGRADE = 'upgrade';

    public function forUser(Authenticatable $user): EloquentWishlist
    {
        return $this->createEloquentDriver($user);
    }

    public function getDefaultDriver()
    {
        return $this->config->get('wishlist.driver');
    }

    protected function createArrayDriver(): InMemoryWishlist
    {
        return InMemoryWishlist::make();
    }

    protected function createCookieDriver(): CookieWishlist
    {
        return CookieWishlist::make(
            $this->container->make('cookie'),
            $this->container->make('request'),
            $this->config->get('wishlist.cookie'),
        );
    }

    protected function createEloquentDriver(?Authenticatable $user = null): EloquentWishlist
    {
        return EloquentWishlist::make(
            ($user ?? call_user_func($this->auth()->userResolver()))->getAuthIdentifier(),
            $this->config->get('wishlist.eloquent.scope'),
        );
    }

    protected function createUpgradeDriver(): CookieWishlist|EloquentWishlist
    {
        if ($this->auth()->check()) {
            return $this->createEloquentDriver();
        }

        return $this->createCookieDriver();
    }

    private function auth(): AuthManager
    {
        return $this->container->make(__FUNCTION__);
    }

    // region CONTRACT
    public function add(Wishable $wishable): Wish
    {
        return $this->driver()->add($wishable);
    }

    public function all(): WishCollection
    {
        return $this->driver()->all();
    }

    public function count(): int
    {
        return $this->driver()->count();
    }

    public function has(Wishable $wishable): bool
    {
        return $this->driver()->has($wishable);
    }

    public function isEmpty(): bool
    {
        return $this->driver()->isEmpty();
    }

    public function isNotEmpty(): bool
    {
        return $this->driver()->isNotEmpty();
    }

    public function purge(): int
    {
        return $this->driver()->purge();
    }

    public function remove(Wishable|int|string $id): bool
    {
        return $this->driver()->remove($id);
    }

    // endregion
}
