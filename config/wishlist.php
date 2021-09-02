<?php

return [
    'cookie' => [
        'domain' => env('WISHLIST_COOKIE_DOMAIN', env('SESSION_DOMAIN')),
        'max_age' => env('WISHLIST_COOKIE_MAX_AGE', 43200),
        'name' => env('WISHLIST_COOKIE_NAME', 'wishlist'),
    ],

    /**
     * Supported drivers:
     * - "array" (only available during the current request lifecycle)
     * - "cookie" (persists the user's wishes as a serialized string inside a cookie)
     * - "eloquent" (persists the users' wishes to the `wishes` table)
     * - "upgrade" (uses the cookie driver if a user is not authenticated, otherwise uses the eloquent driver)
     */
    'driver' => env('WISHLIST_DRIVER', Dive\Wishlist\WishlistManager::ELOQUENT),

    'eloquent' => [
        'scope' => 'default',
        'user' => config('auth.providers.users.model'),
    ],
];
