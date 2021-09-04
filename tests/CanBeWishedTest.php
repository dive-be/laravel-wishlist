<?php

namespace Tests;

use Illuminate\Database\Eloquent\Relations\MorphOne;

test('model can form a "morph key"', function () {
    expect(product()->getMorphKey())->toBe('product-1');
});

test('model has a polymorphic wish relation', function () {
    expect(product()->wish())
        ->toBeInstanceOf(MorphOne::class)
        ->getQualifiedMorphType()->toBe('wishes.wishable_type');
});
