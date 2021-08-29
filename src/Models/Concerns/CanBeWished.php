<?php

namespace Dive\Wishlist\Models\Concerns;

use Dive\Wishlist\Models\Wish;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CanBeWished
{
    public function getMorphKey(): string
    {
        return "{$this->getMorphClass()}-{$this->getKey()}";
    }

    public function wish(): BelongsTo
    {
        return $this->belongsTo(Wish::class);
    }
}
