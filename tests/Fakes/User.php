<?php

namespace Tests\Fakes;

use Dive\Wishlist\Models\Concerns\InteractsWithWishlist;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use InteractsWithWishlist;
}
