<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\Models\Wish as Model;
use Dive\Wishlist\Support\Makeable;
use Dive\Wishlist\Support\PoorMansCaching;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

final class EloquentWishlist implements Wishlist
{
    use Makeable;
    use PoorMansCaching;

    private array $constraints;

    public function __construct(private readonly Model $model, int $user, string $scope)
    {
        $this->constraints = ['user_id' => $user, 'scope' => $scope];
    }

    public function add(Wishable $wishable): Wish
    {
        $model = $this->newQuery()->where($columns = $this->morphColumns($wishable))->first();

        if (! $model instanceof Model) {
            $model = $this->model->create(['uuid' => (string) Str::uuid()] + $this->constraints + $columns);

            $this->markAsDirty();
        }

        return Wish::of($model);
    }

    public function all(): WishCollection
    {
        return $this->remember(fn () => $this
            ->newQuery()
            ->with('wishable')
            ->get()
            ->map(Wish::of(...))
            ->pipe(WishCollection::make(...)));
    }

    public function count(): int
    {
        return $this->remember(fn () => $this->newQuery()->count());
    }

    public function find(string|Wishable $id): ?Wish
    {
        return transform($this->newQuery()
            ->where($id instanceof Wishable ? $this->morphColumns($id) : ['uuid' => $id])
            ->first(), Wish::of(...));
    }

    public function has(Wishable $wishable): bool
    {
        return array_key_exists(
            $wishable->getMorphKey(),
            $this->remember(fn () => $this
                ->newQuery()
                ->toBase()
                ->get(['wishable_id', 'wishable_type'])
                ->mapWithKeys(static fn ($wish) => ["{$wish->wishable_type}-{$wish->wishable_id}" => true])
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

    public function merge(WishCollection $wishes): void
    {
        $wishMap = $this->all()->keyBy(fn (Wish $wish) => $wish->wishable->getMorphKey());

        $this->newQuery()->insert(
            $wishes->reject(function (Wish $wish) use ($wishMap) {
                return $wishMap->has($wish->wishable->getMorphKey());
            })->map(fn (Wish $wish) => $this->morphColumns($wish->wishable) + $this->constraints + [
                'created_at' => now(),
                'updated_at' => now(),
                'uuid' => $wish->id,
            ])->all()
        );

        $this->markAsDirty();
    }

    public function purge(): int
    {
        return tap($this->newQuery()->delete(), function () {
            $this->markAsDirty();
        });
    }

    public function remove(string|Wish|Wishable $id): bool
    {
        if ($id instanceof Wish) {
            $id = $id->id;
        }

        $removed = (bool) $this->newQuery()
            ->where($id instanceof Wishable ? $this->morphColumns($id) : ['uuid' => $id])
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

    /**
     * @return Builder<Model>
     */
    private function newQuery(): Builder
    {
        return $this->model->newQuery()->where($this->constraints);
    }
}
