<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Contracts\Wishlist;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\Manager;

final class WishlistManager extends Manager implements Wishlist
{
    public const string ARRAY = 'array';
    public const string COOKIE = 'cookie';
    public const string ELOQUENT = 'eloquent';
    public const string UPGRADE = 'upgrade';

    public function auth(): Guard
    {
        return $this->container->make(__FUNCTION__)->guard($this->config('auth_guard'));
    }

    public function config(string $key): array|string
    {
        return $this->config->get("wishlist.{$key}");
    }

    public function forUser(Authenticatable $user): EloquentWishlist
    {
        return $this->createEloquentDriver($user);
    }

    public function getDefaultDriver(): string
    {
        return $this->config('driver');
    }

    protected function createArrayDriver(): InMemoryWishlist
    {
        return InMemoryWishlist::make();
    }

    protected function createCookieDriver(): CookieWishlist
    {
        return CookieWishlist::make(
            $this->container['cookie'],
            $this->container['request'],
            $this->config('cookie'),
        );
    }

    protected function createDriver($driver): WishlistEventDecorator
    {
        return $this->events(parent::createDriver($driver));
    }

    protected function createEloquentDriver(?Authenticatable $user = null): EloquentWishlist
    {
        return EloquentWishlist::make(
            new ($this->config('eloquent.model'))(),
            ($user ?? $this->auth()->user())->getAuthIdentifier(),
            $this->config('eloquent.scope'),
        );
    }

    protected function createUpgradeDriver(): CookieWishlist|EloquentWishlist
    {
        if ($this->auth()->check()) {
            return $this->createEloquentDriver();
        }

        return $this->createCookieDriver();
    }

    protected function events(Wishlist $wishlist): WishlistEventDecorator
    {
        return WishlistEventDecorator::make($wishlist, $this->container['events']);
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

    public function find(string|Wishable $id): ?Wish
    {
        return $this->driver()->find($id);
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

    public function remove(string|Wish|Wishable $id): bool
    {
        return $this->driver()->remove($id);
    }

    // endregion
}
