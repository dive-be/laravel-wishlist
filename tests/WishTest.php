<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Wish;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\Product;

final class WishTest extends TestCase
{
    private Wish $wish;

    protected function setUp(): void
    {
        parent::setUp();

        $this->wish = Wish::make('1337', new Product(['id' => 9876]));
    }

    #[Test]
    public function it_is_arrayable(): void
    {
        $this->assertSame([
            'id' => '1337',
            'wishable' => [
                'id' => 9876,
                'type' => (new Product())->getMorphClass(),
            ],
        ], $this->wish->toArray());
    }

    #[Test]
    public function it_is_json_serializable(): void
    {
        $this->assertSame($this->wish->toArray(), $this->wish->jsonSerialize());
    }

    #[Test]
    public function it_is_jsonable(): void
    {
        $this->assertSame(json_encode($this->wish), $this->wish->toJson());
    }

    #[Test]
    public function it_can_retrieve_the_wish_id(): void
    {
        $this->assertSame('1337', $this->wish->id);
    }

    #[Test]
    public function it_can_retrieve_the_wishable(): void
    {
        $this->assertInstanceOf(Product::class, $this->wish->wishable);
        $this->assertSame(9876, $this->wish->wishable->getKey());
    }

    #[Test]
    public function it_can_be_route_model_bound(): void
    {
        $this->mock('wishlist')
            ->shouldReceive('find')
            ->once()
            ->withArgs(fn ($id) => $id === 'Dive Hard')
            ->andReturn($this->wish);

        $this->assertSame($this->wish, $this->wish->resolveRouteBinding('Dive Hard'));
        $this->assertSame('1337', $this->wish->getRouteKey());
        $this->assertSame('wish', $this->wish->getRouteKeyName());
        $this->expectException(Exception::class);
        $this->wish->resolveChildRouteBinding('Dive', 'Very', 'Hard');
    }

    #[Test]
    public function it_can_be_deleted(): void
    {
        $this->mock('wishlist')
            ->shouldReceive('remove')
            ->once()
            ->withArgs(fn ($wish) => $wish === $this->wish)
            ->andReturn(true);

        $this->wish->delete();
    }

    #[Test]
    public function it_can_be_converted_to_an_array(): void
    {
        $this->assertSame([
            'id' => '1337',
            'wishable' => [
                'id' => 9876,
                'type' => (new Product())->getMorphClass(),
            ],
        ], $this->wish->toArray());
    }

    #[Test]
    public function it_can_be_converted_to_json(): void
    {
        $this->assertSame(json_encode($this->wish->toArray()), $this->wish->toJson());
    }
}
