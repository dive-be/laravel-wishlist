<?php declare(strict_types=1);

namespace Dive\Wishlist\Listeners;

use Dive\Wishlist\Actions\MigrateWishesAction;
use Dive\Wishlist\WishlistManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class MigrateWishes
{
    public function __construct(
        private Request $request,
        private WishlistManager $wishlist,
    ) {}

    public function handle(Login $event)
    {
        if ($this->request->hasCookie($this->wishlist->config('cookie.name'))) {
            app(MigrateWishesAction::class)->execute();
        }
    }
}
