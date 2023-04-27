<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Wish;
use Exception;
use Tests\Fakes\Product;

beforeEach(function () {
    $this->wish = Wish::make('1337', new Product(['id' => 9876]));
});

it('is arrayable', function () {
    expect($this->wish->toArray())->toBe([
        'id' => '1337',
        'wishable' => [
            'id' => 9876,
            'type' => (new Product())->getMorphClass(),
        ],
    ]);
});

it('is jsonSerializable', function () {
    expect($this->wish->toArray())->toBe($this->wish->jsonSerialize());
});

it('is jsonable', function () {
    expect(json_encode($this->wish))->toBe($this->wish->toJson());
});

it('can retrieve the wish id', function () {
    expect($this->wish->id)->toBe('1337');
});

it('can retrieve the wishable', function () {
    expect($this->wish->wishable)->toBeInstanceOf(Product::class);
    expect($this->wish->wishable->getKey())->toBe(9876);
});

it('can be route model bound', function () {
    $this->mock('wishlist')
        ->shouldReceive('find')
        ->once()
        ->withArgs(fn ($id) => $id === 'Dive Hard')
        ->andReturn($this->wish);

    expect($this->wish->resolveRouteBinding('Dive Hard'))->toBe($this->wish);
    expect($this->wish->getRouteKey())->toBe('1337');
    expect($this->wish->getRouteKeyName())->toBe('wish');
    expect(fn () => $this->wish->resolveChildRouteBinding('Dive', 'Very', 'Hard'))->toThrow(Exception::class);
});

it('can be deleted', function () {
    $this->mock('wishlist')
        ->shouldReceive('remove')
        ->once()
        ->withArgs(fn ($wish) => $wish === $this->wish)
        ->andReturn(true);

    $this->wish->delete();
});
