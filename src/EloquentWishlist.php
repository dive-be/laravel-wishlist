<?php

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\Models\Wish as Model;
use Dive\Wishlist\Support\Makeable;
use Dive\Wishlist\Support\RemembersResults;
use Illuminate\Database\Eloquent\Builder;

class EloquentWishlist implements Wishlist
{
    use Makeable;
    use RemembersResults;

    private array $constraints;

    public function __construct(int $user, string $scope)
    {
        $this->constraints = ['user_id' => $user, 'scope' => $scope];
    }

    public function add(Wishable $wishable): Wish
    {
        $wish = $this->newQuery()->where($columns = $this->morphColumns($wishable))->first();

        if (! $wish instanceof Model) {
            $wish = Model::create(array_merge($this->constraints, $columns));

            $this->markAsDirty();
        }

        return Wish::fromModel($wish);
    }

    public function all(): WishCollection
    {
        return $this->remember(fn () => $this
            ->newQuery()
            ->with('wishable')
            ->get()
            ->map(fn ($model) => Wish::fromModel($model))
            ->pipe(fn ($collection) => WishCollection::make($collection)));
    }

    public function count(): int
    {
        return $this->remember(fn () => $this->newQuery()->count());
    }

    public function has(Wishable $wishable): bool
    {
        return array_key_exists(
            $wishable->getMorphKey(),
            $this->remember(fn () => $this
                ->newQuery()
                ->toBase()
                ->get(['wishable_id', 'wishable_type'])
                ->mapWithKeys(fn ($wish) => ["{$wish->wishable_type}-{$wish->wishable_id}" => true])
                ->toArray())
        );
    }

    public function isEmpty(): bool
    {
        return ! $this->isNotEmpty();
    }

    public function isNotEmpty(): bool
    {
        return (bool) $this->count();
    }

    public function remove(int|string|Wishable $id): bool
    {
        $removed = (bool) $this->newQuery()
            ->where($id instanceof Wishable ? $this->morphColumns($id) : compact('id'))
            ->delete();

        if ($removed) {
            $this->markAsDirty();
        }

        return $removed;
    }

    private function morphColumns(Wishable $wishable): array
    {
        return [
            'wishable_type' => $wishable->getMorphClass(),
            'wishable_id' => $wishable->getKey(),
        ];
    }

    private function newQuery(): Builder
    {
        return Model::query()->where($this->constraints);
    }
}
