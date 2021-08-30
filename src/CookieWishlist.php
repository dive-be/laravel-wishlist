<?php

namespace Dive\Wishlist;

use Closure;
use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\Support\Makeable;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Http\Request;

class CookieWishlist implements Wishlist
{
    use Makeable;

    private string $domain;

    private int $maxAge;

    private string $name;

    private InMemoryWishlist $wishlist;

    public function __construct(private QueueingFactory $jar, Request $request, array $config)
    {
        ['domain' => $this->domain, 'max_age' => $this->maxAge, 'name' => $this->name] = $config;

        $this->wishlist = InMemoryWishlist::make(
            transform($request->cookie($this->name), fn ($cookie) => unserialize($cookie), [])
        );
    }

    public function add(Wishable $wishable): Wish
    {
        return $this->enqueue(fn () => $this->wishlist->add($wishable));
    }

    public function all(): WishCollection
    {
        return $this->wishlist->all()->load();
    }

    public function count(): int
    {
        return $this->wishlist->count();
    }

    public function has(Wishable $wishable): bool
    {
        return $this->wishlist->has($wishable);
    }

    public function isEmpty(): bool
    {
        return $this->wishlist->isEmpty();
    }

    public function isNotEmpty(): bool
    {
        return $this->wishlist->isNotEmpty();
    }

    public function remove(Wishable|int|string $id): void
    {
        $this->enqueue(fn () => $this->wishlist->remove($id));
    }

    private function enqueue(Closure $callback): mixed
    {
        return tap($callback(), function () {
            $this->jar->queue($this->jar->make(
                name: $this->name,
                value: serialize($this->wishlist->all()),
                minutes: $this->maxAge,
                domain: $this->domain
            ));
        });
    }
}
