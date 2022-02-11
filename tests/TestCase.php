<?php declare(strict_types=1);

namespace Tests;

use Dive\Wishlist\Facades\Wishlist;
use Dive\Wishlist\WishlistServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\Fakes\Product;
use Tests\Fakes\Sample;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    protected function getPackageAliases($app)
    {
        return [
            'Wishlist' => Wishlist::class,
        ];
    }

    protected function getPackageProviders($app)
    {
        return [WishlistServiceProvider::class];
    }

    protected function setUpDatabase()
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('purveyors', function (Blueprint $table) {
            $table->id();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained();
            $table->string('name');
            $table->string('sku');
        });

        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purveyor_id')->constrained();
            $table->string('name');
        });

        $users = require __DIR__ . '/../vendor/orchestra/testbench-core/laravel/migrations/2014_10_12_000000_testbench_create_users_table.php';
        $wishes = require __DIR__ . '/../database/migrations/create_wishes_table.php.stub';

        $users->up();
        $wishes->up();

        Relation::morphMap([
            'product' => Product::class,
            'sample' => Sample::class,
        ]);
    }
}
