<?php

namespace Dive\Wishlist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wish extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function getWishableKey(): string
    {
        return "{$this->wishable_type}-{$this->wishable_id()}";
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('wishlist.eloquent.user'));
    }

    public function wishable(): MorphTo
    {
        return $this->morphTo();
    }
}
