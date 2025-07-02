<?php

namespace App\Providers;

use App\Models\StaticPage;
use Illuminate\Support\ServiceProvider;

class StaticPageProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('globalStaticPage', function ($app) {
            return StaticPage::where('status', 1)->get();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
