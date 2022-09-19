<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use LogicException;

class WishCollection extends Collection
{
    private bool $hydrated;

    public function __construct($items = [])
    {
        parent::__construct($items);

        $this->hydrated = $this->isEmpty() || $this->first() instanceof Wish;
    }

    public function find(string|Wishable $id): ?Wish
    {
        $index = $this->search(Comparator::for($id));

        if ($index === false) {
            return null;
        }

        $wish = $this->get($index);

        if (is_array($wish)) {
            $wish = Wish::make(
                $wish['id'],
                $id instanceof Wishable ? $id : $this->findModel($wish['wishable']['type'], $wish['wishable']['id']),
            );

            $this->items = $this->replace([$index => $wish])->all();
        }

        return $wish;
    }

    public function exists(Wishable $wishable): bool
    {
        return $this->some(Comparator::object($wishable));
    }

    public function groupByType(): Collection
    {
        return $this->groupBy(fn (Wish $wish) => $wish->wishable->getMorphClass())->toBase();
    }

    public function ids(): Collection
    {
        return $this->map(fn (Wish $wish) => $wish->id)->toBase();
    }

    public function load(array|string $relations): self
    {
        if ($this->isNotEmpty()) {
            if (is_string($relations)) {
                $relations = [$relations];
            }

            $groupedByType = $this->groupByType();

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
                    (new EloquentCollection(
                        $wishes->map(fn (Wish $wish) => $wish->wishable)->all()
                    ))->load($relations[$morphType]);
                }
            });
        }

        return $this;
    }

    /**
     * @internal
     */
    public function hydrate(): self
    {
        if ($this->hydrated) {
            return $this;
        }

        $wishes = $this->reject(fn ($wish) => $wish instanceof Wish);

        if ($wishes->isNotEmpty()) {
            $models = $wishes->groupBy('wishable.type')->map(function ($wishes) {
                return $wishes->map(fn (array $wish) => Arr::get($wish, 'wishable.id'))->all();
            })->map(function (array $ids, string $morphType) {
                return $this->findModels($morphType, $ids)->keyBy('id');
            });

            $this->transform(function (array|Wish $wish) use ($models) {
                if ($wish instanceof Wish) {
                    return $wish;
                }

                return Wish::make(
                    $wish['id'],
                    $models->get(Arr::get($wish, 'wishable.type'))->get(Arr::get($wish, 'wishable.id')),
                );
            });
        }

        $this->hydrated = true;

        return $this;
    }

    public function ofType(string $type): self
    {
        if ($morph = $this->morphType($type)) {
            $type = $morph;
        }

        return $this->filter(fn (Wish $wish) => $wish->wishable->getMorphClass() === $type);
    }

    public function wishables(): Collection
    {
        return $this->map(fn (Wish $wish) => $wish->wishable)->toBase();
    }

    public function without(string|Wishable $id): self
    {
        return $this->reject(Comparator::for($id));
    }

    private function findModel(string $type, int|string $id): Wishable
    {
        return call_user_func([$this->morphModel($type), 'find'], $id);
    }

    private function findModels(string $type, array $ids): EloquentCollection
    {
        return call_user_func([$this->morphModel($type), 'findMany'], $ids);
    }

    private function morphModel(string $type): string
    {
        return Relation::getMorphedModel($type) ?? $type;
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
