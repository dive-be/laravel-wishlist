<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Closure;
use Dive\Wishlist\Contracts\Wishable;
use Dive\Wishlist\Models\Wish as Model;
use Exception;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use JsonSerializable;

/**
 * @property string   $id
 * @property Wishable $wishable
 */
final class Wish implements Arrayable, Jsonable, JsonSerializable, UrlRoutable
{
    private static Closure $managerResolver;

    private array $attributes;

    public static function of(Model $model): self
    {
        return self::make($model->getAttribute('uuid'), $model->getRelationValue('wishable'));
    }

    public static function make(string $id, Wishable $wishable): self
    {
        return (new self())->fill(compact('id', 'wishable'));
    }

    public static function setManagerResolver(Closure $resolver): void
    {
        self::$managerResolver = $resolver;
    }

    public function delete(): bool
    {
        if (isset(self::$managerResolver)) {
            return call_user_func(self::$managerResolver)->remove($this);
        }

        return false;
    }

    public function fill(array $attributes): self
    {
        if (! isset($this->attributes)) {
            $this->attributes = $attributes;
        }

        return $this;
    }

    public function getRouteKey(): string
    {
        return $this->id;
    }

    public function getRouteKeyName(): string
    {
        return 'wish';
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function resolveRouteBinding($value, $field = null): ?self
    {
        return isset(self::$managerResolver)
            ? call_user_func(self::$managerResolver)->find($value)
            : null;
    }

    public function resolveChildRouteBinding($childType, $value, $field): never
    {
        $class = self::class;

        throw new Exception("{$class} shouldn't be implicitly resolved from other bindings.");
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'wishable' => [
                'id' => $this->wishable->getKey(),
                'type' => $this->wishable->getMorphClass(),
            ],
        ];
    }

    public function toJson($options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    public function __get(string $name): mixed
    {
        if (! array_key_exists($name, $this->attributes)) {
            $class = self::class;

            throw new Exception("Undefined property: {$class}::{$name}");
        }

        return $this->attributes[$name];
    }
}
