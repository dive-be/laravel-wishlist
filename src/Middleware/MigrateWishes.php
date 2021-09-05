<?php declare(strict_types=1);

namespace Dive\Wishlist\Middleware;

use Closure;
use Dive\Wishlist\Actions\MigrateWishesAction;
use Dive\Wishlist\WishlistManager;
use Illuminate\Http\Request;

class MigrateWishes
{
    public function __construct(private WishlistManager $wishlist) {}

    public function handle(Request $request, Closure $next)
    {
        if ($this->wishlist->auth()->check()
            && $request->hasCookie($this->wishlist->config('cookie.name'))
        ) {
            app(MigrateWishesAction::class)->execute();
        }

        return $next($request);
    }
}
