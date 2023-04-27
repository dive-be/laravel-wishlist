<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\Support\Makeable;
use Illuminate\Support\Str;

final class InMemoryWishlist implements Wishlist
{
    use Makeable;

    private WishCollection $wishes;

    public function __construct(array|WishCollection $wishes = [])
    {
        $this->wishes = new WishCollection($wishes);
    }

    public function add(Wishable $wishable): Wish
    {
        if ($wish = $this->wishes->find($wishable)) {
            return $wish;
        }

        return tap(Wish::make((string) Str::uuid(), $wishable), function ($wish) {
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

    public function find(string|Wishable $id): ?Wish
    {
        return $this->wishes->find($id);
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

    public function purge(): int
    {
        return tap($this->count(), function () {
            $this->wishes = WishCollection::make();
        });
    }

    public function remove(string|Wish|Wishable $id): bool
    {
        if ($id instanceof Wish) {
            $id = $id->id;
        }

        $previous = $this->count();

        $this->wishes = $this->wishes->without($id);

        return $previous !== $this->count();
    }
}
