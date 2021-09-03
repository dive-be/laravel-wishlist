<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Models\Wish as Model;
use Dive\Wishlist\Support\Makeable;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * @property int|string $id
 * @property Wishable   $wishable
 */
class Wish implements Arrayable, Jsonable, JsonSerializable
{
    use Makeable;

    private array $attributes;

    public function __construct(int|string $id, Wishable $wishable)
    {
        $this->attributes = compact('id', 'wishable');
    }

    public static function of(Model $model): self
    {
        return new self($model->getKey(), $model->getRelationValue('wishable'));
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

    public function __get(string $name)
    {
        if (! array_key_exists($name, $this->attributes)) {
            $class = static::class;

            throw new Exception("Undefined property: {$class}::{$name}");
        }

        return $this->attributes[$name];
    }
}
