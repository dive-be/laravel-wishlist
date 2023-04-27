<?php declare(strict_types=1);

namespace Dive\Wishlist\Support;

trait PoorMansCaching
{
    private array $cache = [
        'methods' => [],
        'pristine' => [],
    ];

    private function markAsDirty(): void
    {
        foreach ($this->cache['pristine'] as $key => $_) {
            $this->cache['pristine'][$key] = false;
        }
    }

    private function remember(\Closure $callback): mixed
    {
        [$one, $caller] = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        $method = $caller['function'];

        if (array_key_exists($method, $this->cache['pristine'])
            && $this->cache['pristine'][$method]
            && array_key_exists($method, $this->cache['methods'])
            && ! is_null($value = $this->cache['methods'][$method])
        ) {
            return $value;
        }

        return tap($callback(), function ($result) use ($method) {
            $this->cache['pristine'][$method] = true;
            $this->cache['methods'][$method] = $result;
        });
    }
}
