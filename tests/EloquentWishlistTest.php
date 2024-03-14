<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\EloquentWishlist;
use Dive\Wishlist\Models\Wish as Model;
use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;
use PHPUnit\Framework\Attributes\Test;

final class EloquentWishlistTest extends TestCase
{
    use WishlistContractTests;

    #[Test]
    public function it_can_merge_a_collection_of_wishes_with_its_own(): void
    {
        $wishlist = $this->getInstance();

        $wishlist->add($this->product());
        $sampleA = $wishlist->add($this->sample());
        $productA = $wishlist->add($this->product());

        $wishlist->merge(WishCollection::make([
            $sampleA, // duplicate
            Wish::make('1234', $this->sample()),
            $productA, // duplicate
            Wish::make('5678', $this->product()),
        ]));

        $this->assertCount(5, $wishlist);
    }

    protected function getInstance(): Wishlist
    {
        return EloquentWishlist::make(new Model(), $this->user()->getKey(), '*');
    }
}
