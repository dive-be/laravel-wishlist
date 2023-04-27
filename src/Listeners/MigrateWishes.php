<?php declare(strict_types=1);

namespace Dive\Wishlist\Listeners;

use Dive\Wishlist\Actions\MigrateWishesAction;
use Dive\Wishlist\WishlistManager;
use Illuminate\Http\Request;

final readonly class MigrateWishes
{
    public function __construct(private Request $request, private WishlistManager $wishlist) {}

    public function handle(): void
    {
        if ($this->request->hasCookie($this->wishlist->config('cookie.name'))) {
            app(MigrateWishesAction::class)->execute();
        }
    }
}
