<?php declare(strict_types=1);

namespace Dive\Wishlist\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wish extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $table = 'wishes';

    public function user(): BelongsTo
    {
        return $this->belongsTo(wishlist('eloquent.user'));
    }

    public function wishable(): MorphTo
    {
        return $this->morphTo();
    }
}
