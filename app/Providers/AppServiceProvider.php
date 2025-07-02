<?php

namespace App\Providers;

use App\Models\Icon;
use App\Models\Logo;
use App\Models\Menu;
use App\Models\Banner;
use App\Models\Inventory;
use App\Models\FooterMenu;
use App\Models\Notification;
use App\Service\LeadService;
use App\Service\UserService;
use App\Models\FooterContent;
use App\Models\GeneralSetting;
use App\Service\InventoryService;
use App\Observers\InventoryObserver;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cookie;
use App\Interface\LeadServiceInterface;
use App\Interface\UserServiceInterface;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;
use Stevebauman\Location\Facades\Location;
use App\Interface\InventoryServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        app()->bind(InventoryServiceInterface::class, InventoryService::class);
        app()->bind(LeadServiceInterface::class, LeadService::class);
        app()->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inventory::observe(InventoryObserver::class);

        View::composer('frontend.website.layout.topbar', function ($view) {
            $header_menus = Menu::with('submenus')
            ->where('parent', 0)
            ->where('status', 1)
            ->orderBy('column_position')
            ->get();

            $logo = GeneralSetting::first();
            $files = Icon::where('status',1)->get();

            $view->with(['header_menus'=> $header_menus, 'logo' => $logo, 'files'=>$files ]);
        });
        View::composer('frontend.website.layout.sidebar', function ($view) {
            $header_menus = Menu::with('submenus')
            ->where('parent', 0)
            ->where('status', 1)
            ->orderBy('column_position')
            ->get();



            $view->with(['header_menus'=> $header_menus ]);
        });
        View::composer('frontend.website.layout.home', function ($view) {


            $slider = GeneralSetting::first();


            $view->with(['slider' => $slider]);
        });



        View::composer('frontend.website.layout.footer', function ($view) {
            $footer_menus = FooterMenu::where('status',1)
            ->get();
            $footer_content = FooterContent::first();
            $view->with(['footer_menus'=> $footer_menus,'footer_content' => $footer_content]);
        });
        View::composer('backend.admin.components.header-top_nav-link', function ($view) {
            $notifications = Notification::where('is_read','0')->orderBy('id','desc')->get();
        $number = $notifications->count();
            $view->with(['notifications'=> $notifications,'number' =>$number]);
        });

    // cookies code start here
    // $ip = Request::ip();
    // dd($ip);
    // $ip = '149.40.58.135';
    // $ip = '64.31.3.251';
    // $position = Location::get($ip);
    // dd($position);
    // $zippo = $this->setZipCode($position, $ip);
    // $cookie_zippo =  $this->getNearByZipCode(Cookie::get('zipcode'));
    //  $cookie_zippo =  Cookie::get('zipcode');
    //  View::share('cookie_zippo', $cookie_zippo);
    }

    public function  setZipCode($position, $ip)
    {
        if ($position) {
            $zipcode = $position->zipCode;
            $minutes = 7 * 24 * 60; // 7 days
            Cookie::queue('zipcode', $zipcode, $minutes);

            return response()->json([
                'ip' => $ip,
                'country' => $position->countryName,
                'region' => $position->regionName,
                'city' => $position->cityName,
                'zipcode' => $zipcode,
            ]);
        } else {
            return response()->json([
                'error' => 'Unable to retrieve location'
            ], 404);
        }
    }

    public function getNearByZipCode($zipCode)
    {
        $zipCodeList = Inventory::distinct()->pluck('zip_code')->toArray();

        $zipCodeDistances = [];
        foreach ($zipCodeList as $zipCode) {
            $distance = $this->calculateDistance($zipCode, $zipCodeList);
            return $distance;
            $zipCodeDistances[$zipCode] = $distance;
        }

        // Sort zip codes by distance
        asort($zipCodeDistances);

        // Return the closest zip code
        $closestZipCode = key($zipCodeDistances);
        return $closestZipCode;
    }

    public function calculateDistance($requestedZipCode, $zipCodeList)
    {
        $minDistance = PHP_INT_MAX;
        $nearestZipCode = '';

        foreach ($zipCodeList as $zipCode) {
            $distance = abs(intval($requestedZipCode) - intval($zipCode));
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestZipCode = $zipCode;
            }
        }

        return $nearestZipCode;
    }


}
