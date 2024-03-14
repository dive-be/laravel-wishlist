<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\Attributes\Test;

final class InstallPackageTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();

        file_exists(config_path('wishlist.php')) && unlink(config_path('wishlist.php'));
        array_map('unlink', glob(database_path('migrations/*_create_wishes_table.php')));
    }

    #[Test]
    public function it_copies_the_config(): void
    {
        $this->artisan('wishlist:install')->execute();

        $this->assertTrue(file_exists(config_path('wishlist.php')));
    }

    #[Test]
    public function it_copies_the_migration(): void
    {
        $this->artisan('wishlist:install')->execute();

        $this->assertCount(1, glob(database_path('migrations/*_create_wishes_table.php')));
    }
}
