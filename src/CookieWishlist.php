<?php

namespace Dive\Wishlist;

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
        $previous = $this->count();
        $wish = $this->wishlist->add($wishable);

        if ($previous !== $this->count()) {
            $this->enqueue();
        }

        return $wish;
    }

    public function all(): WishCollection
    {
        return $this->wishlist->all()->hydrate();
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

    public function remove(Wishable|int|string $id): bool
    {
        $removed = $this->wishlist->remove($id);

        if ($removed) {
            $this->enqueue();
        }

        return $removed;
    }

    private function enqueue()
    {
        $this->jar->queue($this->jar->make(
            name: $this->name,
            value: serialize($this->wishlist->all()),
            minutes: $this->maxAge,
            domain: $this->domain
        ));
    }
}
