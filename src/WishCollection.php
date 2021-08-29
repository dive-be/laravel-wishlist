<?php

namespace Dive\Wishlist;

use Closure;
use Dive\Wishlist\Contracts\Wishable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class WishCollection extends Collection
{
    private bool $hydrated;

    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->hydrated = $this->isEmpty() || $this->first() instanceof Wish;
    }

    public function find(Wishable $wishable): ?Wish
    {
        return $this->first($this->comparator($wishable));
    }

    public function exists(Wishable $wishable): bool
    {
        return $this->some($this->comparator($wishable));
    }

    public function hydrate(): self
    {
        if ($this->hydrated) {
            return $this;
        }

        $models = $this->groupBy('wishable.type')->map(function (self $wishes) {
            return $wishes->map(fn (array $wish) => Arr::get($wish, 'wishable.id'))->all();
        })->map(fn (array $ids, string $morphType) => call_user_func([
            Relation::getMorphedModel($morphType) ?? $morphType,
            'findMany',
        ], $ids)->keyBy('id'));

        $this->items = $this->filter($retriever = function (array $wish) use ($models) {
            return $models->get(Arr::get($wish, 'wishable.type'))->get(Arr::get($wish, 'wishable.id'));
        })->map(function (array $wish) use ($retriever) {
            return Wish::make($wish['id'], $retriever($wish));
        })->all();

        $this->hydrated = true;

        return $this;
    }

    public function without(Wishable|int|string $id): self
    {
        return $this->reject($this->comparator($id));
    }

    private function comparator(Wishable|int|string $id): Closure
    {
        if (! $id instanceof Wishable) {
            return fn ($wish) => (is_array($wish) ? $wish['id'] : $wish->id()) === $id;
        }

        return function ($wish) use ($id) {
            $type = is_array($wish) ? $wish['wishable']['type'] : $wish->wishable()->getMorphClass();
            $key = is_array($wish) ? $wish['wishable']['id'] : $wish->wishable()->getKey();

            return $id->getKey() === $key && $id->getMorphClass() === $type;
        };
    }

    public function __serialize(): array
    {
        return $this->map(fn ($wish) => json_encode($wish))->toArray();
    }

    public function __unserialize(array $data): void
    {
        $this->items = array_map(fn ($wish) => json_decode($wish, true), $data);
    }
}
