<?php declare(strict_types=1);

namespace Dive\Wishlist\Commands;

use Illuminate\Console\Command;

class InstallPackageCommand extends Command
{
    protected $description = 'Install wishlist.';

    protected $signature = 'wishlist:install';

    public function handle(): int
    {
        if ($this->isHidden()) {
            $this->error('🤚  Wishlist is already installed.');

            return self::FAILURE;
        }

        $this->line('🏎  Installing laravel-wishlist...');
        $this->line('📑  Publishing configuration...');

        $this->call('vendor:publish', [
            '--provider' => "Dive\Wishlist\WishlistServiceProvider",
            '--tag' => 'config',
        ]);

        $this->line('📑  Publishing migration...');

        $this->call('vendor:publish', [
            '--provider' => "Dive\Wishlist\WishlistServiceProvider",
            '--tag' => 'migrations',
        ]);

        $this->info('🏁  Wishlist installed successfully!');

        return self::SUCCESS;
    }

    public function isHidden(): bool
    {
        return file_exists(config_path('wishlist.php'));
    }
}
