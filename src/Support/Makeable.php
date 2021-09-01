<?php

namespace Dive\Wishlist\Support;

trait Makeable
{
    public static function make(...$arguments)
    {
        return new self(...$arguments);
    }
}
