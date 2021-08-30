<?php

namespace Dive\Wishlist\Models\Concerns;

use Dive\Wishlist\Models\Wish;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait CanBeWished
{
    public function getMorphKey(): string
    {
        return "{$this->getMorphClass()}-{$this->getKey()}";
    }

    public function wish(): MorphOne
    {
        return $this->morphOne(Wish::class, 'wishable');
    }
}
