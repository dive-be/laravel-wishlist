<?php declare(strict_types=1);

namespace Tests;

use Illuminate\Database\Eloquent\Relations\MorphOne;
use PHPUnit\Framework\Attributes\Test;

final class CanBeWishedTest extends TestCase
{
    #[Test]
    public function model_can_form_a_morph_key(): void
    {
        $this->assertSame('product-1', $this->product()->getMorphKey());
    }

    #[Test]
    public function model_has_a_polymorphic_wish_relation(): void
    {
        $this->assertInstanceOf(MorphOne::class, $this->product()->wish());
        $this->assertSame('wishes.wishable_type', $this->product()->wish()->getQualifiedMorphType());
    }
}
