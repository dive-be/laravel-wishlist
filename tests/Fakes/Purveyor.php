<?php declare(strict_types=1);

namespace Tests\Fakes;

use Illuminate\Database\Eloquent\Model;

final class Purveyor extends Model
{
    public $timestamps = false;

    protected $guarded = [];
}
