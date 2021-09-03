<?php

namespace Tests;

use Dive\Wishlist\Wish;
use Tests\Fakes\Product;

beforeEach(function () {
    $this->wish = Wish::make(1337, new Product(['id' => 9876]));
});

it('is arrayable', function () {
    expect($this->wish->toArray())->toBe([
        'id' => 1337,
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
    expect($this->wish->id)->toBe(1337);
});

it('can retrieve the wishable', function () {
    expect($this->wish->wishable)->toBeInstanceOf(Product::class);
    expect($this->wish->wishable->getKey())->toBe(9876);
});
