<?php declare(strict_types=1);

return [
    /**
     * The authentication guard to use when using the `eloquent` or `upgrade` drivers.
     */
    'auth_guard' => config('auth.defaults.guard'),

    'cookie' => [
        /**
         * You may choose to scope the cookies to a particular subdomain. Especially useful when serving multiple apps.
         * The default (no scoping) will suffice for most apps, though.
         */
        'domain' => env('WISHLIST_COOKIE_DOMAIN', env('SESSION_DOMAIN')),

        /**
         * Time-to-Live in minutes. Default 43200 (1 month).
         */
        'max_age' => env('WISHLIST_COOKIE_MAX_AGE', 43_200),

        /**
         * You may customize the name of the cookie. Completely optional.
         */
        'name' => env('WISHLIST_COOKIE_NAME', 'wishlist'),
    ],

    /**
     * Supported drivers:
     * - "array" (only available during the current request lifecycle)
     * - "cookie" (persists the user's wishes as a serialized string inside a cookie)
     * - "eloquent" (persists the users' wishes to the `wishes` table)
     * - "upgrade" (uses the cookie driver if a user is not authenticated, otherwise uses the eloquent driver).
     */
    'driver' => env('WISHLIST_DRIVER', Dive\Wishlist\WishlistManager::ELOQUENT),

    'eloquent' => [
        /**
         * The model that should be used with this driver.
         * It must be, or extend the base Wish model.
         */
        'model' => Dive\Wishlist\Models\Wish::class,

        /**
         * You may choose to provide a context for the saved wishes in the database.
         * Particularly useful when serving multiple apps. The default will suffice for most apps, though.
         */
        'scope' => 'default',

        /**
         * The user model of your application. For most apps, the default will suffice.
         * However, if you wish, you may customize that here. This is used for the Wish model's
         * user relationship so you can display the owner of the wish in .e.g. Laravel Nova.
         */
        'user' => config('auth.providers.users.model'),
    ],
];
