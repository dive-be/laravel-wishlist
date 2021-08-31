<?php

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\Support\Makeable;
use Illuminate\Support\Str;

class InMemoryWishlist implements Wishlist
{
    use Makeable;

    private WishCollection $wishes;

    public function __construct(array|WishCollection $wishes = [])
    {
        $this->wishes = WishCollection::make($wishes);
    }

    public function add(Wishable $wishable): Wish
    {
        if ($wish = $this->wishes->find($wishable)) {
            return $wish;
        }

        return tap(Wish::make(Str::random(), $wishable), function ($wish) {
            $this->wishes->push($wish);
        });
    }

    public function all(): WishCollection
    {
        return $this->wishes;
    }

    public function count(): int
    {
        return $this->wishes->count();
    }

    public function has(Wishable $wishable): bool
    {
        return $this->wishes->exists($wishable);
    }

    public function isEmpty(): bool
    {
        return $this->wishes->isEmpty();
    }

    public function isNotEmpty(): bool
    {
        return $this->wishes->isNotEmpty();
    }

    public function remove(Wishable|int|string $id): bool
    {
        $previous = $this->count();

        $this->wishes = $this->wishes->without($id);

        return $previous !== $this->count();
    }
}
