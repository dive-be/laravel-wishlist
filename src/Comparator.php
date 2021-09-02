<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Closure;
use Dive\Wishlist\Contracts\Wishable;

final class Comparator
{
    public static function for(Wishable|int|string $comparable): Closure
    {
        if ($comparable instanceof Wishable) {
            return self::object($comparable);
        }

        return self::primitive($comparable);
    }

    public static function object(Wishable $wishable): Closure
    {
        return function ($wish) use ($wishable) {
            $type = is_array($wish) ? $wish['wishable']['type'] : $wish->wishable()->getMorphClass();
            $key = is_array($wish) ? $wish['wishable']['id'] : $wish->wishable()->getKey();

            return $wishable->getKey() === $key && $wishable->getMorphClass() === $type;
        };
    }

    public static function primitive(int|string $id): Closure
    {
        return function ($wish) use ($id) {
            return is_array($wish) ? $wish['id'] : $wish->id() === $id;
        };
    }
}
