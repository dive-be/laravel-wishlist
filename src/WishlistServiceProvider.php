<?php

namespace Dive\Wishlist;

use Dive\Wishlist\Commands\InstallPackageCommand;
use Dive\Wishlist\Contracts\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class WishlistServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->registerCommands();
            $this->registerConfig();
            $this->registerMigration();
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/wishlist.php', 'wishlist');

        $this->app->singleton(WishlistManager::class);
        $this->app->alias(WishlistManager::class, Wishlist::class);
        $this->app->alias(WishlistManager::class, 'wishlist');
    }

    private function registerCommands()
    {
        $this->commands([
            InstallPackageCommand::class,
        ]);
    }

    private function registerConfig()
    {
        $config = 'wishlist.php';

        $this->publishes([
            __DIR__.'/../config/'.$config => $this->app->configPath($config),
        ], 'config');
    }

    private function registerMigration()
    {
        $migration = 'create_wishes_table.php';
        $doesntExist = Collection::make(glob($this->app->databasePath('migrations/*.php')))
            ->every(fn ($filename) => ! str_ends_with($filename, $migration));

        if ($doesntExist) {
            $timestamp = date('Y_m_d_His', time());
            $stub = __DIR__."/../database/migrations/{$migration}.stub";

            $this->publishes([
                $stub => $this->app->databasePath("migrations/{$timestamp}_{$migration}"),
            ], 'migrations');
        }
    }
}
