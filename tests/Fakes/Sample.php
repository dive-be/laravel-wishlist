<?php

namespace Tests\Fakes;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Models\Concerns\CanBeWished;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sample extends Model implements Wishable
{
    use CanBeWished;

    public $timestamps = false;

    protected $guarded = [];

    public function purveyor(): BelongsTo
    {
        return $this->belongsTo(Purveyor::class);
    }
}
