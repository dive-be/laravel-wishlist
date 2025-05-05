<?php declare(strict_types=1);

namespace Dive\Wishlist\Support;

trait Makeable
{
    public static function make(...$arguments): static
    {
        return new static(...$arguments);
    }
}
