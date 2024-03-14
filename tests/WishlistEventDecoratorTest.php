<?php declare(strict_types=1);

namespace Tests;

use Closure;
use Dive\Wishlist\Events\WishlistTouched;
use Dive\Wishlist\InMemoryWishlist;
use Dive\Wishlist\WishlistEventDecorator;
use Illuminate\Contracts\Events\Dispatcher;
use Mockery;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

final class WishlistEventDecoratorTest extends TestCase
{
    #[DataProvider('methods')]
    #[Test]
    public function test_it_dispatches_a_touched_event_for_dirty_operations(string $method, ?Closure $arg = null): void
    {
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->withArgs(fn ($e) => $e instanceof WishlistTouched);

        WishlistEventDecorator::make(InMemoryWishlist::make(), $dispatcher)->{$method}(value($arg));
    }

    public static function methods(): array
    {
        return [
            ['add', self::wishable(...)],
            ['purge'],
            ['remove', self::wishable(...)],
        ];
    }
}
