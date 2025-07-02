<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class ProductServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    // public function register(): void
    // {
    //     $this->app->singleton('priceRange', function ($app) {
    //         $data = DB::select('SELECT MIN(price) AS min_price, MAX(price) AS max_price, MIN(miles) as min_miles, MAX(miles) as max_miles FROM inventories');
    //         return [
    //             'minPrice' => $data[0]->min_price,
    //             'maxPrice' => $data[0]->max_price,
    //             'minMiles' => $data[0]->min_miles,
    //             'maxMiles' => $data[0]->max_miles,
    //         ];
    //     });
    // }

    public function register(): void
    {
        $this->app->singleton('priceRange', function ($app) {
            $newData = DB::select('SELECT MIN(price) AS min_price, MAX(price) AS max_price, MIN(miles) as min_miles, MAX(miles) as max_miles FROM main_inventories WHERE type = "new"');
            $usedData = DB::select('SELECT MIN(price) AS min_price, MAX(price) AS max_price, MIN(miles) as min_miles, MAX(miles) as max_miles FROM main_inventories WHERE type != "new"');
            return [
                'new' => [
                    'minPrice' => $newData[0]->min_price ?? null,
                    'maxPrice' => $newData[0]->max_price ?? null,
                    'minMiles' => $newData[0]->min_miles ?? null,
                    'maxMiles' => $newData[0]->max_miles ?? null,
                ],
                'used' => [
                    'minPrice' => $usedData[0]->min_price ?? null,
                    'maxPrice' => $usedData[0]->max_price ?? null,
                    'minMiles' => $usedData[0]->min_miles ?? null,
                    'maxMiles' => $usedData[0]->max_miles ?? null,
                ],
            ];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $view->with('priceRange', app('priceRange'));
        });
    }
}
