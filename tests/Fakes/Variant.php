<?php declare(strict_types=1);

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;

final class Variant extends Model
{
    public $timestamps = false;

    protected $guarded = [];
}
