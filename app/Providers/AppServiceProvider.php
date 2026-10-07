<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // The default pagination view needs Tailwind; ours is plain CSS that matches the app.
        Paginator::defaultView('pagination.simple');
        Paginator::defaultSimpleView('pagination.simple');
    }
}
