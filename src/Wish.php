<?php

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Models\Wish as Model;
use Dive\Wishlist\Support\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

class Wish implements Arrayable, Jsonable, JsonSerializable
{
    use Makeable;

    public function __construct(
        private int|string $id,
        private Wishable $wishable,
    ) {}

    public static function fromModel(Model $wish): self
    {
        return self::make($wish->getKey(), $wish->wishable);
    }

    public function id(): int|string
    {
        return $this->id;
    }

    public function wishable(): Wishable
    {
        return $this->wishable;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'wishable' => [
                'id' => $this->wishable->getKey(),
                'type' => $this->wishable->getMorphClass(),
            ],
        ];
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
