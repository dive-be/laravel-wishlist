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

        return tap($this->wishlist->add($wishable), function () use ($previous) {
            if ($previous !== $this->count()) {
                $this->enqueue();
            }
        });
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

    public function purge(): int
    {
        return tap($this->wishlist->purge(), function () {
            $this->forget();
        });
    }

    public function remove(Wishable|int|string $id): bool
    {
        return tap($this->wishlist->remove($id), function (bool $removed) {
            if ($removed) {
                $this->enqueue();
            }
        });
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

    private function forget()
    {
        $this->jar->queue(
            $this->jar->forget(name: $this->name, domain: $this->domain)
        );
    }
}
