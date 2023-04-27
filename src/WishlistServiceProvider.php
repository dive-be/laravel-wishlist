<?php declare(strict_types=1);

namespace Dive\Wishlist;

use Dive\Wishlist\Commands\InstallPackageCommand;
use Dive\Wishlist\Contracts\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

final class WishlistServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerConfig();
            $this->registerMigration();
        }

        Wish::setManagerResolver(fn () => $this->app['wishlist']);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/wishlist.php', 'wishlist');

        $this->app->singleton(WishlistManager::class);
        $this->app->alias(WishlistManager::class, Wishlist::class);
        $this->app->alias(WishlistManager::class, 'wishlist');
    }

    private function registerCommands(): void
    {
        $this->commands([
            InstallPackageCommand::class,
        ]);
    }

    private function registerConfig(): void
    {
        $config = 'wishlist.php';

        $this->publishes([
            __DIR__ . '/../config/' . $config => $this->app->configPath($config),
        ], 'config');
    }

    private function registerMigration(): void
    {
        $migration = 'create_wishes_table.php';
        $doesntExist = Collection::make(glob($this->app->databasePath('migrations/*.php')))
            ->every(fn ($filename) => ! str_ends_with($filename, $migration));

        if ($doesntExist) {
            $timestamp = date('Y_m_d_His', time());
            $stub = __DIR__ . "/../database/migrations/{$migration}.stub";

            $this->publishes([
                $stub => $this->app->databasePath("migrations/{$timestamp}_{$migration}"),
            ], 'migrations');
        }
    }
}
