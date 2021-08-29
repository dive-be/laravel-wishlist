<?php

namespace Dive\Wishlist\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphOne;

interface Wishable
{
    public function getKey();

    public function getMorphClass();

    public function getMorphKey(): string;

    public function wish(): MorphOne;
}
