<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Wish;
use Dive\Wishlist\WishCollection;
use Illuminate\Support\Collection;
use LogicException;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakes\Product;
use Tests\Fakes\Sample;

final class WishCollectionTest extends TestCase
{
    private WishCollection $collection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collection = WishCollection::make([
            Wish::make('09c09144-42fd-47b2-98b9-396687eb23ca', $this->product()),
            Wish::make('e133a571-ddde-49ff-9657-b76d25a42901', $this->wishable = $this->sample()),
            Wish::make('d33886a4-491e-4dce-894d-9569650dd658', $this->product()),
        ]);
    }

    #[Test]
    public function it_can_find_a_wish_using_a_wishable(): void
    {
        $wish = $this->collection->find($this->wishable);

        $this->assertSame('e133a571-ddde-49ff-9657-b76d25a42901', $wish->id);
        $this->assertInstanceOf(Wish::class, $wish);
        $this->assertInstanceOf(Sample::class, $wish->wishable);
    }

    #[Test]
    public function it_can_determine_existence_using_a_wishable(): void
    {
        $this->assertTrue($this->collection->exists($this->wishable));
        $this->assertFalse($this->collection->exists($this->product()));
    }

    #[Test]
    public function it_can_exclude_a_single_wish(): void
    {
        $collection = $this->collection->without($this->wishable);

        $this->assertCount(2, $collection);
        $this->assertFalse($collection->exists($this->wishable));
    }

    #[Test]
    public function it_can_retrieve_the_with_ids(): void
    {
        $this->assertInstanceOf(Collection::class, $this->collection->ids());
        $this->assertNotInstanceOf(WishCollection::class, $this->collection->ids());
        $this->assertSame([
            '09c09144-42fd-47b2-98b9-396687eb23ca',
            'e133a571-ddde-49ff-9657-b76d25a42901',
            'd33886a4-491e-4dce-894d-9569650dd658',
        ], $this->collection->ids()->all());
    }

    #[Test]
    public function it_can_retrieve_the_wishables(): void
    {
        $wishables = $this->collection->wishables();

        $wishables->each(function ($wishable, $idx) {
            $this->assertTrue($wishable->is($this->collection->get($idx)->wishable));
        });
    }

    #[Test]
    public function it_can_group_wishes_by_wishable_morph_type(): void
    {
        $grouped = $this->collection->groupByType();

        $this->assertInstanceOf(Collection::class, $grouped);
        $this->assertNotInstanceOf(WishCollection::class, $grouped);
        $this->assertCount(2, $grouped);
        $grouped->each(function ($group) {
            $this->assertInstanceOf(WishCollection::class, $group);
        });
    }

    #[Test]
    public function it_can_retrieve_wishes_of_a_single_morph_type(): void
    {
        $products = $this->collection->ofType(Product::class);
        $samples = $this->collection->ofType(Sample::class);

        $this->assertInstanceOf(WishCollection::class, $products);
        $this->assertInstanceOf(WishCollection::class, $samples);
        $this->assertCount(2, $products);
        $this->assertCount(1, $samples);
        $products->each(function ($wish) {
            $this->assertInstanceOf(Product::class, $wish->wishable);
        });
        $samples->each(function ($wish) {
            $this->assertInstanceOf(Sample::class, $wish->wishable);
        });
    }

    #[Test]
    public function it_can_eager_load_the_relations_of_wishables(): void
    {
        $products = $this->collection->ofType(Product::class);
        $samples = $this->collection->ofType(Sample::class);

        $products->each(function ($wish) {
            $this->assertFalse($wish->wishable->relationLoaded('variant'));
        });
        $samples->each(function ($wish) {
            $this->assertFalse($wish->wishable->relationLoaded('purveyor'));
        });

        $this->collection->load([
            Product::class => 'variant',
            Sample::class => 'purveyor',
        ]);

        $products->each(function ($wish) {
            $this->assertTrue($wish->wishable->relationLoaded('variant'));
        });
        $samples->each(function ($wish) {
            $this->assertTrue($wish->wishable->relationLoaded('purveyor'));
        });
    }

    #[Test]
    public function it_can_eager_load_without_a_type_relation_map_when_unambiguous(): void
    {
        $products = $this->collection->ofType(Product::class);

        $products->each(function ($wish) {
            $this->assertFalse($wish->wishable->relationLoaded('variant'));
        });

        $products->load('variant');

        $products->each(function ($wish) {
            $this->assertTrue($wish->wishable->relationLoaded('variant'));
        });
    }

    #[Test]
    public function it_can_replace_plain_array_representations_of_wishes_with_a_hydrated_wish_instance(): void
    {
        $collection = WishCollection::make($this->collection->map->toArray()->all());

        $collection->each(function ($wish) {
            $this->assertIsArray($wish);
            $this->assertArrayHasKey('id', $wish);
            $this->assertArrayHasKey('wishable', $wish);
        });

        $collection->hydrate();

        $collection->each(function ($wish) {
            $this->assertInstanceOf(Wish::class, $wish);
        });
    }

    #[Test]
    public function it_throws_if_the_eager_load_is_ambiguous(): void
    {
        $this->expectException(LogicException::class);
        $this->collection->load('variant');
    }

    #[Test]
    public function it_can_find_a_wish_whose_wishable_has_an_integer_key(): void
    {
        $collection = $this->collection->slice(0, 1)->transform(fn ($wish) => $wish->toArray());

        $wish = $collection->find('09c09144-42fd-47b2-98b9-396687eb23ca');

        $this->assertInstanceOf(Wish::class, $wish);
        $this->assertSame($collection->first(), $wish);
    }
}
