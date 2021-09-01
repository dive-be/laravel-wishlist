<?php

namespace Tests;

use function Pest\Laravel\artisan;

afterEach(function () {
    file_exists(config_path('wishlist.php')) && unlink(config_path('wishlist.php'));
    array_map('unlink', glob(database_path('migrations/*_create_wishes_table.php')));
});

it('copies the config', function () {
    artisan('wishlist:install')->execute();

    expect(file_exists(config_path('wishlist.php')))->toBeTrue();
});

it('copies the migration', function () {
    artisan('wishlist:install')->execute();

    expect(glob(database_path('migrations/*_create_wishes_table.php')))->toHaveCount(1);
});
