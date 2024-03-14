<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Contracts\Wishlist;
use Dive\Wishlist\CookieWishlist;
use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;
use Illuminate\Cookie\CookieJar;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;

final class CookieWishlistTest extends TestCase
{
    use WishlistContractTests;

    private CookieJar $jar;

    #[Test]
    public function it_enqueues_a_new_cookie_when_modifications_happen(): void
    {
        $wishlist = $this->wishlist();

        $this->assertEmpty($this->jar->getQueuedCookies());

        $wishlist->add($wishable = $this->wishable());

        $this->assertCount(1, $cookies = $this->jar->getQueuedCookies());
        $this->assertGreaterThan(time(), $cookies[0]->getExpiresTime());

        $this->jar->flushQueuedCookies();

        $wishlist->add($wishable);

        $this->assertEmpty($this->jar->getQueuedCookies());

        $this->jar->flushQueuedCookies();

        $wishlist->remove($wishable);

        $this->assertCount(1, $cookies = $this->jar->getQueuedCookies());
        $this->assertGreaterThan(time(), $cookies[0]->getExpiresTime());

        $this->jar->flushQueuedCookies();

        $wishlist->remove($wishable);

        $this->assertEmpty($this->jar->getQueuedCookies());
    }

    #[Test]
    public function it_forgets_the_cookie_when_a_purge_takes_place(): void
    {
        $wishlist = $this->wishlist([Wish::make('1', $this->wishable())]);

        $this->assertEmpty($this->jar->getQueuedCookies());

        $wishlist->purge();

        $this->assertCount(1, $cookies = $this->jar->getQueuedCookies());
        $this->assertLessThan(time(), $cookies[0]->getExpiresTime());
    }

    #[Test]
    public function it_hydrates_the_collection_when_necessary(): void
    {
        $this->wishlist([
            Wish::make('1', $this->wishable()),
            Wish::make('2', $this->wishable()),
        ])->all()->each(fn (Wish $wish) => $this->assertInstanceOf(Wish::class, $wish));
    }

    #[Test]
    public function it_retrieves_existing_wishes_from_the_correct_cookie(): void
    {
        $this->assertCount(2, $this->wishlist([
            Wish::make('1', $this->wishable()),
            Wish::make('2', $this->wishable()),
        ]));
    }

    protected function getInstance(): Wishlist
    {
        return $this->wishlist();
    }

    private function wishlist(array $state = []): CookieWishlist
    {
        return CookieWishlist::make(
            $this->jar = new CookieJar(),
            new Request(cookies: [
                'wishlist' => serialize(WishCollection::make($state)),
            ]),
            [
                'domain' => '.localhost',
                'max_age' => 1337,
                'name' => 'wishlist',
            ]
        );
    }
}
