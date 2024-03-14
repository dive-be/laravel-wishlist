<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\Wish;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;

/** @mixin TestCase */
trait WishlistContractTests
{
    abstract protected function getInstance(): Wishlist;

    #[Test]
    public function it_can_add_a_new_wish(): void
    {
        $wishlist = $this->getInstance();

        $this->assertTrue($wishlist->isEmpty());

        $wish = $wishlist->add($wishable = $this->wishable());

        $this->assertTrue($wishlist->isNotEmpty());
        $this->assertInstanceOf(Wish::class, $wish);
        $this->assertNotNull($wish->id);
        $this->assertSame($wish->wishable->getKey(), $wishable->getKey());
        $this->assertSame($wish->wishable->getMorphClass(), $wishable->getMorphClass());
    }

    #[Test]
    public function it_does_not_create_a_new_wish_if_it_already_exists(): void
    {
        $wishlist = $this->getInstance();

        $wishA = $wishlist->add($this->wishable());
        $wishB = $wishlist->add($wishA->wishable);

        $this->assertSame($wishB->id, $wishA->id);
    }

    #[Test]
    public function it_can_retrieve_all_wishes(): void
    {
        $wishlist = $this->getInstance();

        $wishes = Collection::make([
            $wishlist->add($this->wishable()),
            $wishlist->add($this->wishable()),
            $wishlist->add($this->wishable()),
        ]);

        $wishlist->all()->each(function (Wish $wish, int $idx) use ($wishes) {
            $this->assertSame($wish->id, $wishes->get($idx)->id);
            $this->assertSame($wish->wishable->getKey(), $wishes->get($idx)->wishable->getKey());
        });
    }

    #[Test]
    public function it_can_retrieve_the_count(): void
    {
        $wishlist = $this->getInstance();

        $this->assertSame(0, $wishlist->count());

        $wishlist->add($this->wishable());

        $this->assertSame(1, $wishlist->count());
    }

    #[Test]
    public function it_can_determine_emptiness(): void
    {
        $wishlist = $this->getInstance();

        $this->assertFalse($wishlist->isNotEmpty());
        $this->assertTrue($wishlist->isEmpty());

        $wishlist->add($this->wishable());

        $this->assertTrue($wishlist->isNotEmpty());
        $this->assertFalse($wishlist->isEmpty());
    }

    #[Test]
    public function it_can_determine_if_a_wishable_has_already_been_wished_for(): void
    {
        $wishlist = $this->getInstance();

        $this->assertFalse($wishlist->has($wishable = $this->wishable()));

        $wishlist->add($wishable);

        $this->assertTrue($wishlist->has($wishable));
    }

    #[Test]
    public function it_can_be_purged(): void
    {
        $wishlist = $this->getInstance();

        $wishlist->add($this->wishable());
        $wishlist->add($this->wishable());

        $this->assertSame(2, $wishlist->count());

        $purged = $wishlist->purge();

        $this->assertSame(0, $wishlist->count());
        $this->assertSame(2, $purged);
    }

    #[Test]
    public function it_can_remove_a_wish(): void
    {
        $wishlist = $this->getInstance();

        $wish = $wishlist->add($this->wishable());

        $resultA = $wishlist->remove($wish);
        $resultB = $wishlist->remove($this->wishable());

        $this->assertTrue($resultA);
        $this->assertFalse($resultB);
        $this->assertTrue($wishlist->isEmpty());
    }
}
