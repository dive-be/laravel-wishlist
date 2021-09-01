<?php

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use LogicException;

class WishCollection extends Collection
{
    private bool $loaded;

    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->loaded = $this->isEmpty() || $this->first() instanceof Wish;
    }

    public function find(Wishable $wishable): ?Wish
    {
        return $this->first(Comparator::object($wishable));
    }

    public function exists(Wishable $wishable): bool
    {
        return $this->some(Comparator::object($wishable));
    }

    /**
     * @return Collection<string, WishCollection>
     */
    public function groupByType(): Collection
    {
        return $this->groupBy(fn (Wish $wish) => $wish->wishable()->getMorphClass())->toBase();
    }

    public function ids(): Collection
    {
        return $this->map(fn ($wish) => $wish->id())->toBase();
    }

    public function load(array|string $relations): self
    {
        if ($this->isNotEmpty()) {
            if (is_string($relations)) {
                $relations = [$relations];
            }

            $groupedByType = $this->groupBy(fn (Wish $wish) => $wish->wishable()->getMorphClass());

            if (! Arr::isAssoc($relations)) {
                if ($groupedByType->count() > 1) {
                    throw new LogicException('You must provide the polymorphic types explicitly.');
                }

                $relations = [$groupedByType->keys()->first() => $relations];
            }

            foreach ($relations as $key => $value) {
                $key = (string) $key;

                if ($morph = $this->morphType($key)) {
                    $relations[$morph] = $value;

                    unset($relations[$key]);
                }
            }

            $groupedByType->each(function (self $wishes, string $morphType) use ($relations) {
                if (array_key_exists($morphType, $relations)) {
                    EloquentCollection::make(
                        $wishes->map(fn (Wish $wish) => $wish->wishable())->all()
                    )->load($relations[$morphType]);
                }
            });
        }

        return $this;
    }

    /**
     * @internal
     */
    public function loadIfNotLoaded(): self
    {
        if ($this->loaded) {
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

        $this->loaded = true;

        return $this;
    }

    public function ofType(string $type): self
    {
        if ($morph = $this->morphType($type)) {
            $type = $morph;
        }

        return $this->filter(fn (Wish $wish) => $wish->wishable()->getMorphClass() === $type);
    }

    public function wishables(): Collection
    {
        return $this->map(fn (Wish $wish) => $wish->wishable())->toBase();
    }

    public function without(Wishable|int|string $id): self
    {
        return $this->reject(Comparator::for($id));
    }

    private function morphType(string $value): ?string
    {
        if (! class_exists($value)) {
            return null;
        }

        $morphMap = Relation::morphMap();

        if (empty($morphMap) || ! in_array($value, $morphMap)) {
            return null;
        }

        return array_search($value, $morphMap, true);
    }

    public function __serialize(): array
    {
        return $this->map(fn (array|Wish $wish) => json_encode($wish))->toArray();
    }

    public function __unserialize(array $data): void
    {
        $this->items = array_map(fn (string $wish) => json_decode($wish, true), $data);
    }
}
