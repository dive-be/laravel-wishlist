<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\Events\WishlistTouched;
use Dive\Wishlist\Support\Makeable;
use Illuminate\Contracts\Events\Dispatcher;

final readonly class WishlistEventDecorator implements Wishlist
{
    use Makeable;

    public function __construct(private Wishlist $next, private Dispatcher $dispatcher) {}

    public function add(Wishable $wishable): Wish
    {
        return tap($this->next->add($wishable), function () {
            $this->dispatchTouchedEvent();
        });
    }

    public function all(): WishCollection
    {
        return $this->next->all();
    }

    public function count(): int
    {
        return $this->next->count();
    }

    public function find(Wishable|string $id): ?Wish
    {
        return $this->next->find($id);
    }

    public function has(Wishable $wishable): bool
    {
        return $this->next->has($wishable);
    }

    public function isEmpty(): bool
    {
        return $this->next->isEmpty();
    }

    public function isNotEmpty(): bool
    {
        return $this->next->isNotEmpty();
    }

    public function purge(): int
    {
        return tap($this->next->purge(), function () {
            $this->dispatchTouchedEvent();
        });
    }

    public function remove(Wishable|string|Wish $id): bool
    {
        return tap($this->next->remove($id), function () {
            $this->dispatchTouchedEvent();
        });
    }

    private function dispatchTouchedEvent(): void
    {
        $this->dispatcher->dispatch(
            WishlistTouched::make($this->count())
        );
    }

    public function __call(string $name, array $arguments): mixed
    {
        return $this->next->{$name}(...$arguments);
    }
}
