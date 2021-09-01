<?php

namespace Tests;

use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;
use Illuminate\Support\Collection;
use LogicException;
use Tests\Fakes\Product;
use Tests\Fakes\Sample;

beforeEach(function () {
    $this->collection = WishCollection::make([
        Wish::make(1, product()),
        Wish::make(2, $this->wishable = sample()),
        Wish::make(333, product()),
    ]);
});

it('can find a wish using a wishable', function () {
    expect($this->collection->find($this->wishable))
        ->toBeInstanceOf(Wish::class)
        ->wishable()->toBeInstanceOf(Sample::class)
        ->id()->toBe(2);
});

it('can determine existence using a wishable', function () {
    expect($this->collection->exists($this->wishable))->toBeTrue();
    expect($this->collection->exists(product()))->toBeFalse();
});

it('can exclude a single wish', function () {
    expect($this->collection->without($this->wishable))
        ->toHaveCount(2)
        ->exists($this->wishable)->toBeFalse();
});

it('can retrieve the with ids', function () {
    expect($this->collection->ids())
        ->toBeInstanceOf(Collection::class)
        ->not->toBeInstanceOf(WishCollection::class)
        ->toMatchArray($this->collection->map->id());
});

it('can retrieve the wishables', function () {
    expect($wishables = $this->collection->wishables())
        ->toBeInstanceOf(Collection::class)
        ->not->toBeInstanceOf(WishCollection::class);

    $wishables->each(function ($wishable, $idx) {
        expect($wishable->is($this->collection->get($idx)->wishable()))->toBeTrue();
    });
});

it('can group wishes by wishable morph type', function () {
    expect($this->collection->groupByType())
        ->toBeInstanceOf(Collection::class)
        ->not->toBeInstanceOf(WishCollection::class)
        ->toHaveCount(2)
        ->each->toBeInstanceOf(WishCollection::class);
});

it('can retrieve wishes of a single morph type', function (string $morphType, int $count, string $type) {
    expect($this->collection->ofType($morphType))
        ->toBeInstanceOf(WishCollection::class)
        ->toHaveCount($count)
        ->each(fn ($expect) => $expect->wishable()->toBeInstanceOf($type));
})->with([
    [Product::class, 2, Product::class],
    ['product', 2, Product::class],
    [Sample::class, 1, Sample::class],
    ['sample', 1, Sample::class],
]);

it('can eager load the relations of wishables', function () {
    $products = $this->collection->ofType(Product::class);
    $samples = $this->collection->ofType(Sample::class);

    expect($products)->each(fn ($expect) => $expect->wishable()->relationLoaded('variant')->toBeFalse());
    expect($samples)->each(fn ($expect) => $expect->wishable()->relationLoaded('purveyor')->toBeFalse());

    $this->collection->load([
        Product::class => 'variant',
        Sample::class => 'purveyor',
    ]);

    expect($products)->each(fn ($expect) => $expect->wishable()->relationLoaded('variant')->toBeTrue());
    expect($samples)->each(fn ($expect) => $expect->wishable()->relationLoaded('purveyor')->toBeTrue());
});

it('can eager load without a type-relation map when unambiguous', function () {
    $products = $this->collection->ofType(Product::class);

    expect($products)->each(fn ($expect) => $expect->wishable()->relationLoaded('variant')->toBeFalse());

    $products->load('variant');

    expect($products)->each(fn ($expect) => $expect->wishable()->relationLoaded('variant')->toBeTrue());
});

it('throws if the eager load is ambiguous', function () {
    $this->collection->load('variant');
})->throws(LogicException::class);
