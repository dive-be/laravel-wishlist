<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Actions\MigrateWishesAction;
use Dive\Wishlist\WishlistManager;
use PHPUnit\Framework\Attributes\Test;

final class MigrateWishesTest extends TestCase
{
    #[Test]
    public function it_can_migrate_the_wishes_from_the_cookie_driver_to_the_eloquent_driver(): void
    {
        $this->actingAs($this->user());

        $manager = app(WishlistManager::class);

        $cookie = $manager->driver(WishlistManager::COOKIE);
        $cookie->add($this->product());
        $cookie->add($this->sample());
        $cookie->add($this->product());

        $eloquent = $manager->driver(WishlistManager::ELOQUENT);

        $this->assertCount(3, $cookie);
        $this->assertCount(0, $eloquent);

        new MigrateWishesAction($manager)->execute();

        $this->assertCount(0, $cookie);
        $this->assertCount(3, $eloquent);
    }
}
