<?php

namespace Dive\Wishlist\Listeners;

use Dive\Wishlist\Actions\MigrateWishesAction;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

class MigrateWishes
{
    public function __construct(
        private Repository $config,
        private Request $request,
    ) {}

    public function handle(Login $event)
    {
        if ($this->request->hasCookie($this->config->get('wishlist.cookie.name'))) {
            app(MigrateWishesAction::class)->execute();
        }
    }
}
