<?php

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Contracts\Wishlist;
use Illuminate\Support\Manager;

class WishlistManager extends Manager implements Wishlist
{
    public function getDefaultDriver()
    {
        return $this->config->get('wishlist.driver');
    }

    private function createArrayDriver(): InMemoryWishlist
    {
        return InMemoryWishlist::make();
    }

    private function createCookieDriver(): CookieWishlist
    {
        return CookieWishlist::make(
            $this->container->make('cookie'),
            $this->container->make('request'),
            $this->config->get('wishlist.cookie'),
        );
    }

    private function createDatabaseDriver(): EloquentWishlist
    {
        return $this->createEloquentDriver();
    }

    private function createEloquentDriver(): EloquentWishlist
    {
        return EloquentWishlist::make(
            call_user_func($this->container->make('auth')->userResolver())->getAuthIdentifier(),
            $this->config->get('wishlist.eloquent.scope'),
        );
    }

    private function createUpgradeDriver(): CookieWishlist|EloquentWishlist
    {
        if ($this->container->make('auth')->check()) {
            return $this->createEloquentDriver();
        } else {
            return $this->createCookieDriver();
        }
    }

    # region CONTRACT
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

    public function remove(Wishable|int|string $id): void
    {
        $this->driver()->remove($id);
    }
    # endregion
}
