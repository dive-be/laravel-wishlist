<?php

namespace Tests;

use function Pest\Laravel\artisan;

afterEach(function () {
    file_exists(config_path('laravel-wishlist.php')) && unlink(config_path('laravel-wishlist.php'));
    array_map('unlink', glob(database_path('migrations/*_create_laravel_wishlist_table.php')));
});

it('copies the config', function () {
    artisan('laravel-wishlist:install')->execute();

    expect(file_exists(config_path('laravel-wishlist.php')))->toBeTrue();
});

it('copies the migration', function () {
    artisan('laravel-wishlist:install')->execute();

    expect(glob(database_path('migrations/*_create_laravel_wishlist_table.php')))->toHaveCount(1);
});
