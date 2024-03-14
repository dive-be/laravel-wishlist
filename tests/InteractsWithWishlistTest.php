<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Models\Wish;
use PHPUnit\Framework\Attributes\Test;

final class InteractsWithWishlistTest extends TestCase
{
    #[Test]
    public function user_can_wish_or_unwish_something(): void
    {
        $this->assertDatabaseCount(Wish::class, 0);

        $user = $this->user();
        $user->wish($product = $this->product());

        $this->assertInstanceOf(Wish::class, $wish = Wish::first());

        $user->unwish($product);

        $this->assertTrue($wish->refresh()->trashed());
    }

    #[Test]
    public function user_can_retrieve_their_own_wishlist(): void
    {
        $user = $this->user();
        $user->wish($this->product());
        $user->wish($product = $this->product());
        $user->wish($this->sample());
        $user->wish($this->product());
        $user->unwish($product);

        $this->assertCount(3, $user->wishes());
    }
}
