<?php declare(strict_types=1);

namespace Dive\Wishlist\Middleware;

use Closure;
use Dive\Wishlist\Actions\MigrateWishesAction;
use Illuminate\Http\Request;

class MigrateWishes
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && $request->hasCookie(config('wishlist.cookie.name'))) {
            app(MigrateWishesAction::class)->execute();
        }

        return $next($request);
    }
}
