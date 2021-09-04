<?php declare(strict_types=1);

namespace Dive\Wishlist\Contracts;

use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;

interface Wishlist
{
    public function add(Wishable $wishable): Wish;

    public function all(): WishCollection;

    public function count(): int;

    public function find(string|Wishable $id): ?Wish;

    public function has(Wishable $wishable): bool;

    public function isEmpty(): bool;

    public function isNotEmpty(): bool;

    public function purge(): int;

    public function remove(string|Wish|Wishable $id): bool;
}
