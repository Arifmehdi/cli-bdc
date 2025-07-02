<?php

namespace App\Http\Controllers;


use App\Interface\InventoryServiceInterface;
use App\Mail\ContactMail;
use App\Mail\SubscribeMail;
use App\Mail\WelcomeEmail;
use App\Models\Admin\Setting;
use App\Models\Advertisement;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Contact;
use App\Models\Faq;
use App\Models\Favourite;
use App\Models\GeneralSetting;
use App\Models\Icon;
use App\Models\Inventory;
use App\Models\LatestVideo;
use App\Models\LocationCity;
use App\Models\LocationState;
use App\Models\MainInventory;
use App\Models\Menu;
use App\Models\News;
use App\Models\Review;
use App\Models\Slider;
use App\Models\Subscribe;
use App\Models\Message;
use App\Models\RequestInventory;
use App\Models\TermsCondition;
use App\Models\Tips;
use App\Models\VehicleMake;
use App\Models\VehicleYear;
use App\Models\VehicleModel;
use Illuminate\Http\Request;
use App\Models\TmpInventories;
use App\Models\Trending;
use App\Models\User;
use App\Models\LocationZip;
use App\Models\Page;
use App\Models\UserTrack;
use App\Models\VehicleBody;
use App\Traits\Notify;
use Illuminate\Auth\Events\Validated;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Jorenvh\Share\ShareFacade as Share;
use PDO;
use Stevebauman\Location\Facades\Location;
// use function PHPUnit\Framework\isEmpty;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SiteController extends Controller
{

    use Notify;

    private $inventoryService;
    public function __construct(InventoryServiceInterface $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        // // Check if the zip code exists in cookies
        // $zipcode = request()->cookie('zipcode');

        // if (!$zipcode) {
        //     // Get user IP (or use a default IP for testing)
        //     $ip = $request->ip();

        //     // Call a function to get location data (you can replace this with an API call)
        //     $position = $this->getUserLocation($ip);

        //     if ($position && isset($position['zipcode'])) {
        //         $zipcode = $position['zipcode'];

        //         // Set cookie for 7 days
        //         Cookie::queue('zipcode', $zipcode, 7 * 24 * 60);

        //         // Redirect with zip parameter in the URL
        //         return redirect()->route('home', ['zip' => $zipcode]);
        //     }
        // }
        $ip = $request->ip();
        $url = "http://ip-api.com/json/{$ip}";
        $response = @file_get_contents($url);

        $data = json_decode($response, true);
        if ($data['status'] == 'success') {
            $zipcode = $data['zip'];
            $minutes = 7 * 24 * 60;   // 7 days
            Cookie::queue('zipcode', $zipcode, $minutes);
        }




        $makes = VehicleMake::select('id', 'make_name', 'status')
            ->where('status', 1)
            ->orderBy('make_name', 'asc') // Ensure sorting by make_name
            ->get();    //optimized

        // Get inventory data for all makes in one query
        $inventoryData = MainInventory::where('type', '!=', 'new')
            ->whereIn('make', $makes->pluck('make_name'))
            ->selectRaw('make, COUNT(*) as count_make, MIN(CASE WHEN price > 0 THEN price END) as min_price')
            ->groupBy('make')
            ->orderBy('make', 'asc') // Ensure inventory data is sorted by make_name
            ->get()
            ->keyBy('make'); // Keep keyBy to allow efficient lookup   //optimized

        $makesData = VehicleMake::orderBy('make_name')->where('status', 1)->pluck('id', 'make_name');

        $years = VehicleYear::orderByDesc('year')->select('id', 'year', 'status')->where('status', 1)->get();    //optimized
        $news = News::with('user')->where('status', '1')->select('id', 'user_id', 'slug', 'title', 'sub_title', 'img', 'status')->get();   //optimized

        $slider = GeneralSetting::select('id', 'slider_image', 'slider_title', 'slider_subtitle')->first();   //optimized
        $bodies = VehicleBody::orderBy('name')->select('id', 'image', 'slug', 'name')->get();  //optimized

        $tips = Tips::where('status', 1)->select('id', 'title', 'slug', 'description', 'img', 'status')->orderBy('id', 'desc')->get();  //optimized 
        // return $userHistories;
        $tendings = Trending::where('status', 1)->select('id', 'title', 'slug', 'route', 'status')->orderBy('id', 'desc')->get();

        return view('frontend.home', compact('inventoryData', 'makesData', 'years', 'news', 'slider', 'bodies','tips'));


        // $ads = Advertisement::where('status', '1')
        //     ->orderBy('id', 'desc')
        //     ->take(2)
        //     ->get();
        // $ad1 = $ads->get(0); // First advertisement    //optimized    // not used now
        // $ad2 = $ads->get(1); // Second advertisement   //optimized   // not used now

        // $makes_obj = VehicleMake::orderBy('make_name');
        // $makes = $makes_obj->select('id','make_name','status')->where('status', 1)->get();
        //     $userHistories = UserTrack::where('type', 'Viewed')
        //     ->orWhere(function ($query) {
        //     $query->where('ip_address', request()->ip())
        //           ->where('type', 'Viewed');
        // })
        // ->orderBy('id', 'desc')
        // ->get();


        //     $totalCount = UserTrack::where('type', 'Viewed')
        //     ->orWhere(function ($query) {
        //         $query->where('ip_address', request()->ip())
        //             ->where('type', 'Viewed');
        //     })
        //     ->count();

        // // Fetch the top 12 records
        // $userHistories = UserTrack::where('type', 'Viewed')
        //     ->orWhere(function ($query) {
        //         $query->where('ip_address', request()->ip())
        //             ->where('type', 'Viewed');
        //     })->with('inventory')
        //     ->orderBy('count', 'desc')
        //     ->limit(12)
        //     ->get();
        // $header_menus = Menu::with('submenus')->where('parent', 0)->where('status', 1)->get();
        // $ad1 = Advertisement::where('status', '1')->orderBy('id', 'desc')->first();
        // $ad2 = Advertisement::where('status', '1')->orderBy('id', 'desc')->skip(1)->first();
        // return view('frontend.home', compact('inventoryData','makes', 'makesData', 'years', 'news', 'slider', 'bodies', 'ad1', 'ad2', 'userHistories', 'tendings'));
    }


    private function getUserLocation($ip)
    {

        // You can use an API like ip-api.com, ipinfo.io, or a database
        // Example: Using ip-api.com (free, but limited)
        $ip = '170.171.1.0';
        // $url = "http://ip-api.com/json/{$ip}?fields=status,country,regionName,city,zip";
        $url = "http://ip-api.com/json/{$ip}";
        $response = @file_get_contents($url);
        $data = json_decode($response, true);

        if ($data && $data['status'] === 'success') {
            return [
                'countryName' => $data['country'],
                'regionName' => $data['regionName'],
                'cityName' => $data['city'],
                'zipCode' => $data['zip']
            ];
        }

        return null;
    }


    public function auto(Request $request, $param = null)
    {
        // Handle clear cache requests
        $clear = $request->input('clear');
        if ($clear == 'flush') {
            Cookie::queue(Cookie::forget('searchData'));
            Cookie::queue(Cookie::forget('zipcode'));
            Cache::forget('vehicles_list');
            Cache::forget('vehicles_body_list');
            Cache::forget('vehicles_fuel_list');
            Cache::forget('price_range');
            Cache::forget('miles_range');
            return response()->json(['success' => 'clear']);
        }

        if ($clear == 'newCar') {
            Cookie::queue(Cookie::forget('searchData'));
            Cookie::queue(Cookie::forget('zipcode'));
            return response()->json(['success' => 'newcar']);
        }

        $zipCode = $request->input('zip');
        $radius = $request->input('radius', $zipCode ? 75 : null);
        $searchBody = $request->query('homeBodySearch');

        // Cache vehicle makes for 24 hours
        $vehicles = Cache::remember('vehicles_list', 60*24, function () {
            return VehicleMake::orderBy('make_name')->where('status', 1)->pluck('id', 'make_name');
        });

        // Cache distinct body types for 24 hours
        $vehicles_body = Cache::remember('vehicles_body_list', 60*24, function () {
            $inventory_obj = MainInventory::query();
            $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
            sort($vehicles_body);
            return $vehicles_body;
        });

        // Cache distinct fuel types for 24 hours
        $vehicles_fuel_other = Cache::remember('vehicles_fuel_list', 60*24, function () {
            $inventory_obj = MainInventory::query();
            $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();
            sort($vehicles_fuel_other);
            return $vehicles_fuel_other;
        });

        // Cache price and mileage ranges for 24 hours
        $price_range = Cache::remember('price_range', 60*24, function () {
            $inventory_obj = MainInventory::query();
            return [
                'max' => (int)$inventory_obj->where('price', '!=', 'N/A')->max('price'),
                'min' => (int)$inventory_obj->where('price', '!=', 'N/A')->min('price')
            ];
        });

        $miles_range = Cache::remember('miles_range', 60*24, function () {
            $inventory_obj = MainInventory::query();
            return [
                'max' => $inventory_obj->where('miles', '!=', 'N/A')->max('miles'),
                'min' => $inventory_obj->where('miles', '!=', 'N/A')->min('miles')
            ];
        });

        $price_max = $price_range['max'];
        $price_min = $price_range['min'];
        $miles_max = $miles_range['max'];
        $miles_min = $miles_range['min'];

        $global_make = '';
        if ($request->ajax()) {
            // Save user tracking data
            $currentUrl = $request->requestURL;
            $urlComponents = parse_url($currentUrl);
            $queryString = $urlComponents['query'] ?? '';
            $type = $queryString ? 'Searched' : 'Used';
            parse_str($queryString, $queryParams);

            $homeInventorySearch = $queryParams['homeBodySearch'] ?? null;
            $homeMakeSearch = $queryParams['homeMakeSearch'] ?? null;
            $homeBestMakeSearch = $queryParams['makeTypeSearch'] ?? null;
            $homeModelSearch = $queryParams['homeModelSearch'] ?? null;
            $dealer_id = $queryParams['dealer_id'] ?? null;

            $ip_address = $request->ip();
            $user_id = Auth::id() ?? null;
            $image = 'uploads/NotFound.png';
            $date = today();

            $existingRecord = UserTrack::where([
                'ip_address' => $ip_address,
                'type' => $type,
                'title' => $homeInventorySearch,
            ])
                ->whereDate('created_at', $date)
                ->first(['ip_address', 'type', 'title', 'created_at']);

            // if (!$existingRecord) {
            //     $history_saved = new UserTrack();
            //     $history_saved->type = $type;
            //     $history_saved->links = $currentUrl;
            //     $history_saved->title = $homeInventorySearch . ' ' . $homeMakeSearch . ' ' . $homeModelSearch . ' ' . $homeBestMakeSearch;
            //     $history_saved->image = $image;
            //     $history_saved->ip_address = $ip_address;
            //     $history_saved->user_id = $user_id;
            //     $history_saved->save();
            // }

                    // Get county name from zip code
        $zipCodeInfo = $request->weblocationNewInput; 
        $countyName = $this->fetchCountyName($zipCodeInfo);
        // $cacheKey = 'inventory_' . $countyName;
        // dd($cacheKey, $zipCodeInfo, $countyName);

    

        // Generate cache key based on request parameters
        // $cacheKey = 'inventory_' . md5(json_encode($request->all()) . $countyName);
        $cacheKey = 'inventory_' . md5(json_encode($request->except('weblocationNewInput','mobilelocation','requestURL')) . $countyName);
        // $cacheKey = 'inventory_' .  $countyName;

            // Initiate main inventory query with eager loading
            $mainInventory = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'inventory_status')
                ->with([
                    'dealer' => function ($query) {
                        $query->select('id', 'dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id');
                    },
                    'additionalInventory' => function ($query) {
                        $query->select('id', 'main_inventory_id', 'local_img_url');
                    },
                    'mainPriceHistory' => function ($query) {
                        $query->select('id', 'main_inventory_id', 'change_amount');
                    }
                ])->where(function ($query) {
                    $query->where('inventory_status', '!=', 'Sold')
                        ->orWhereNull('inventory_status'); // Include NULL values
                });

            // Apply filters
            $query = $this->inventoryService->getItemByFilter($request, $dealer_id, $mainInventory);

            // Parse URL parameters for dealer city/state filtering
            $urlData = parse_url($request->input('requestURL'));
            if (isset($urlData['query'])) {
                parse_str($urlData['query'], $queryParams);
                if (isset($queryParams['homeDealerCitySearch']) && isset($queryParams['homeDealerStateSearch'])) {
                    $city_data = $queryParams['homeDealerCitySearch'];
                    $state_data = $queryParams['homeDealerStateSearch'];

                    if (!empty($city_data)) {
                        $query->whereHas('dealer', function ($q) use ($city_data) {
                            $q->where('city', 'like', '%' . $city_data . '%');
                        });
                    }

                    if (!empty($state_data)) {
                        $query->whereHas('dealer', function ($q) use ($state_data) {
                            $q->where('state', 'like', '%' . $state_data . '%');
                        });
                    }
                }
            }

            // Extract more parameters from URL
            $urlComponents = parse_url($request->requestURL);
            $queryParams = [];
            if (isset($urlComponents['query'])) {
                parse_str($urlComponents['query'], $queryParams);
            }

            $homeBodySearch = $queryParams['homeBodySearch'] ?? null;
            $make = $queryParams['make'] ?? null;
            $model = $queryParams['model'] ?? null;
            $location = $queryParams['location'] ?? null;
            $zipCode = $queryParams['zip'] ?? null;
            $zip_radios = $queryParams['radius'] ?? null;
            $mobileLocation = $request->input('mobilelocation') ?? null;
            $webLocation = $request->input('weblocationNewInput') ?? null;
            $location = $webLocation ?? $mobileLocation ?? null;

            if ($location !== null) {
                queueZipCodeCookie($location);
            } else {
                Cookie::queue(Cookie::forget('zipcode'));
            }

            if ($make != null) {
                $query->where('make', $make);
                $global_make = $make;
            }

            if ($model != null) {
                $query->where('model', $model);
            }

            $query->whereNotNull('price')->where('price', '>', 1);
            $message = '';

            $zipCodeData = [
                'zip_code_data' => $zipCode,
                'zip_radios_data' => $zip_radios,
                'query_data' => $query,
            ];

            // Use Cache::remember for final inventory results
            // $cacheDuration = 30; // Cache for 30 minutes
            $cacheDuration = 1440; // Cache for 24 hours
            $cacheResult = null;

            if ($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide') {
                $cacheKey .= '_nationwide';
                $cacheResult = Cache::remember($cacheKey, $cacheDuration, function() use ($query) {
                    return [
                        'inventories' => $query->paginate(20),
                        'message' => null
                    ];
                });

                $inventories = $cacheResult['inventories'];
                $message = $cacheResult['message'];
            } else {
                $cacheKey .= '_' . ($zipCode ?? 'nozip') . '_' . ($zip_radios ?? 'noradius');

                // We can't cache paginated results directly, so we'll cache the query preparation
                $zipCodeResult = Cache::remember($cacheKey, $cacheDuration, function() use ($zipCodeData) {
                    return $this->getItemByDistance($zipCodeData['zip_code_data'], $zipCodeData['zip_radios_data'], $zipCodeData['query_data']);
                });

                $inventories = $zipCodeResult['inventories'];
                $message = $zipCodeResult['message'];
            }

            // $filteredInventories = collect($inventories); 
            // dd($filteredInventories);
            
            $current_page_count = $inventories->count();
            $total_count = number_format($inventories->total());
            $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;

            $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message'))->render();

            return response()->json([
                'view' => $view,
                'pagination' => $inventories->links()->toHtml(),
                'total_count' => $total_count,
                'message' => $message
            ]);
        }

        $make_data = $request->input('make');
        $states = LocationState::orderBy('state_name')->pluck('state_name', 'id');
        $cachefuel = $vehicles_fuel_other;

        return view('frontend.auto', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'states', 'cachefuel'));
    }
    /// radios with any qury  method for auto page  start here  
    // public function auto(Request $request, $param = null)
    // {
    //     // Handle clear cache requests
    //     $clear = $request->input('clear');
    //     if ($clear == 'flush') {
    //         Cookie::queue(Cookie::forget('searchData'));
    //         Cookie::queue(Cookie::forget('zipcode'));
    //         Cache::forget('vehicles_list');
    //         Cache::forget('vehicles_body_list');
    //         Cache::forget('vehicles_fuel_list');
    //         Cache::forget('price_range');
    //         Cache::forget('miles_range');
    //         return response()->json(['success' => 'clear']);
    //     }

    //     if ($clear == 'newCar') {
    //         Cookie::queue(Cookie::forget('searchData'));
    //         Cookie::queue(Cookie::forget('zipcode'));
    //         return response()->json(['success' => 'newcar']);
    //     }

    //     $zipCode = $request->input('zip');
    //     $radius = $request->input('radius', $zipCode ? 75 : null);
    //     $searchBody = $request->query('homeBodySearch');

    //     // Cache vehicle makes for 24 hours
    //     $vehicles = Cache::remember('vehicles_list', 60*24, function () {
    //         return VehicleMake::orderBy('make_name')->where('status', 1)->pluck('id', 'make_name');
    //     });

    //     // Cache distinct body types for 24 hours
    //     $vehicles_body = Cache::remember('vehicles_body_list', 60*24, function () {
    //         $inventory_obj = MainInventory::query();
    //         $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
    //         sort($vehicles_body);
    //         return $vehicles_body;
    //     });

    //     // Cache distinct fuel types for 24 hours
    //     $vehicles_fuel_other = Cache::remember('vehicles_fuel_list', 60*24, function () {
    //         $inventory_obj = MainInventory::query();
    //         $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();
    //         sort($vehicles_fuel_other);
    //         return $vehicles_fuel_other;
    //     });

    //     // Cache price and mileage ranges for 24 hours
    //     $price_range = Cache::remember('price_range', 60*24, function () {
    //         $inventory_obj = MainInventory::query();
    //         return [
    //             'max' => (int)$inventory_obj->where('price', '!=', 'N/A')->max('price'),
    //             'min' => (int)$inventory_obj->where('price', '!=', 'N/A')->min('price')
    //         ];
    //     });

    //     $miles_range = Cache::remember('miles_range', 60*24, function () {
    //         $inventory_obj = MainInventory::query();
    //         return [
    //             'max' => $inventory_obj->where('miles', '!=', 'N/A')->max('miles'),
    //             'min' => $inventory_obj->where('miles', '!=', 'N/A')->min('miles')
    //         ];
    //     });

    //     $price_max = $price_range['max'];
    //     $price_min = $price_range['min'];
    //     $miles_max = $miles_range['max'];
    //     $miles_min = $miles_range['min'];

    //     $global_make = '';
    //     if ($request->ajax()) {
    //         // Save user tracking data
    //         $currentUrl = $request->requestURL;
    //         $urlComponents = parse_url($currentUrl);
    //         $queryString = $urlComponents['query'] ?? '';
    //         $type = $queryString ? 'Searched' : 'Used';
    //         parse_str($queryString, $queryParams);

    //         $homeInventorySearch = $queryParams['homeBodySearch'] ?? null;
    //         $homeMakeSearch = $queryParams['homeMakeSearch'] ?? null;
    //         $homeBestMakeSearch = $queryParams['makeTypeSearch'] ?? null;
    //         $homeModelSearch = $queryParams['homeModelSearch'] ?? null;
    //         $dealer_id = $queryParams['dealer_id'] ?? null;

    //         $ip_address = $request->ip();
    //         $user_id = Auth::id() ?? null;
    //         $image = 'uploads/NotFound.png';
    //         $date = today();

    //         $existingRecord = UserTrack::where([
    //             'ip_address' => $ip_address,
    //             'type' => $type,
    //             'title' => $homeInventorySearch,
    //         ])
    //             ->whereDate('created_at', $date)
    //             ->first(['ip_address', 'type', 'title', 'created_at']);

    //         if (!$existingRecord) {
    //             $history_saved = new UserTrack();
    //             $history_saved->type = $type;
    //             $history_saved->links = $currentUrl;
    //             $history_saved->title = $homeInventorySearch . ' ' . $homeMakeSearch . ' ' . $homeModelSearch . ' ' . $homeBestMakeSearch;
    //             $history_saved->image = $image;
    //             $history_saved->ip_address = $ip_address;
    //             $history_saved->user_id = $user_id;
    //             $history_saved->save();
    //         }

    //         // Generate cache key based on request parameters
    //         $cacheKey = 'inventory_' . md5(json_encode($request->all()) . $dealer_id);

    //         // Initiate main inventory query with eager loading
    //         $mainInventory = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'inventory_status')
    //             ->with([
    //                 'dealer' => function ($query) {
    //                     $query->select('id', 'dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id');
    //                 },
    //                 'additionalInventory' => function ($query) {
    //                     $query->select('id', 'main_inventory_id', 'local_img_url');
    //                 },
    //                 'mainPriceHistory' => function ($query) {
    //                     $query->select('id', 'main_inventory_id', 'change_amount');
    //                 }
    //             ])->where(function ($query) {
    //                 $query->where('inventory_status', '!=', 'Sold')
    //                     ->orWhereNull('inventory_status'); // Include NULL values
    //             });

    //         // Apply filters
    //         $query = $this->inventoryService->getItemByFilter($request, $dealer_id, $mainInventory);

    //         // Parse URL parameters for dealer city/state filtering
    //         $urlData = parse_url($request->input('requestURL'));
    //         if (isset($urlData['query'])) {
    //             parse_str($urlData['query'], $queryParams);
    //             if (isset($queryParams['homeDealerCitySearch']) && isset($queryParams['homeDealerStateSearch'])) {
    //                 $city_data = $queryParams['homeDealerCitySearch'];
    //                 $state_data = $queryParams['homeDealerStateSearch'];

    //                 if (!empty($city_data)) {
    //                     $query->whereHas('dealer', function ($q) use ($city_data) {
    //                         $q->where('city', 'like', '%' . $city_data . '%');
    //                     });
    //                 }

    //                 if (!empty($state_data)) {
    //                     $query->whereHas('dealer', function ($q) use ($state_data) {
    //                         $q->where('state', 'like', '%' . $state_data . '%');
    //                     });
    //                 }
    //             }
    //         }

    //         // Extract more parameters from URL
    //         $urlComponents = parse_url($request->requestURL);
    //         $queryParams = [];
    //         if (isset($urlComponents['query'])) {
    //             parse_str($urlComponents['query'], $queryParams);
    //         }

    //         $homeBodySearch = $queryParams['homeBodySearch'] ?? null;
    //         $make = $queryParams['make'] ?? null;
    //         $model = $queryParams['model'] ?? null;
    //         $location = $queryParams['location'] ?? null;
    //         $zipCode = $queryParams['zip'] ?? null;
    //         $zip_radios = $queryParams['radius'] ?? null;
    //         $mobileLocation = $request->input('mobilelocation') ?? null;
    //         $webLocation = $request->input('weblocationNewInput') ?? null;
    //         $location = $webLocation ?? $mobileLocation ?? null;

    //         if ($location !== null) {
    //             queueZipCodeCookie($location);
    //         } else {
    //             Cookie::queue(Cookie::forget('zipcode'));
    //         }

    //         if ($make != null) {
    //             $query->where('make', $make);
    //             $global_make = $make;
    //         }

    //         if ($model != null) {
    //             $query->where('model', $model);
    //         }

    //         $query->whereNotNull('price')->where('price', '>', 1);
    //         $message = '';

    //         $zipCodeData = [
    //             'zip_code_data' => $zipCode,
    //             'zip_radios_data' => $zip_radios,
    //             'query_data' => $query,
    //         ];

    //         // Use Cache::remember for final inventory results
    //         // $cacheDuration = 30; // Cache for 30 minutes
    //         $cacheDuration = 1440; // Cache for 24 hours
    //         $cacheResult = null;

    //         if ($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide') {
    //             $cacheKey .= '_nationwide';
    //             $cacheResult = Cache::remember($cacheKey, $cacheDuration, function() use ($query) {
    //                 return [
    //                     'inventories' => $query->paginate(20),
    //                     'message' => null
    //                 ];
    //             });

    //             $inventories = $cacheResult['inventories'];
    //             $message = $cacheResult['message'];
    //         } else {
    //             $cacheKey .= '_' . ($zipCode ?? 'nozip') . '_' . ($zip_radios ?? 'noradius');

    //             // We can't cache paginated results directly, so we'll cache the query preparation
    //             $zipCodeResult = Cache::remember($cacheKey, $cacheDuration, function() use ($zipCodeData) {
    //                 return $this->getItemByDistance($zipCodeData['zip_code_data'], $zipCodeData['zip_radios_data'], $zipCodeData['query_data']);
    //             });

    //             $inventories = $zipCodeResult['inventories'];
    //             $message = $zipCodeResult['message'];
    //         }

    //         $current_page_count = $inventories->count();
    //         $total_count = number_format($inventories->total());
    //         $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;

    //         $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message'))->render();

    //         return response()->json([
    //             'view' => $view,
    //             'pagination' => $inventories->links()->toHtml(),
    //             'total_count' => $total_count,
    //             'message' => $message
    //         ]);
    //     }

    //     $make_data = $request->input('make');
    //     $states = LocationState::orderBy('state_name')->pluck('state_name', 'id');
    //     $cachefuel = $vehicles_fuel_other;

    //     return view('frontend.auto', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'states', 'cachefuel'));
    // }
    /// radios with any qury  method for auto page  end here  


/// old auto method for auto page  start here 

//     public function auto(Request $request, $param = null)
// {
//     // Handle clear cache requests
//     $clear = $request->input('clear');
//     if ($clear == 'flush') {
//         Cookie::queue(Cookie::forget('searchData'));
//         Cookie::queue(Cookie::forget('zipcode'));
//         Cache::forget('vehicles_list');
//         Cache::forget('vehicles_body_list');
//         Cache::forget('vehicles_fuel_list');
//         Cache::forget('price_range');
//         Cache::forget('miles_range');
//         return response()->json(['success' => 'clear']);
//     }

//     if ($clear == 'newCar') {
//         Cookie::queue(Cookie::forget('searchData'));
//         Cookie::queue(Cookie::forget('zipcode'));
//         return response()->json(['success' => 'newcar']);
//     }

//     $zipCode = $request->input('zip');
//     $radius = $request->input('radius', $zipCode ? 75 : null);
//     $searchBody = $request->query('homeBodySearch');

//     // Cache vehicle makes for 24 hours
//     $vehicles = Cache::remember('vehicles_list', 60*24, function () {
//         return VehicleMake::orderBy('make_name')->where('status', 1)->pluck('id', 'make_name');
//     });

//     // Cache distinct body types for 24 hours
//     $vehicles_body = Cache::remember('vehicles_body_list', 60*24, function () {
//         $inventory_obj = MainInventory::query();
//         $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
//         sort($vehicles_body);
//         return $vehicles_body;
//     });

//     // Cache distinct fuel types for 24 hours
//     $vehicles_fuel_other = Cache::remember('vehicles_fuel_list', 60*24, function () {
//         $inventory_obj = MainInventory::query();
//         $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();
//         sort($vehicles_fuel_other);
//         return $vehicles_fuel_other;
//     });

//     // Cache price and mileage ranges for 24 hours
//     $price_range = Cache::remember('price_range', 60*24, function () {
//         $inventory_obj = MainInventory::query();
//         return [
//             'max' => (int)$inventory_obj->where('price', '!=', 'N/A')->max('price'),
//             'min' => (int)$inventory_obj->where('price', '!=', 'N/A')->min('price')
//         ];
//     });

//     $miles_range = Cache::remember('miles_range', 60*24, function () {
//         $inventory_obj = MainInventory::query();
//         return [
//             'max' => $inventory_obj->where('miles', '!=', 'N/A')->max('miles'),
//             'min' => $inventory_obj->where('miles', '!=', 'N/A')->min('miles')
//         ];
//     });

//     $price_max = $price_range['max'];
//     $price_min = $price_range['min'];
//     $miles_max = $miles_range['max'];
//     $miles_min = $miles_range['min'];

//     $global_make = '';
//     if ($request->ajax()) {
//         // Save user tracking data
//         $currentUrl = $request->requestURL;
//         $urlComponents = parse_url($currentUrl);
//         $queryString = $urlComponents['query'] ?? '';
//         $type = $queryString ? 'Searched' : 'Used';
//         parse_str($queryString, $queryParams);

//         $homeInventorySearch = $queryParams['homeBodySearch'] ?? null;
//         $homeMakeSearch = $queryParams['homeMakeSearch'] ?? null;
//         $homeBestMakeSearch = $queryParams['makeTypeSearch'] ?? null;
//         $homeModelSearch = $queryParams['homeModelSearch'] ?? null;
//         $dealer_id = $queryParams['dealer_id'] ?? null;

//         $ip_address = $request->ip();
//         $user_id = Auth::id() ?? null;
//         $image = 'uploads/NotFound.png';
//         $date = today();

//         $existingRecord = UserTrack::where([
//             'ip_address' => $ip_address,
//             'type' => $type,
//             'title' => $homeInventorySearch,
//         ])
//             ->whereDate('created_at', $date)
//             ->first(['ip_address', 'type', 'title', 'created_at']);

//         if (!$existingRecord) {
//             $history_saved = new UserTrack();
//             $history_saved->type = $type;
//             $history_saved->links = $currentUrl;
//             $history_saved->title = $homeInventorySearch . ' ' . $homeMakeSearch . ' ' . $homeModelSearch . ' ' . $homeBestMakeSearch;
//             $history_saved->image = $image;
//             $history_saved->ip_address = $ip_address;
//             $history_saved->user_id = $user_id;
//             $history_saved->save();
//         }

//         // Generate cache key based on request parameters
//         $cacheKey = 'inventory_' . md5(json_encode($request->all()) . $dealer_id);

//         // Initiate main inventory query with eager loading
//         $mainInventory = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'inventory_status')
//             ->with([
//                 'dealer' => function ($query) {
//                     $query->select('id', 'dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id');
//                 },
//                 'additionalInventory' => function ($query) {
//                     $query->select('id', 'main_inventory_id', 'local_img_url');
//                 },
//                 'mainPriceHistory' => function ($query) {
//                     $query->select('id', 'main_inventory_id', 'change_amount');
//                 }
//             ]);

//         // Apply filters
//         $query = $this->inventoryService->getItemByFilter($request, $dealer_id, $mainInventory);

//         // Parse URL parameters for dealer city/state filtering
//         $urlData = parse_url($request->input('requestURL'));
//         if (isset($urlData['query'])) {
//             parse_str($urlData['query'], $queryParams);
//             if (isset($queryParams['homeDealerCitySearch']) && isset($queryParams['homeDealerStateSearch'])) {
//                 $city_data = $queryParams['homeDealerCitySearch'];
//                 $state_data = $queryParams['homeDealerStateSearch'];

//                 if (!empty($city_data)) {
//                     $query->whereHas('dealer', function ($q) use ($city_data) {
//                         $q->where('city', 'like', '%' . $city_data . '%');
//                     });
//                 }

//                 if (!empty($state_data)) {
//                     $query->whereHas('dealer', function ($q) use ($state_data) {
//                         $q->where('state', 'like', '%' . $state_data . '%');
//                     });
//                 }
//             }
//         }

//         // Extract more parameters from URL
//         $urlComponents = parse_url($request->requestURL);
//         $queryParams = [];
//         if (isset($urlComponents['query'])) {
//             parse_str($urlComponents['query'], $queryParams);
//         }

//         $homeBodySearch = $queryParams['homeBodySearch'] ?? null;
//         $make = $queryParams['make'] ?? null;
//         $model = $queryParams['model'] ?? null;
//         $location = $queryParams['location'] ?? null;
//         $zipCode = $queryParams['zip'] ?? null;
//         $zip_radios = $queryParams['radius'] ?? null;
//         $mobileLocation = $request->input('mobilelocation') ?? null;
//         $webLocation = $request->input('weblocationNewInput') ?? null;
//         $location = $webLocation ?? $mobileLocation ?? null;

//         if ($location !== null) {
//             queueZipCodeCookie($location);
//         } else {
//             Cookie::queue(Cookie::forget('zipcode'));
//         }

//         if ($make != null) {
//             $query->where('make', $make);
//             $global_make = $make;
//         }

//         if ($model != null) {
//             $query->where('model', $model);
//         }

//         $query->whereNotNull('price')->where('price', '>', 1);
//         $message = '';

//         $zipCodeData = [
//             'zip_code_data' => $zipCode,
//             'zip_radios_data' => $zip_radios,
//             'query_data' => $query,
//         ];

//         // Use Cache::remember for final inventory results
//         $cacheDuration = 30; // Cache for 30 minutes
//         $cacheResult = null;

//         if ($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide') {
//             $cacheKey .= '_nationwide';
//             $cacheResult = Cache::remember($cacheKey, $cacheDuration, function() use ($query) {
//                 return [
//                     'inventories' => $query->paginate(20),
//                     'message' => null
//                 ];
//             });

//             $inventories = $cacheResult['inventories'];
//             $message = $cacheResult['message'];
//         } else {
//             $cacheKey .= '_' . ($zipCode ?? 'nozip') . '_' . ($zip_radios ?? 'noradius');

//             // We can't cache paginated results directly, so we'll cache the query preparation
//             $zipCodeResult = Cache::remember($cacheKey, $cacheDuration, function() use ($zipCodeData) {
//                 return $this->getItemByDistance($zipCodeData['zip_code_data'], $zipCodeData['zip_radios_data'], $zipCodeData['query_data']);
//             });

//             $inventories = $zipCodeResult['inventories'];
//             $message = $zipCodeResult['message'];
//         }

//         $current_page_count = $inventories->count();
//         $total_count = number_format($inventories->total());
//         $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;

//         $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message'))->render();

//         return response()->json([
//             'view' => $view,
//             'pagination' => $inventories->links()->toHtml(),
//             'total_count' => $total_count,
//             'message' => $message
//         ]);
//     }

//     $make_data = $request->input('make');
//     $states = LocationState::orderBy('state_name')->pluck('state_name', 'id');
//     $cachefuel = $vehicles_fuel_other;

//     return view('frontend.auto', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'states', 'cachefuel'));
// }
/// old auto method for auto page end here   

    // public function auto(Request $request, $param = null)
    // {
    //     // dd(request()->cookie('zipcode'));
    //     $clear = $request->input('clear');
    //     if ($clear == 'flush') {

    //         Cookie::queue(Cookie::forget('searchData'));
    //         Cookie::queue(Cookie::forget('zipcode'));
    //         return response()->json(['success' => 'clear']);
    //     }

    //     if ($clear == 'newCar') {
    //         Cookie::queue(Cookie::forget('searchData'));
    //         Cookie::queue(Cookie::forget('zipcode'));
    //         return response()->json(['success' => 'newcar']);
    //     }

    //     $zipCode = $request->input('zip');
    //     $radius = $request->input('radius', $zipCode ? 75 : null);
    //     // $radius = $request->input('radius', 75); // Default to 75 miles

    //     // if (empty($request->zip)) {
    //     //     Cookie::queue(Cookie::forget('zipcode'));
    //     // }


    //     $searchBody = $request->query('homeBodySearch');
    //     // $inventores = Inventory::all();

    //     $vehicles = VehicleMake::orderBy('make_name')->where('status',1)->pluck('id', 'make_name');

    //     $inventory_obj = MainInventory::query();
    //     $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
    //     $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();

    //     sort($vehicles_fuel_other);
    //     sort($vehicles_body);

    //     $price_max = (int)$inventory_obj->where('price', '!=', 'N/A')->max('price');
    //     $price_min = (int)$inventory_obj->where('price', '!=', 'N/A')->min('price');
    //     $miles_max = $inventory_obj->where('miles', '!=', 'N/A')->max('miles');
    //     $miles_min = $inventory_obj->where('miles', '!=', 'N/A')->min('miles');

    //     $global_make = '';
    //     if ($request->ajax()) {
    //         // save this track list user satart here
    //         $currentUrl = $request->requestURL;

    //         $urlComponents = parse_url($currentUrl);
    //         $queryString = $urlComponents['query'] ?? '';
    //         $type = $queryString ? 'Searched' : 'Used';
    //         parse_str($queryString, $queryParams);
    //         $homeInventorySearch = $queryParams['homeBodySearch'] ?? null;
    //         $homeMakeSearch = $queryParams['homeMakeSearch'] ?? null;
    //         $homeBestMakeSearch = $queryParams['makeTypeSearch'] ?? null;
    //         $homeModelSearch = $queryParams['homeModelSearch'] ?? null;
    //         $dealer_id = $queryParams['dealer_id'] ?? null;
    //         $ip_address =  $request->ip();
    //         $user_id = Auth::id() ?? null;
    //         $image = 'uploads/NotFound.png';
    //         $date = today();
    //         // $existingRecord = UserTrack::where([
    //         //     'ip_address' => $ip_address,
    //         //     'type' => $type,
    //         //     'title' => $homeInventorySearch,
    //         // ])->whereDate('created_at', $date)->exists();
    //         $existingRecord = UserTrack::where([
    //             'ip_address' => $ip_address,
    //             'type' => $type,
    //             'title' => $homeInventorySearch,
    //         ])
    //             ->whereDate('created_at', $date)
    //             ->first(['ip_address', 'type', 'title', 'created_at']);
    //         if (!$existingRecord) {
    //             $history_saved = new UserTrack();
    //             $history_saved->type = $type;
    //             $history_saved->links = $currentUrl;
    //             $history_saved->title = $homeInventorySearch . ' ' . $homeMakeSearch . ' ' . $homeModelSearch . ' ' . $homeBestMakeSearch;
    //             $history_saved->image = $image;
    //             $history_saved->ip_address = $ip_address;
    //             $history_saved->user_id = $user_id;
    //             $history_saved->save();
    //         }

    //         $mainInventory = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details','inventory_status')
    //         ->with([
    //             'dealer' => function ($query) {
    //                 $query->select('id', 'dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id');
    //             },
    //             'additionalInventory' => function ($query) {
    //                 $query->select('id', 'main_inventory_id', 'local_img_url');
    //             },
    //             'mainPriceHistory' => function ($query) {
    //                 $query->select('id', 'main_inventory_id', 'change_amount');
    //             }
    //         ]);

    //         $query = $this->inventoryService->getItemByFilter($request, $dealer_id, $mainInventory);

    //         // Parse the URL from the request
    //         $urlData = parse_url($request->input('requestURL'));
    //         if (isset($urlData['query'])) {
    //             // Parse the query string into an associative array
    //             parse_str($urlData['query'], $queryParams);
    //             // Check if 'homeDealerCitySearch' exists in the parsed query parameters
    //             if (isset($queryParams['homeDealerCitySearch']) && isset($queryParams['homeDealerStateSearch'])) {
    //                 $city_data = $queryParams['homeDealerCitySearch'];
    //                 $state_data = $queryParams['homeDealerStateSearch'];
    //                 // Start building the query
    //                 // $query = Inventory::with('dealer');
    //                 $query = MainInventory::with('dealer', 'additionalInventory');

    //                 // Apply the city filter if provided
    //                 if (!empty($city_data)) {
    //                     $query->whereHas('dealer', function ($q) use ($city_data) {
    //                         $q->where('city', 'like', '%' . $city_data . '%');
    //                     });
    //                 }
    //                 // Apply the state filter if provided
    //                 if (!empty($state_data)) {
    //                     $query->whereHas('dealer', function ($q) use ($state_data) {
    //                         $q->where('state', 'like', '%' . $state_data . '%');
    //                     });
    //                 }
    //             }
    //         }

    //         $urlComponents = parse_url($request->requestURL);
    //         $queryParams = [];
    //         if (isset($urlComponents['query'])) {
    //             parse_str($urlComponents['query'], $queryParams);
    //         }
    //         // Extract the 'make' parameter
    //         $homeBodySearch = $queryParams['homeBodySearch'] ?? null;
    //         $make = $queryParams['make'] ?? null;
    //         $model = $queryParams['model'] ?? null;
    //         $location = $queryParams['location'] ?? null;
    //         $zipCode = $queryParams['zip'] ?? null;
    //         $zip_radios = $queryParams['radius'] ?? null;
    //         $mobileLocation = $request->input('mobilelocation') ?? null;
    //         $webLocation = $request->input('weblocationNewInput') ?? null;
    //         $location = $webLocation ??  $mobileLocation ?? null;
    //         //dd($location);

    //         if ($location !== null) {
    //             queueZipCodeCookie($location);
    //         } else {
    //             Cookie::queue(Cookie::forget('zipcode'));
    //         }

    //         if ($make != null) {
    //             $query->where('make', $make);
    //             $global_make = $make;
    //         }
    //         if ($model != null) {
    //             $query->where('model', $model);
    //         }

    //         $query->whereNotNull('price')->where('price', '>', 1);
    //         // $query->where('price', '>', '1');
    //         $message = ''; // Initialize message variable

    //         $zip_location_data = null;

    //         $query->where('price', '>', '1');
    //         $zipCodeData = [
    //             'zip_code_data' => $zipCode,
    //             'zip_radios_data' => $zip_radios,
    //             'query_data' => $query,
    //         ];

    //         if($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide'){
    //             $message = null;
    //             $inventories = $query->paginate(20);

    //         }else{
    //             $result = $this->getItemByDistance($zipCodeData['zip_code_data'], $zipCodeData['zip_radios_data'], $zipCodeData['query_data']);
    //             $inventories = $result['inventories'];
    //             $message = $result['message'];
    //         }

    //         $current_page_count = $inventories->count();

    //         $total_count = number_format($inventories->total());
    //         $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;
    //         // inventory numberf calculation end  here
    //         $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message'))->render();
    //         return response()->json(['view' => $view, 'pagination' => $inventories->links()->toHtml(), 'total_count' => $total_count, 'message' => $message]);
    //     }



    //     $make_data = $request->input('make');
    //     $states = LocationState::orderBy('state_name')->pluck('state_name', 'id');
    //     // $cachefuel = MainInventory::distinct()->pluck('fuel')->toArray();

    //     // $cachefuel = MainInventory::distinct()->pluck('fuel')->toArray();
    //     $cachefuel = $vehicles_fuel_other;
    //     return view('frontend.auto', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'states', 'cachefuel'));
    //     // return view('frontend.auto', compact('vehicles', 'vehicles_body', 'inventores', 'searchBody', 'vehicles_fuel_other', 'make_data', 'states','cachefuel'));
    // }

    public function newAuto(Request $request, $param = null)
    {

        $clear = $request->input('clear');
        if ($clear == 'flush') {
            Cookie::queue(Cookie::forget('searchData'));
            Cookie::queue(Cookie::forget('zipcode'));
            return response()->json(['success' => 'clear']);
        }
        if ($clear == 'newCar') {

            Cookie::queue(Cookie::forget('searchData'));
            Cookie::queue(Cookie::forget('zipcode'));
            return response()->json(['success' => 'newcar']);
        }

        // $inventories = collect();
        $searchBody = $request->query('homeBodySearch');
        $inventores = Inventory::all();
        $vehicles_obj = VehicleMake::query();
        $vehicles = $vehicles_obj->where('status', 1)->get();
        $inventory_obj = Inventory::query();
        $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
        $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();
        sort($vehicles_fuel_other);
        sort($vehicles_body);
        $price_max = $inventory_obj->max('price');
        $price_min = $inventory_obj->min('price');
        $miles_max = $inventory_obj->max('miles');
        $miles_min = $inventory_obj->min('miles');
        $global_make = '';
        if ($request->ajax()) {
            // save this track list user satart here
            $currentUrl = $request->requestURL;
            $urlComponents = parse_url($currentUrl);
            $queryString = $urlComponents['query'] ?? '';
            $type = $queryString ? 'Searched' : 'Used';
            parse_str($queryString, $queryParams);
            $homeInventorySearch = $queryParams['homeBodySearch'] ?? null;
            $homeMakeSearch = $queryParams['homeMakeSearch'] ?? null;
            $homeBestMakeSearch = $queryParams['makeTypeSearch'] ?? null;
            $homeModelSearch = $queryParams['homeModelSearch'] ?? null;
            $dealer_id = $queryParams['dealer_id'] ?? null;

            $ip_address =  $request->ip();
            $user_id = Auth::id() ?? null;
            $image = 'uploads/NotFound.png';
            $date = today();
            $existingRecord = UserTrack::where([
                'ip_address' => $ip_address,
                'type' => $type,
                'title' => $homeInventorySearch,
            ])->whereDate('created_at', $date)->exists();
            if (!$existingRecord) {
                $history_saved = new UserTrack();
                $history_saved->type = $type;
                $history_saved->links = $currentUrl;
                $history_saved->title = $homeInventorySearch . ' ' . $homeMakeSearch . ' ' . $homeModelSearch . ' ' . $homeBestMakeSearch;
                $history_saved->image = $image;
                $history_saved->ip_address = $ip_address;
                $history_saved->user_id = $user_id;
                $history_saved->save();
            }
            $query = $this->inventoryService->getItemByFilter($request, $dealer_id);

            // Parse the URL from the request
            $urlData = parse_url($request->input('requestURL'));

            if (isset($urlData['query'])) {
                // Parse the query string into an associative array
                parse_str($urlData['query'], $queryParams);

                // Check if 'homeDealerCitySearch' exists in the parsed query parameters
                if (isset($queryParams['homeDealerCitySearch']) && isset($queryParams['homeDealerStateSearch'])) {
                    $city_data = $queryParams['homeDealerCitySearch'];
                    $state_data = $queryParams['homeDealerStateSearch'];

                    // Start building the query
                    $query = Inventory::with('dealer');

                    // Apply the city filter if provided
                    if (!empty($city_data)) {
                        $query->whereHas('dealer', function ($q) use ($city_data) {
                            $q->where('city', 'like', '%' . $city_data . '%');
                        });
                    }

                    // Apply the state filter if provided
                    if (!empty($state_data)) {
                        $query->whereHas('dealer', function ($q) use ($state_data) {
                            $q->where('state', 'like', '%' . $state_data . '%');
                        });
                    }
                }
            }

            $urlComponents = parse_url($request->requestURL);
            $queryParams = [];
            if (isset($urlComponents['query'])) {
                parse_str($urlComponents['query'], $queryParams);
            }

            // Extract the 'make' parameter
            $homeBodySearch = $queryParams['homeBodySearch'] ?? null;
            $make = $queryParams['make'] ?? null;
            $model = $queryParams['model'] ?? null;
            $location = $queryParams['location'] ?? null;
            $zipCode = $queryParams['zip'] ?? null;
            $zip_radios = $queryParams['radius'] ?? null;
            $mobileLocation = $request->input('mobilelocation') ?? null;
            $webLocation = $request->input('weblocationNewInput') ?? null;
            $location = $webLocation ??  $mobileLocation ?? null;

            if ($location !== null) {
                queueZipCodeCookie($location);
            } else {
                Cookie::queue(Cookie::forget('zipcode'));
            }

            if ($make != null) {
                $query->where('make', $make);
                $global_make = $make;
            }
            if ($model != null) {
                $query->where('model', $model);
            }


            $query->where('type', 'New');

            $message = ''; // Initialize the $message variable

            if ($zipCode != null) {
                try {

                    if ($zip_radios !== null) {
                        $countryCode = 'us';
                        $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
                        $response = file_get_contents($url);
                        $zip_location_data = json_decode($response, true);

                        if (isset($zip_location_data['results'][0]['geometry'])) {
                            $latitude = $zip_location_data['results'][0]['geometry']['lat'];
                            $longitude = $zip_location_data['results'][0]['geometry']['lng'];
                            $cityName = $zip_location_data['results'][0]['components']['city'] ?? '';

                            // Only check within the specified radius
                            $zipCodeQuery = clone $query;
                            $zipCodeQuery->selectRaw(
                                "*, (3959 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"
                            );
                            $zipCodeQuery->having('distance', '<=', $zip_radios);
                            $zipCodeQuery->orderBy('distance', 'asc');

                            $zipCodeInventories = $zipCodeQuery->get();

                            if (!$zipCodeInventories->isEmpty()) {
                                // Results found within the input radius
                                $inventories = $zipCodeQuery->paginate(20);

                                // Check if any result is exactly within the radius specified by user
                                $maxDistance = $zipCodeInventories->max('distance');
                                if ($maxDistance > $zip_radios) {
                                    $message = "
                                        <section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:3px\" class=\"sptb2\">
                                            <div style=\"border-radius:5px\" class=\"container bg-white p-5\">
                                                <div class=\"text-center\">
                                                    <h3 class=\"mb-2\">You searched {$zip_radios} miles.</h3>
                                                    <p class=\"mb-2\">Showing results within {$maxDistance} miles.</p>
                                                </div>
                                            </div>
                                        </section>";
                                }
                            } else {
                                // No results found within the specified radius
                                $inventories = $query->paginate(20);
                                $message = "
                                    <section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px\" class=\"sptb2\">
                                        <div style=\"border-radius:5px\" class=\"container bg-white p-5 match\">
                                            <div class=\"text-center\">
                                                <h3 class=\"mb-2\">No exact matches within {$zip_radios} miles.</h3>
                                                <p class=\"mb-2\">Modify your filters or click \"Save Search\" to be notified when more matches are available.</p>
                                                <a href=\"#\" class=\"mb-2 clearfilterAjax\" style=\"text-decoration:underline;font-weight:bold;font-size:15px\" id=\"clearfilterAjax\">Clear all filters.</a>
                                            </div>
                                        </div>
                                    </section>";
                            }
                        } else {
                            // Fallback if no location data is found
                            $inventories = $query->paginate(20);
                        }
                    } else {
                        $zipCodeQuery = clone $query;
                        $zipCodeQuery->where('zip_code', $zipCode);
                        $zipCodeInventories = $zipCodeQuery->get();

                        if ($zipCodeInventories->isEmpty()) {
                            // No exact matches found for ZIP code alone, so try increasing radii
                            $radiusOptions = [10, 25, 50, 100];
                            $foundInventories = false;
                            $lastRadiusChecked = null;

                            // Call external geolocation API to get latitude and longitude of the ZIP code
                            $countryCode = 'us';
                            $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
                            $response = file_get_contents($url);
                            $zip_location_data = json_decode($response, true);

                            if (isset($zip_location_data['results'][0]['geometry'])) {
                                $latitude = $zip_location_data['results'][0]['geometry']['lat'];
                                $longitude = $zip_location_data['results'][0]['geometry']['lng'];

                                // Iterate through each radius option
                                foreach ($radiusOptions as $radius) {
                                    $zipCodeQuery = clone $query;
                                    $zipCodeQuery->selectRaw(
                                        "*, (3959 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"
                                    );
                                    $zipCodeQuery->having('distance', '<=', $radius);
                                    $zipCodeQuery->orderBy('distance', 'asc');

                                    $zipCodeInventories = $zipCodeQuery->get();

                                    if (!$zipCodeInventories->isEmpty()) {
                                        $inventories = $zipCodeQuery->paginate(20);
                                        $foundInventories = true;
                                        break; // Exit loop if inventories are found within the current radius
                                    } else {
                                        // No results for this radius, so update lastRadiusChecked
                                        $lastRadiusChecked = $radius;
                                    }
                                }
                            }

                            // If no inventories found, show a message with the last radius checked
                            if (!$foundInventories) {
                                $inventories = $query->paginate(20);
                                $message = "
                                    <section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px\" class=\"sptb2\">
                                        <div style=\"border-radius:5px\" class=\"container bg-white p-5 match\">
                                            <div class=\"text-center\">
                                                <h3 class=\"mb-2\">No exact matches within {$lastRadiusChecked} miles.</h3>
                                                <p class=\"mb-2\">Modify your filters or click \"Save Search\" to be notified when more matches are available.</p>
                                                <a href=\"#\" class=\"mb-2 clearfilterAjax\" style=\"text-decoration:underline;font-weight:bold;font-size:15px\" id=\"clearfilterAjax\">Clear all filters.</a>
                                            </div>
                                        </div>
                                    </section>";
                            }
                        } else {
                            // Exact matches found for the ZIP code alone
                            $inventories = $zipCodeQuery->paginate(20);
                        }
                    }
                } catch (\Exception $e) {
                    // Handle any exceptions that occur during the API call or query building
                    $message = 'An error occurred while processing your request. Please try again.';
                    $inventories = $query->paginate(20); // Fallback to default pagination
                }
            } else {
                // No zip code provided, paginate default query
                $inventories = $query->paginate(20);
            }
            // inventory numberf calculation start here
            $current_page_count = $inventories->count();
            $total_count = number_format($inventories->total());
            $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;
            // inventory numberf calculation end  here

            $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message'))->render();

            return response()->json(['view' => $view, 'pagination' => $inventories->links()->toHtml(), 'total_count' => $total_count, 'message' => $message]);
        }

        // $zip_data = $request->input('zip');
        $make_data = $request->input('make');
        $states = LocationState::orderBy('state_name')->pluck('state_name', 'id');
        // $cookie_zipcode = $request->query('ilocation') ?? null;
        return view('frontend.newAuto', compact('vehicles', 'vehicles_body', 'inventores', 'searchBody', 'vehicles_fuel_other', 'make_data', 'states'));
    }


    public function dealerinfo(Request $request, $stockId, $dealer_name, $id = null)
    {

        $clear = $request->input('clear');
        if ($clear == 'flush') {
            Cookie::queue(Cookie::forget('searchData'));
            Cookie::queue(Cookie::forget('zipcode'));
            return response()->json(['success' => 'clear']);
        }
        if ($clear == 'newCar') {
            Cookie::queue(Cookie::forget('searchData'));
            Cookie::queue(Cookie::forget('zipcode'));
            return response()->json(['success' => 'newcar']);
        }
        $searchBody = $request->query('homeBodySearch');
        // $inventores = Inventory::all();
        $vehicles_obj = VehicleMake::query();

        $vehicles = $vehicles_obj->where('status', 1)->get();
        $inventory_obj = MainInventory::query();
        $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
        $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();

        sort($vehicles_fuel_other);
        sort($vehicles_body);

        $price_max = $inventory_obj->max('price');
        $price_min = $inventory_obj->min('price');
        $miles_max = $inventory_obj->max('miles');
        $miles_min = $inventory_obj->min('miles');
        $global_make = '';

        if ($request->ajax()) {

            $currentUrl = $request->requestURL;
            $urlComponents = parse_url($currentUrl);
            $queryString = $urlComponents['query'] ?? '';
            $type = $queryString ? 'Searched' : 'Used';
            parse_str($queryString, $queryParams);
            $homeInventorySearch = $queryParams['homeBodySearch'] ?? null;
            $homeMakeSearch = $queryParams['homeMakeSearch'] ?? null;
            $homeBestMakeSearch = $queryParams['makeTypeSearch'] ?? null;
            $homeModelSearch = $queryParams['homeModelSearch'] ?? null;
            $dealer_id = $queryParams['dealer_id'] ?? null;

            $ip_address =  $request->ip();
            $user_id = Auth::id() ?? null;
            $image = 'uploads/NotFound.png';
            $date = today();
            $existingRecord = UserTrack::where([
                'ip_address' => $ip_address,
                'type' => $type,
                'title' => $homeInventorySearch,
            ])->whereDate('created_at', $date)->exists();


            if (!$existingRecord) {

                $history_saved = new UserTrack();
                $history_saved->type = $type;
                $history_saved->links = $currentUrl;
                $history_saved->title = $homeInventorySearch . ' ' . $homeMakeSearch . ' ' . $homeModelSearch . ' ' . $homeBestMakeSearch;
                $history_saved->image = $image;
                $history_saved->ip_address = $ip_address;
                $history_saved->user_id = $user_id;
                $history_saved->save();
            }

            $query = $this->inventoryService->getItemByFilter($request, $id);

            // Parse the URL from the request
            $urlData = parse_url($request->input('requestURL'));

            if (isset($urlData['query'])) {
                // Parse the query string into an associative array
                parse_str($urlData['query'], $queryParams);

                // Check if 'homeDealerCitySearch' exists in the parsed query parameters
                if (isset($queryParams['homeDealerCitySearch']) && isset($queryParams['homeDealerStateSearch'])) {
                    $city_data = $queryParams['homeDealerCitySearch'];
                    $state_data = $queryParams['homeDealerStateSearch'];

                    // Start building the query
                    // $query = Inventory::with('dealer');
                    $query = MainInventory::with('dealer', 'additionalInventory');

                    // Apply the city filter if provided
                    if (!empty($city_data)) {
                        $query->whereHas('dealer', function ($q) use ($city_data) {
                            $q->where('city', 'like', '%' . $city_data . '%');
                        });
                    }

                    // Apply the state filter if provided
                    if (!empty($state_data)) {
                        $query->whereHas('dealer', function ($q) use ($state_data) {
                            $q->where('state', 'like', '%' . $state_data . '%');
                        });
                    }
                }
            }

            $urlComponents = parse_url($request->requestURL);
            $queryParams = [];
            if (isset($urlComponents['query'])) {
                parse_str($urlComponents['query'], $queryParams);
            }

            // Extract the 'make' parameter
            $homeBodySearch = $queryParams['homeBodySearch'] ?? null;
            $make = $queryParams['make'] ?? null;
            $model = $queryParams['model'] ?? null;
            $location = $queryParams['location'] ?? null;
            $zipCode = $queryParams['zip'] ?? null;
            $zip_radios = $queryParams['radius'] ?? null;
            $mobileLocation = $request->input('mobilelocation') ?? null;
            $webLocation = $request->input('weblocationNewInput') ?? null;
            if ($webLocation != null) {
                queueZipCodeCookie($webLocation);
            } else {
                Cookie::queue(Cookie::forget('zipcode'));
            }

            if ($make != null) {
                $query->where('make', $make);
                $global_make = $make;
            }
            if ($model != null) {
                $query->where('model', $model);
            }

            // $query->where('deal_id', $id);

            $message = ''; // Ensure $message is always defined

            $zipCode = $zipCode ?? '';
            if ($zipCode != null) {
                try {
                    if ($zip_radios != null) {
                        $countryCode = 'us';
                        $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
                        $response = file_get_contents($url);
                        $zip_location_data = json_decode($response, true);

                        if (isset($zip_location_data['results'][0]['geometry'])) {
                            $latitude = $zip_location_data['results'][0]['geometry']['lat'];
                            $longitude = $zip_location_data['results'][0]['geometry']['lng'];
                            $cityName = $zip_location_data['results'][0]['components']['city'] ?? '';

                            $zipCodeQuery = clone $query;
                            $zipCodeQuery->selectRaw(
                                "*, (3959 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"
                            );

                            $zipCodeQuery->having('distance', '<=', $zip_radios);

                            // Ensure the result is paginated
                            $zipCodeInventories = $zipCodeQuery->get();

                            if ($zipCodeInventories->isEmpty()) {
                                $inventories = $query->paginate(20);
                                $message = '
                                <section style="padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px" class="sptb2">
                                    <div style="border-radius:5px" class="container bg-white p-5">
                                        <div class="text-center">
                                            <h3 class="mb-2">No exact matches within the radius.</h3>
                                            <p class="mb-2">Modify your filters or click "Save Search" to be notified when more matches are available.</p>
                                            <a href="#" class="mb-2 clearfilterAjax" style="text-decoration:underline;font-weight:bold;font-size:15px" id="clearfilterAjax">Clear all filters.</a>
                                        </div>
                                    </div>
                                </section>';
                            } else {
                                $inventories = $zipCodeQuery->paginate(20);
                            }
                        }
                    } else {
                        // When no radius filter is applied
                        $zipCodeQuery = clone $query;
                        $zipCodeQuery->where('zip_code', $zipCode);
                        $zipCodeInventories = $zipCodeQuery->get();

                        if ($zipCodeInventories->isEmpty()) {
                            $inventories = $query->paginate(20);
                            $message = '
                                <section style="padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px" class="sptb2">
                                    <div style="border-radius:5px" class="container bg-white p-5">
                                        <div class="text-center">
                                            <h3 class="mb-2">No exact matches.</h3>
                                            <p class="mb-2">Modify your filters or click "Save Search" to be notified when more matches are available.</p>
                                            <a href="#" class="mb-2 clearfilterAjax" style="text-decoration:underline;font-weight:bold;font-size:15px" id="clearfilterAjax">Clear all filters.</a>
                                        </div>
                                    </div>
                                </section>';
                        } else {
                            // Ensure the result is paginated
                            $inventories = $zipCodeQuery->paginate(20);
                        }
                    }
                } catch (\Exception $e) {
                    // Handle any exceptions that occur during the API call or query building
                    $message = 'An error occurred while processing your request. Please try again.';
                    $inventories = $query->paginate(20); // Fallback to default pagination
                }
            } else {
                // No zip code provided, paginate default query
                $inventories = $query->paginate(20);
            }

            // inventory numberf calculation start here
            $current_page_count = $inventories->count();
            $total_count = number_format($inventories->total());
            $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;
            // inventory numberf calculation end  here
            // dd($inventories, $total_count, $single_inventories_count,$message,$stockId,$dealer_name,$id);  1000
            $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message', 'stockId', 'dealer_name', 'id'))->render();
            return response()->json(['view' => $view, 'pagination' => $inventories->links()->toHtml(), 'total_count' => $total_count, 'message' => $message]);
        }

        $make_data = $request->input('make');
        $cachefuel = MainInventory::distinct()->pluck('fuel')->toArray();
        return view('frontend.dealer', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'stockId', 'dealer_name', 'id', 'cachefuel'));
    }



    public function autoDetails(Request $request, $vin, $slug)
    {
        // $inventory = Inventory::where('vin', $vin)->first();
        $inventory = MainInventory::with('additionalInventory', 'mainPriceHistory')->where('vin', $vin)->first();

        // if ($inventory->priceHistory->isNotEmpty()) {
        //     foreach ($inventory->priceHistory as $priceHistory) {
        //         echo $priceHistory->amount;
        //     }
        // } else {
        //     echo 'No price history available.';
        // }



        if (!$inventory) {
            return redirect()->route('car-not-found');
            // return redirect()->route('not-found');
        }

        $lowerPrice = (float)$inventory->price - 5000;
        $higherPrice = (float)$inventory->price + 5000;
        $other_vehicles = MainInventory::with([
            'additionalInventory' => function ($query) {
                $query->select('id', 'main_inventory_id', 'local_img_url'); // Selecting fields from additional_inventories
            }
        ])
            ->where('body_formated', $inventory->body_formated)
            ->where('id', '!=', $inventory->id)
            ->whereBetween('price', [$lowerPrice, $higherPrice])
            ->select('id', 'deal_id', 'title', 'type', 'transmission', 'price', 'payment_price', 'miles', 'price_rating')
            ->take(4)
            ->get();



        $lowerPrice = (float)$inventory->price - 5000;
        $higherPrice = (float)$inventory->price + 5000;

        // Fetch other vehicles and extract their VINs
        $other_vehicles = MainInventory::with([
            'additionalInventory' => function ($query) {
                $query->select('id', 'main_inventory_id', 'local_img_url');
            }
        ])
            ->where('body_formated', $inventory->body_formated)
            ->where('id', '!=', $inventory->id)
            ->where('deal_id', $inventory->deal_id)
            ->whereBetween('price', [$lowerPrice, $higherPrice])
            ->select('id', 'deal_id', 'title', 'type', 'transmission', 'price', 'payment_price', 'miles', 'price_rating', 'vin') // Ensure VIN is selected
            ->take(12)
            ->get();

        // Extract VIN numbers from other vehicles
        $excludedVins = $other_vehicles->pluck('vin')->toArray();

        // Fetch related vehicles excluding the ones with matching VINs
        $relateds = MainInventory::with([
            'additionalInventory' => function ($query) {
                $query->select('id', 'main_inventory_id', 'local_img_url'); // Select columns for additionalInventory
            },
            'dealer' => function ($query) {
                $query->select('id', 'name', 'city', 'state'); // Select columns for dealer
            }
        ])
            ->where('body_formated', $inventory->body_formated)
            ->where('id', '!=', $inventory->id)
            ->whereNotIn('vin', $excludedVins) // Exclude vehicles already in $other_vehicles
            ->whereBetween('price', [$lowerPrice, $higherPrice])
            ->select('id', 'deal_id', 'title', 'type', 'transmission', 'price', 'payment_price', 'year', 'make', 'model', 'miles', 'price_rating', 'vin') // Include VIN
            ->take(12)
            ->get();

        // dd($other_vehicles, $relateds, $excludedVins);
        $url_id = $inventory->year . '-' . $inventory->make . '-' . $inventory->model . '-in-' . $inventory->dealer->city . '-' . strtoupper($inventory->dealer->state);
        $shareUrl = url('/best-used-cars-for-sale/listing/' . $vin . '/' . $url_id);
        $title = urlencode($inventory->title);

        // Store all share links in one variable (associative array)
        $shareButtons = [
            'facebook'  => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($shareUrl) . "&quote=" . $url_id,
            'twitter'   => "https://x.com/intent/tweet?url=" . urlencode($shareUrl) . "&text=" . $url_id . "&via=your_twitter_handle",
            'linkedin'  => "https://www.linkedin.com/shareArticle?mini=true&url=" . urlencode($shareUrl) . "&title=" . $url_id,
            'whatsapp'  => "https://api.whatsapp.com/send?text=" . $title . "%20" . urlencode($shareUrl),
            'pinterest' => "https://pinterest.com/pin/create/button/?url=" . urlencode($shareUrl) . "&description=" . $url_id,
            'telegram'  => "https://t.me/share/url?url=" . urlencode($shareUrl) . "&text=" . $url_id
        ];

        // $url_id = $inventory->year . '-' . $inventory->make . '-' . $inventory->model . '-in-' . $inventory->dealer->city . '-' . strtoupper($inventory->dealer->state);
        // // https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}&quote={{ $data->title }}
        // // https://x.com/intent/tweet?url={{ url()->current() }}&text={{ $data->title }}&via=your_twitter_handle
        // $shareButtons = Share::page(url('/best-used-cars-for-sale' . '/' . 'listing' . '/' . $vin . '/' . $url_id), $inventory->title)
        //     ->facebook()
        //     ->twitter()
        //     ->linkedin()
        //     ->whatsapp()
        //     ->pinterest()
        //     ->telegram();


        //dd($shareButtons);
        // save this track list user satart here
        $image_obj =  $inventory->local_img_url;
        $image_splice = explode(',', $image_obj);
        // $image = str_replace(["[", "'"], "", $image_splice[0]);
        $image = str_replace(["[", "'", "]"], "", $image_splice[0]);
        $title =  $inventory->year . ' ' .  $inventory->make . ' ' .  $inventory->model;
        $inventory_id =  $inventory->id;
        $currentUrl =  $request->url();
        $type = 'Viewed';
        $ip_address =  $request->ip();
        $user_id = Auth::id() ?? null;
        $date = today();
        // $existingRecord = UserTrack::where([
        //     'ip_address' => $ip_address,
        //     'type' => $type,
        //     'title' => $title,
        // ])->whereDate('created_at', $date)->exists();

        $existingRecord = UserTrack::where([
            'ip_address' => $ip_address,
            'type' => $type,
            'title' => $title,
        ])->whereDate('created_at', $date)->first();


        if (!$existingRecord) {
            $history_saved = new UserTrack();
            $history_saved->type = $type;
            $history_saved->links = $currentUrl;
            $history_saved->title = $title;
            $history_saved->image = $image;
            $history_saved->ip_address = $ip_address;
            $history_saved->inventory_id = $inventory_id;
            $history_saved->user_id = $user_id;
            $history_saved->count = 1;
            $history_saved->save();
        } else {
            $existingRecord->increment('count');
        }

        // $history_saved = new UserTrack();
        // $history_saved->type = $type;
        // $history_saved->links = $currentUrl;
        // $history_saved->title = $title;
        // $history_saved->image = $image;
        // $history_saved->ip_address = $ip_address;
        // $history_saved->inventory_id = $inventory_id;
        // $history_saved->user_id = $user_id;
        // $history_saved->save();
        $stateData = strtoupper($inventory->dealer->state);
        $stateCity = ucwords($inventory->dealer->city);
        $locationStateData = LocationState::where('short_name', $stateData)->first();
        $locationCityData = LocationCity::where('city_name', $stateCity)->first();

        ($locationStateData) ? $stateRate = (float) $locationStateData->sales_tax : $stateRate = 0;
        ($locationCityData) ? $cityrate = (float) $locationCityData->sales_tax : $cityrate = 0;

        return response()->view('frontend.auto_details', compact('inventory', 'relateds', 'shareButtons', 'stateRate', 'cityrate','other_vehicles'));
    }

    public function contact()
    {
        return view('frontend.contact');
    }


    public function profile(Request $request)
    {
        $user = Auth::user();
        return view('frontend.Buyer.account', compact('user'));
    }

    public function cargarage(Request $request)
    {
        $user = Auth::user();
        return view('frontend.Buyer.cargarage', compact('user'));
    }

    public function cargarage_data(Request $request)
    {
        $datas = RequestInventory::where('status', 0)->orderby('id', 'desc')->get();
        $approves = RequestInventory::where('status', 1)->orderby('id', 'desc')->get();
        return view('frontend.Buyer.cargarage-show', compact('datas', 'approves'));
    }

    public function listing_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|string',
            'make' => 'required|string',
            'model' => 'required|string',
            'vin' => 'required|string',
            'price' => 'required|numeric|min:0',
            'exterior_color' => 'required|string',
            'transmission' => 'required|string',
            'miles' => 'required|integer|min:0',
            'fuel' => 'required|string',
            'drive_info' => 'required|string',
            'img_from_url' => 'required',
        ], [
            'year.required' => 'The year is required.',
            'make.required' => 'The make is required.',
            'model.required' => 'The model is required.',
            'vin.required' => 'The VIN is required.',
            'vin.unique' => 'The VIN must be unique.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'exterior_color.required' => 'The exterior color is required.',
            'transmission.required' => 'The transmission is required.',
            'miles.required' => 'The mileage is required.',
            'miles.integer' => 'The mileage must be a valid number.',
            'fuel.required' => 'The fuel type is required.',
            'drive_info.required' => 'The drivetrain information is required.',
            'img_from_url.required' => 'The image URL is required.',

        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $listing = new RequestInventory();
        $path = 'frontend/assets/images/listings/';
        $imageNames = [];

        if ($request->hasFile('img_from_url')) {
            $uploadedImages = $request->file('img_from_url');
            foreach ($uploadedImages as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('frontend/assets/images/listings/'), $imageName);
                $imageNames[] = $imageName;
            }

            if ($listing->img_from_url) {
                $oldImages = json_decode($listing->img_from_url, true);
                foreach ((array) $oldImages as $oldImage) {
                    $oldImagePath = public_path('frontend/assets/images/listings/') . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }

            $listing->img_from_url = json_encode($imageNames);
        } else {
            $listing->img_from_url = $listing->img_from_url;
        }


        // Update other fields
        $listing->year = $request->year;
        $listing->make = $request->make;
        $listing->model = $request->model;
        $listing->vin = $request->vin;
        $listing->price = $request->price;
        $listing->exterior_color = $request->exterior_color;
        $listing->transmission = $request->transmission;
        $listing->miles = $request->miles;
        $listing->type = 'used';
        $listing->user_id = Auth::id();
        $listing->fuel = $request->fuel;
        $listing->drive_info = $request->drive_info;
        $listing->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Listing Added Successfully'
        ]);
    }

    public function listing_add(Request $request)
    {

        // Validate the request
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required',
            'year' => 'required|string',
            'make' => 'required|string',
            'model' => 'required|string',
            'vin' => 'required|string|unique:request_inventories,vin',
            'price' => 'required|numeric|min:0',
            'exterior_color' => 'required|string',
            'transmission' => 'required|string',
            'miles' => 'required|integer|min:0',
            'fuel' => 'required|string',
            'drive_info' => 'required|string',
            'img_from_url.*' => 'image|mimes:jpeg,png,jpg|max:2048', // Validate each file
        ], [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Phone is required.',
            'year.required' => 'The year is required.',
            'make.required' => 'The make is required.',
            'model.required' => 'The model is required.',
            'vin.required' => 'The VIN is required.',
            'vin.unique' => 'The VIN must be unique.',
            'price.required' => 'The price is required.',
            'price.numeric' => 'The price must be a valid number.',
            'exterior_color.required' => 'The exterior color is required.',
            'transmission.required' => 'The transmission is required.',
            'miles.required' => 'The mileage is required.',
            'miles.integer' => 'The mileage must be a valid number.',
            'fuel.required' => 'The fuel type is required.',
            'drive_info.required' => 'The drivetrain information is required.',
            'img_from_url.*.image' => 'Each file must be an image.',
            'img_from_url.*.mimes' => 'Only jpeg, png, and jpg formats are allowed.',
            'img_from_url.*.max' => 'Each image must not exceed 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find or create a user
        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->first_name . ' ' . $request->last_name,
                'phone' => $request->phone,
            ]
        );

        // $userInfo = [
        //     'name' => $user->name,
        //     'id' => $user->id,
        //     'email' => $user->email,
        // ];

        // Create a new listing
        $listing = new RequestInventory();

        // Handle image uploads
        $imageNames = [];
        if ($request->hasFile('img_from_url')) {
            foreach ($request->file('img_from_url') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('frontend/assets/images/listings/'), $imageName);
                $imageNames[] = $imageName;
            }
            $listing->img_from_url = json_encode($imageNames);
        }

        // Assign listing fields
        $listing->year = $request->year;
        $listing->make = $request->make;
        $listing->model = $request->model;
        $listing->vin = $request->vin;
        $listing->price = $request->price;
        $listing->exterior_color = $request->exterior_color;
        $listing->transmission = $request->transmission;
        $listing->miles = $request->miles;
        $listing->type = 'used';
        $listing->user_id = $user->id;
        $listing->fuel = $request->fuel;
        $listing->drive_info = $request->drive_info;
        $listing->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Listing added successfully!',
        ]);
    }


    public function info()
    {

        $user_profile = User::find(Auth::id());
        return view('frontend.userdashboard.profile_info', compact('user_profile'));
    }

    public function profile_edit()
    {
        $user = Auth::user();
        return view('frontend.Buyer.account', compact('user'));
    }

    public function profile_favorite()
    {
        $favorites = Favourite::with('inventory')->where('user_id', Auth::id())->orderBy('id', 'desc')->get();
        return view('frontend.userdashboard.profile_favorite', compact('favorites'));
    }

    public function deleteFavorite(Request $request)
    {
        $favourite = Favourite::where('inventory_id', $request->inventory_id)->forceDelete();
        if ($favourite) {
            return response()->json([
                'action' => 'remove',
                'message' => 'Removed to favorites',
            ]);
        } else {
            $favourite_save = new Favourite();
            $favourite_save->inventory_id = $request->inventory_id;
            $favourite_save->user_id = Auth::id();
            $favourite_save->ip_address = $request->ip();
            $favourite_save->save();
            return response()->json([
                'action' => 'add',
                'message' => 'Added to favorites',
            ]);
        }
    }

    public function user_message()
    {
        $lead_messages = Message::with('user', 'lead')->where('sender_id', Auth::user()->id)->latest()
            ->get()->unique('lead_id');
        return view('frontend.Buyer.message', compact('lead_messages'));
    }


    public function messageCollect(Request $request)
    {
        // Retrieve messages based on lead_id
        $messages = Message::where('lead_id', $request->lead_id)->get();
        // Check if messages exist
        if ($messages) {
            // Loop through each message and mark as seen
            foreach ($messages as $message) {
                $message->is_seen = 1;
                $message->save(); // Save the changes
            }
        }

        // Return response in JSON format
        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }



    public function store(Request $request)
    {
        $request->validate([
            'fname' => 'required',
            'lname' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
        ]);

        $user = User::find(Auth::id());

        if ($request->hasFile('image') && isset($request->image)) {
            $path = 'frontend/assets/images/';
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Delete the old image if it exists
            if ($user->image != null) {
                $oldImagePath = public_path($path) . $user->image;
                if (file_exists($oldImagePath)) {
                    try {
                        unlink($oldImagePath);
                    } catch (\Exception $e) {
                        // Handle the unlinking error
                        // You can log the error or perform other actions as needed
                        // For now, just log the error message
                        error_log('Error deleting old image: ' . $e->getMessage());
                    }
                }
            }

            // Move the new image to the specified path
            $image->move(public_path($path), $imageName);

            // Update the link's image attribute with the new image name
            $user->image = $imageName;
        } else {
            // If no new image is uploaded, keep the existing image name
            $user->image = $user->image;
        }

        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->name = $request->fname . ' ' . $request->lname;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->city = $request->city;
        $user->zip = $request->zip;
        $user->country = $request->country;
        $user->facebook = $request->facebook;
        $user->google = $request->google;
        $user->twitter = $request->twitter;
        $user->pinterest = $request->pinterest;
        $user->about_me = $request->about_me;
        $user->save();
        return redirect()->back()->with('message', 'User Update Successfully ');
    }

    public function modelSearch(Request $request, $id)
    {
        // Cookie::queue(Cookie::forget('searchData'));
        $vehicleMake = VehicleMake::find($request->id);
        $vehicleModels = $vehicleMake->models()->select('id', 'model_name')->get();
        return response()->json($vehicleModels);
        // $vehiclemodel = VehicleModel::where('vehicle_make_id', $request->id)->pluck('model_name', 'id');
        // return response()->json($vehiclemodel);
        $vehicleModels = VehicleModel::where('vehicle_make_id', $request->id)->orderBy('model_name', 'asc')->pluck('model_name', 'id')->toArray();
        return response()->json($vehicleModels);
        // $vehicleModels = VehicleModel::where('vehicle_make_id', $request->id)->pluck('model_name', 'id')->toJson();
        // return $vehicleModels;
    }

    public function bodySearch(Request $request)
    {
        // $vehicleMakeID = VehicleMake::where('make_name',$request->id)->first()->id;
        $vehicleData = [];
        // $vehicles = Inventory::select('body_formated')->distinct()->pluck('body_formated');
        $vehicles = MainInventory::select('body_formated')->distinct()->pluck('body_formated');
        foreach ($vehicles as $vehicle) {
            ($vehicle == null) ? $vehicleData[] = 'Others' : $vehicleData[] = $vehicle;
        }
        asort($vehicleData);
        return response()->json($vehicleData);
    }

    public function contact_message(Request $request)
    {
        //    dd(session()->all());
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string',
            'message' => 'required|string',
            'mathcaptcha' => ['required', 'mathcaptcha'],

        ], [

            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'message.required' => 'Message is required',
            'mathcaptcha.required' => 'captcha is required.',
            'mathcaptcha.mathcaptcha' => 'Answer is incorrect.',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ]);
        }

        $contact = new Contact();
        $contact->name = $request->name;
        $contact->email = $request->email;
        $contact->message = $request->message;
        $contact->save();


        $notification_title = 'Contact Message';
        $notification_message = 'Contact message come from website';
        $notification_call_back_url = route('admin.contact.show');
        $notification_category = 'communication';
        $notificatioN_auth_id = '0';


        $this->saveNewNotification($notification_title, $notification_message, $notification_call_back_url, $notificatioN_auth_id, $notification_category);


        $data = [
            'email' => $request->email,
            'message' => $request->message,
            'name' => $request->name

        ];
        Mail::to($request->email)->send(new ContactMail($data));

        return response()->json([
            'status' => 'success',
            'message' => 'Message sent Successfully'
        ]);
    }




    // public function favourite(Request $request)
    // {
    //     if (Auth::check()) {
    //         $cookieFavorites = json_decode(Cookie::get('favourite', '[]'), true);
    //         if (!empty($cookieFavorites)) {
    //             $favoriteIds = collect($cookieFavorites)->pluck('id')->toArray();
    //             $favorites = Inventory::whereIn('id', $favoriteIds)
    //                 ->orderByRaw('FIELD(id, ' . implode(',', $favoriteIds) . ') DESC')
    //                 ->paginate(12);
    //         } else {
    //             Favourite::where('user_id', Auth::id())->delete();
    //             $favorites = new LengthAwarePaginator([], 0, 12, 1, ['path' => url()->current()]);
    //         }
    //     } else {
    //         $cookieFavorites = json_decode(Cookie::get('favourite', '[]'), true);
    //         if (!empty($cookieFavorites)) {
    //             $favoriteIds = collect($cookieFavorites)->pluck('id')->toArray();
    //             $favorites = Inventory::whereIn('id', $favoriteIds)
    //                 ->orderByRaw('FIELD(id, ' . implode(',', $favoriteIds) . ') DESC')
    //                 ->paginate(12);
    //         } else {
    //             $favorites = new LengthAwarePaginator([], 0, 12, 1, ['path' => url()->current()]);
    //         }
    //     }
    //     return view('frontend.favourite.index', compact('favorites'));
    // }


    public function favourite(Request $request)
    {
        if (Auth::check()) {

            $sessionFavourites = session()->get('favourite', []);
            if (!empty($sessionFavourites)) {
                $favoriteIds = collect($sessionFavourites)->pluck('id')->toArray();
                // $favorites = Inventory::whereIn('id', $favoriteIds)
                $favorites = MainInventory::whereIn('id', $favoriteIds)
                    ->orderByRaw('FIELD(id, ' . implode(',', $favoriteIds) . ') DESC')
                    ->paginate(12);
            } else {
                Favourite::where('user_id', Auth::id())->delete();
                $favorites = new LengthAwarePaginator([], 0, 12, 1, ['path' => url()->current()]);
            }
        } else {
            $sessionFavourites = session()->get('favourite', []);
            if (!empty($sessionFavourites)) {
                $favoriteIds = collect($sessionFavourites)->pluck('id')->toArray();
                // $favorites = Inventory::whereIn('id', $favoriteIds)
                $favorites = MainInventory::whereIn('id', $favoriteIds)
                    ->orderByRaw('FIELD(id, ' . implode(',', $favoriteIds) . ') DESC')
                    ->paginate(12);
            } else {
                $favorites = new LengthAwarePaginator([], 0, 12, 1, ['path' => url()->current()]);
            }
        }
        return view('frontend.favourite.index', compact('favorites'));
    }

    // about page function

    public function about()
    {
        return view('frontend.about.index');
    }

    public function faq()
    {
        $faqs = Faq::where('status', '1')->where('type', 'faq')->get();
        return view('frontend.faq.index', compact('faqs'));
    }

    public function termsCondition()
    {
        $termsconditions = Page::where('pages', 'terms')->select('id', 'title', 'slug', 'description', 'status')->first();
        return view('frontend.privacy.index', compact('termsconditions'));
        // $termsconditions = TermsCondition::orderBy('id', 'desc')->get();
        // $termsconditions = TermsCondition::orderBy('id', 'desc')->first();
    }

    public function privacyPolicy()
    {
        $termsconditions = Page::where('pages', 'privacy')->select('id', 'title', 'slug', 'description', 'status')->first();
        return view('frontend.privacy.index', compact('termsconditions'));
    }

    public function details($slug)
    {
        try {
            $data = News::where('slug', $slug)->firstOrFail();

            $news = News::whereNot('slug', $slug)->orderBy('created_at', 'desc')->get();
            return view('frontend.news.news_details', compact('data', 'news'));
        } catch (ModelNotFoundException $e) {
            abort(404, 'News article not found.');
        }
    }


    public function Rdetails($id)
    {

        $data = Review::find($id);
        $news = Review::orderBy('created_at', 'desc')->get();
        return view('frontend.news.review_details', compact('data', 'news'));
    }



    public function showFindDealership(Request $request)
    {
        // $query = User::with(['roles', 'inventories'])
        $query = User::with(['roles', 'mainInventories'])
            ->where('status', 1)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'dealer');
            })
            ->select('id', 'dealer_id', 'name', 'phone', 'address', 'city', 'state', 'zip')
            ->distinct()
            ->orderByRaw("
            CASE
                WHEN name REGEXP '^[0-9]' THEN 1
                ELSE 0
            END,
            name ASC
        "); // Conditional sorting for numeric names

        // dd($query->get()[0]);
        if ($request->ajax()) {
            $target_city = $request->city;
            $target_state = $request->state;
            $target_name = $request->name;
            // Apply filters for AJAX requests

            if ($target_city != null) {
                $query->where('city', $target_city);
                // $location_city = LocationCity::find($target_city);

                // $zipCode = '77022';
                // $radius = 50; // Default radius (25 miles)
                // $countryCode = 'us';
                // $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
                // $response = file_get_contents($url);
                // $zip_location_data = json_decode($response, true);

                // if (isset($zip_location_data['results'][0]['geometry'])) {
                //     $latitude = $zip_location_data['results'][0]['geometry']['lat'];
                //     $longitude = $zip_location_data['results'][0]['geometry']['lng'];

                //     $query->selectRaw(
                //         "id, dealer_id, name, phone, address,(3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                //         [$latitude, $longitude, $latitude]
                //     )
                //     ->having('distance', '<=', $radius)
                //     ->orderBy('distance', 'asc');
                // }


            }
            if ($target_name != null) {
                $query->Where('name', 'LIKE', "%{$target_name}%");
            }
            if ($target_state != null) {
                $query->Where('state', $target_state);
            }

            $dealers = $query->paginate(20);
            $cities = LocationCity::where('status', 1)
                ->orderBy('city_name', 'asc')
                ->select('id', 'city_name')
                ->distinct()
                ->get();

            // $states = User::orderBy('state', 'asc')->whereNotNull('state')->select('id','state')->distinct()->get();

            $states = User::whereNotNull('state')  // Ensure the state is not null
                ->distinct()  // Make sure the state values are distinct
                ->orderBy('state', 'asc')  // Order the results alphabetically
                ->pluck('state');

            $full_state_name  = LocationState::whereIn('short_name', $states)->pluck('short_name', 'state_name');
            $state_names  = $full_state_name;

            $select_cities = User::whereNotNull('city')  // Ensure the state is not null
                ->distinct('city')  // Make sure the state values are distinct
                ->orderBy('city', 'asc')  // Order the results alphabetically
                ->where('state', $target_state)
                ->pluck('city')->toArray();

            // dd($select_cities);
            // Pagination values
            $current_page_count = $dealers->count();
            $total_count = number_format($dealers->total());
            $single_dealer_count = ($dealers->perPage() * ($dealers->currentPage() - 1)) + $current_page_count;

            $view = view('frontend.dealer.dealer_ajax', compact('dealers', 'total_count', 'single_dealer_count', 'cities', 'select_cities', 'state_names', 'target_city', 'target_state', 'target_name'))->render();

            return response()->json([
                'view' => $view,
                'pagination' => $dealers->links()->toHtml(),
                'total_count' => $total_count,
                'select_cities' => $select_cities,
            ]);
        }

        // Non-AJAX requests
        $dealers = $query->select('id', 'dealer_id', 'address', 'name', 'city', 'state', 'zip', 'phone', 'website', 'price');
        // $dealers = $query->get();

        $cities = LocationCity::where('status', 1)
            ->orderBy('city_name', 'asc')
            ->select('id', 'city_name')
            ->distinct()
            ->get();

        return view('frontend.dealer.index', compact('dealers', 'cities'));
    }


    // public function news()
    // {
    //     $all = News::orderByDesc('id')->where('status', '1')->get();
    //     $firstNews = News::orderByDesc('id')->where('status', '1')->first();
    //     $lastNews = News::orderByDesc('id')->where('status', '1')->first();
    //     $firstreviews = Blog::orderByDesc('id')->where('status', '1')->first();
    //     $lastreviews = Blog::orderBy('id')->where('status', '1')->first();
    //     // $firstreviews = Review::where('status', '1')->orderBy('created_at', 'desc')->first();
    //     // $lastreviews = Review::where('status', '1')->orderBy('created_at', 'asc')->first();

    //     $reviews = collect();

    //     // if (!empty($firstreviews) && !empty($lastreviews)) {
    //     //     $reviews = Review::where('status', '1')
    //     //         ->whereNotIn('id', [$firstreviews->id, $lastreviews->id])
    //     //         ->limit(6)
    //     //         ->get();
    //     // }
    //     if ($firstreviews) {
    //         $reviews = Blog::orderByDesc('id')->where('status', '1')
    //             ->whereNotIn('id', [$firstreviews->id]) // Use whereNotIn as a fallback
    //             ->limit(6)
    //             ->get();
    //     }

    //     $news = collect();
    //     if (!empty($firstNews) && !empty($lastNews)) {
    //         $news = News::orderByDesc('id')->where('status', '1')
    //             ->whereNotIn('id', [$firstNews->id, $lastNews->id])->limit(6)
    //             ->get();
    //     }

    //     $advices = News::orderByDesc('id')->where('status', '1')
    //         ->orderBy('created_at', 'asc')->limit(4)
    //         ->get();
    //     $videos = LatestVideo::where('status', 1)->orderby('id', 'desc')->limit('4')->get();
    //     // dd($videos);
    //     return view('frontend.news.news_page', compact('news', 'firstNews', 'lastNews', 'reviews', 'firstreviews', 'lastreviews', 'advices', 'videos', 'all'));
    // }

    public function news()  // optimized news
    {
        // Fetch all news records with status 1 in descending order
        $all = News::where('status', '1')->orderByDesc('id')->get();

        // Get first and last news directly from the collection
        $firstNews = $all->first();
        $lastNews = $all->last();

        // Fetch all blog records with status 1 in descending order
        $blogs = Blog::where('status', '1')->orderByDesc('id')->get();

        // Get first and last blog reviews
        $firstreviews = $blogs->first();
        $lastreviews = $blogs->sortBy('id')->first(); // Sorting by ID for ASC order

        // Get reviews excluding the first one (if it exists)
        $reviews = $blogs->whereNotIn('id', [$firstreviews->id ?? null])->take(6);

        $news = $all->whereNotIn('id', [$firstNews->id ?? null])->take(6);
        // // Get news excluding first and last if they exist
        // $news = $all->reject(fn($item) => $item->id === ($firstNews->id ?? null))->take(6);
        // $news = $all->whereNotIn('id', [$firstNews->id ?? null, $lastNews->id ?? null])->take(6);

        // Get advices, sorting by `created_at` in ascending order
        $advices = $all->sortBy('created_at')->take(4);

        // Fetch latest videos
        $videos = LatestVideo::where('status', 1)->orderByDesc('id')->limit(4)->get();

        return view('frontend.news.news_page', compact('news', 'firstNews', 'lastNews', 'reviews', 'firstreviews', 'lastreviews', 'advices', 'videos', 'all'));
    }



    public function tips()
    {
        $all = Tips::orderBy('created_at', 'desc')->where('status', '1')->get();
        $firstTips = Tips::where('status', '1')->orderBy('created_at', 'desc')->first();
        $lastTips = Tips::where('status', '1')->orderBy('created_at', 'asc')->first();
        $firstreviews = Blog::where('status', '1')->orderBy('created_at', 'desc')->first();
        $lastreviews = Blog::where('status', '1')->orderBy('created_at', 'asc')->first();
        // $firstreviews = Review::where('status', '1')->orderBy('created_at', 'desc')->first();
        // $lastreviews = Review::where('status', '1')->orderBy('created_at', 'asc')->first();

        $reviews = collect();

        // if (!empty($firstreviews) && !empty($lastreviews)) {
        //     $reviews = Review::where('status', '1')
        //         ->whereNotIn('id', [$firstreviews->id, $lastreviews->id])
        //         ->limit(6)
        //         ->get();
        // }
        if ($firstreviews) {
            $reviews = Blog::orderBy('created_at', 'desc')->where('status', '1')
                ->whereNotIn('id', [$firstreviews->id]) // Use whereNotIn as a fallback
                ->limit(6)
                ->get();
        }

        $tips = collect();
        if (!empty($firstTips) && !empty($lastTips)) {
            $tips = Tips::orderBy('created_at', 'desc')->where('status', '1')
                ->whereNotIn('id', [$lastTips->id])->limit(12)
                ->get();
        }

        $advices = News::where('status', '1')
            ->orderBy('created_at', 'asc')->limit(4)
            ->get();
        $videos = LatestVideo::where('status', 1)->orderby('id', 'desc')->limit('4')->get();
        // dd($videos);
        return view('frontend.news.tips_page', compact('tips', 'firstTips', 'lastTips', 'reviews', 'firstreviews', 'lastreviews', 'advices', 'videos', 'all'));
    }

    public function articles($slug)
    {
        $videos = LatestVideo::where('status', '1')->latest('created_at')->get();
        $review = Review::where('status', '1')->latest('created_at')->get();
        $news = News::where('status', '1')->latest('created_at')->get();

        $firstTip = Tips::where('status', '1')->latest('created_at')->first();
        $lastTip = Tips::where('status', '1')->oldest('created_at')->first();

        $tips = Tips::where('status', '1')
            ->where('id', '!=', optional($firstTip)->id) 
            ->latest('created_at')
            ->limit(6)
            ->get();

        if ($slug = 'videos') {
            $datas = LatestVideo::where('status', '1')->latest('created_at')->get();
        }

        if ($slug = 'review') {
            $datas = Review::where('status', '1')->latest('created_at')->get();
        }

        if ($slug = 'news') {
            $datas = News::where('status', '1')->latest('created_at')->get();
        }
        $slug_info = $slug;
        // // Determine data based on slug
        // $dataMap = [
        //     'videos'  => LatestVideo::class,
        //     'review'  => Review::class,
        //     'article' => News::class,
        // ];

        // $datas = isset($dataMap[$slug]) ? $dataMap[$slug]::where('status', '1')->latest('created_at')->get() : collect();

        return view('frontend.article', compact('videos', 'review', 'news', 'datas', 'slug', 'slug_info', 'firstTip', 'lastTip', 'tips'));
    }

    // public function articles($slug)
    // {
    //     $videos = LatestVideo::where('status', '1')->orderBy('created_at', 'desc')->get();
    //     $review = Review::where('status', '1')->orderBy('created_at', 'desc')->get();
    //     $news = News::where('status', '1')->orderBy('created_at', 'desc')->get();


    //     $firstTip = Tips::where('status', '1')->latest('created_at')->first();
    //     $lastTip = Tips::where('status', '1')->oldest('created_at')->first();
        
    //     $tips = Tips::where('status', '1')
    //         ->where('id', '!=', optional($firstTip)->id) // Use optional() to avoid errors if null
    //         ->latest('created_at')
    //         ->limit(6)
    //         ->get();


    //     $slug_info = $slug;
    //     if ($slug = 'videos') {
    //         $datas = LatestVideo::where('status', '1')->orderBy('created_at', 'desc')->get();
    //     }

    //     if ($slug = 'review') {
    //         $datas = Review::where('status', '1')->orderBy('created_at', 'desc')->get();
    //     }

    //     if ($slug = 'article') {
    //         $datas = News::where('status', '1')->orderBy('created_at', 'desc')->get();
    //     }
    //     // dd($firstTip,$lastTip,$tips);
    //     return view('frontend.article', compact('videos', 'review', 'news', 'datas', 'slug_info','firstTip', 'lastTip', 'tips'));
    // }

    public function reviews($slug)
    {
        $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
        $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
        $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


        $slug_info = $slug;

        switch ($slug) {
            case 'tools_&_expert_device':
                $review_id = 1;
                break;
            case 'car_buying_advice':
                $review_id = 2;
                break;
            case 'beyond_cars':
                $review_id = 3;
                break;
            default:
                $review_id = null; // Or some default value
                break;
        }

        // dd($review_id);
        $datas = Blog::orderByDesc('id')->where('type', $review_id)->where('status', '1')->get();

        $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
        $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
        $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

        return view('frontend.news.review_page', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    }


    public function vedioDetails($id)
    {
        // LatestVideo::where('id', $id)->increment('views');
        // LatestVideo::findOrfail($id)->increment('views');
        $vedio = LatestVideo::findOrfail($id);
        $all = News::where('status', '1')->get();

        $firstNews = News::where('status', '1')->orderBy('created_at', 'desc')->first();
        $lastNews = News::where('status', '1')->orderBy('created_at', 'asc')->first();
        $firstreviews = Review::where('status', '1')->orderBy('created_at', 'desc')->first();
        $lastreviews = Review::where('status', '1')->orderBy('created_at', 'asc')->first();

        $reviews = collect();

        if (!empty($firstreviews) && !empty($lastreviews)) {
            $reviews = Review::where('status', '1')
                ->whereNotIn('id', [$firstreviews->id, $lastreviews->id])
                ->limit(6)
                ->get();
        }
        $news = collect();
        if (!empty($firstNews) && !empty($lastNews)) {
            $news = News::where('status', '1')
                ->whereNotIn('id', [$firstNews->id, $lastNews->id])->limit(6)
                ->get();
        }

        $advices = News::where('status', '1')
            ->orderBy('created_at', 'asc')->limit(4)
            ->get();
        $videos = LatestVideo::where('status', 1)->orderby('id', 'desc')->limit('4')->get();

        return view('frontend.news.vedio_details', compact('news', 'firstNews', 'lastNews', 'reviews', 'firstreviews', 'lastreviews', 'advices', 'videos', 'all', 'vedio'));
    }

    public function setupPassword($id)
    {
        $user = User::find($id);
        return view('frontend.setup-new-password', compact('user'));
    }


    public function login(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',

        ], [
            'required' => 'The :attribute field is required.',
            'min' => 'The :attribute must be at least :min characters.',
            'confirm_password.required' => 'The :attribute field is required.',
            'confirm_password.same' => 'The :attribute must match the password field.',
        ]);
        $user = User::find($id);
        $user->password = Hash::make($request->password);
        $user->email_verified_at = now();
        $user->save();
        // $lead = Lead::where('user_id',$user->id)->orderBy('id','desc')->first();

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $data = [
                'id' => $user->id,
                'name' => $user->name,
            ];
            Mail::to($request->email)->send(new WelcomeEmail($data));

            // return redirect()->route('buyer.profile');
            return redirect()->back();
        } else {
            return  redirect()->back()->with('message', 'user name or password invalid!');
        }
    }

    public function subscribe(Request $request)
    {
        $data = new Subscribe();
        $data->email = $request->email;
        $data->save();
        $notification_title = 'Subscriber message';
        $notification_message = 'A lead come from website';
        $notification_call_back_url = route('admin.subscriber.show');
        $notification_category = 'communication';
        $notificatioN_auth_id = '0';

        $this->saveNewNotification($notification_title, $notification_message, $notification_call_back_url, $notificatioN_auth_id, $notification_category);

        $data = [
            'email' => $request->email
        ];
        Mail::to($request->email)->send(new SubscribeMail($data));
        return response()->json([
            'status' => 'success',
            'message' => 'Subscribe completed successfully'
        ]);
    }

    public function Mess_add(Request $request)
    {
        $message = new Message();

        $message->sender_id = auth()->user()->id;
        $message->receiver_id = $request->receiver_id;
        $message->lead_id = $request->lead_id;

        $message->message = $request->message;
        $message->is_seen = 1;
        $message->save();
        return response()->json([
            'status' => 'success',
            'data' => $message,
            'message' => 'message sent successfully'
        ]);
    }

    public function contactPage($id = null)
    {
        $user = User::find($id);

        return view('frontend.contact', compact('user'));
    }

    public function search(Request $request)
    {

        $request->validate([
            'query' => 'required|string|min:1',
        ], [
            'query.required' => 'Please enter a search term.',
        ]);


        $data = $request->input('query');

        if ($data) {
            $searchWords = explode(' ', $data);
            // $query = Inventory::query();
            // $query = MainInventory::with('additionalInventory')->;

                    // Initiate main inventory query with eager loading
            $query = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'inventory_status')
            ->with([
                'dealer' => function ($query) {
                    $query->select('id', 'dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id');
                },
                'additionalInventory' => function ($query) {
                    $query->select('id', 'main_inventory_id', 'local_img_url');
                },
                'mainPriceHistory' => function ($query) {
                    $query->select('id', 'main_inventory_id', 'change_amount');
                }
            ])->where(function ($query) {
                $query->where('inventory_status', '!=', 'Sold')
                    ->orWhereNull('inventory_status'); // Include NULL values
            });
            // dd($query->get()[0]);
            $query->where(function ($subquery) use ($searchWords) {
                $subquery->where(function ($subquery2) use ($searchWords) {
                    foreach ($searchWords as $word) {
                        $subquery2->where(function ($subquery3) use ($word) {

                            $subquery3->where('make', 'like', '%' . $word . '%')
                                ->orWhere('model', 'like', '%' . $word . '%')
                                // ->orWhere('dealer_id', 'like', '%' . $word . '%')
                                ->orWhere('stock', 'like', '%' . $word . '%')
                                ->orWhere('year', 'like', '%' . $word . '%')
                                ->orWhere('zip_code', 'like', '%' . $word . '%')
                                ->orWhere('vin', 'like', '%' . $word . '%');
                        });
                    }
                })
                    ->orWhere(function ($subquery4) use ($searchWords) {
                        // $subquery4->whereRaw("CONCAT_WS('', year, make, model,vin,dealer_id) LIKE ?", ['%' . implode('%', $searchWords) . '%']);
                        $subquery4->whereRaw("CONCAT_WS('', year, make, model,vin) LIKE ?", ['%' . implode('%', $searchWords) . '%']);
                    });
            });
        }


        $infos = $query->where('status', '1')->paginate(12)->appends(['query' => $data]);
        $totalResults = $infos->total();
        // $count = $infos->count();
        return view('frontend.search', compact('infos', 'totalResults'));
    }

    public function ipAddress(Request $request)
    {
        $ip_address = $request->ip();
        return response()->json(['ip' => $ip_address]);
    }

    // public function quick($id)
    // {
    //     $inventory = Inventory::findOrFail($id);
    //     $route_string = str_replace(
    //         ' ',
    //         '',
    //         $inventory->year .
    //         '-' .
    //         $inventory->make .
    //         '-' .
    //         $inventory->model .
    //         '-in-' .
    //         $inventory->dealer->city .
    //         '-' .
    //         strtoupper($inventory->dealer->state)
    //     );

    //     $vin_string_replace = str_replace(' ', '', $inventory->vin);
    //     $image_obj = $inventory->local_img_url;
    //     $image_splice = explode(',', $image_obj);
    //     $image = str_replace(["[", "'"], "", $image_splice[0]);
    //     $image_path = asset('frontend/') . '/' . $image;

    //     $inventory_transmission = substr($inventory->formatted_transmission, 0, 25);
    //     return response()->json([
    //         'status' => 'success',
    //         'image_url' => $image_path,
    //         'inventory' => $inventory,
    //         'inventory_transmission' => $inventory_transmission,
    //         'route_string' => $route_string,
    //         'vin_string_replace' => $vin_string_replace,
    //     ]);
    // }

    public function quick($id)
    {
        // Retrieve the inventory item or fail if not found
        // $inventory = Inventory::findOrFail($id);
        // $inventory = MainInventory::with('additionalInventory')->findOrFail($id);
        $inventory = MainInventory::select('id', 'deal_id', 'year', 'make', 'model', 'fuel', 'vin', 'stock', 'mpg_city', 'mpg_highway', 'exterior_color', 'created_at', 'drive_info', 'transmission')
            ->with([
                'dealer:id,city,state', // Include deal_id along with city and state
                'additionalInventory:main_inventory_id,local_img_url'
            ])
            ->findOrFail($id); // Get the inventory by ID

        $route_string = str_replace(
            ' ',
            '',
            $inventory->year .
                '-' .
                $inventory->make .
                '-' .
                $inventory->model .
                '-in-' .
                $inventory->dealer->city .
                '-' .
                strtoupper($inventory->dealer->state)
        );

        $vin_string_replace = str_replace(' ', '', $inventory->vin);



        // Get the image URLs
        $image_obj = $inventory->additionalInventory->local_img_url;

        // Assuming `local_img_url` contains comma-separated URLs
        $image_splice = explode(',', $image_obj);

        // Clean and map the image URLs
        $image_urls = array_map(function ($image) {
            // Remove any unwanted characters and construct the full URL
            return asset(trim(trim($image, "[]'")));
        }, $image_splice);

        $inventory_transmission = substr($inventory->formatted_transmission, 0, 25);
        // return $image_urls;

        // Return the response in JSON format
        return response()->json([
            'status' => 'success',
            'image_urls' => $image_urls,
            'inventory' => $inventory,
            'route_string' => $route_string,
            'vin_string_replace' => $vin_string_replace,
            'inventory_transmission' => $inventory_transmission,
        ]);
    }

    public function forsale()
    {
        $vehicles_obj = VehicleMake::query();
        $vehicles = $vehicles_obj->where('status', 1)->get();
        $tips = Tips::where('status', 1)->limit(3)->get();
        $faqs = Faq::where('status', 1)->where('type', 'carsforsale')->get();
        return view('frontend.carforsale', compact('vehicles', 'faqs', 'tips'));
    }

    public function forSaleNew()
    {
        $vehicles_obj = VehicleMake::query();
        $vehicles = $vehicles_obj->where('status', 1)->get();

        $subquery_truck = DB::table('inventories')
            ->select('make', DB::raw('MIN(price) as min_price'), DB::raw('COUNT(*) as count'))
            ->where('body_formated', 'truck')
            ->where('type', 'new')
            ->groupBy('make');

        $trucks = DB::table(DB::raw("({$subquery_truck->toSql()}) as sub"))
            ->mergeBindings($subquery_truck)
            ->select('make', 'min_price', 'count')
            ->get();

        $subquery_suv = DB::table('inventories')
            ->select('make', DB::raw('MIN(price) as min_price'), DB::raw('COUNT(*) as count'))
            ->where('body_formated', 'full size suv')
            ->where('type', 'new')
            ->groupBy('make');

        $suvs = DB::table(DB::raw("({$subquery_suv->toSql()}) as sub"))
            ->mergeBindings($subquery_suv)
            ->select('make', 'min_price', 'count')
            ->get();

        $subquery_sedan = DB::table('inventories')
            ->select('make', DB::raw('MIN(price) as min_price'), DB::raw('COUNT(*) as count'))
            ->where('body_formated', 'sedan')
            ->where('type', 'new')
            ->groupBy('make');

        $sedans = DB::table(DB::raw("({$subquery_sedan->toSql()}) as sub"))
            ->mergeBindings($subquery_sedan)
            ->select('make', 'min_price', 'count')
            ->get();

        $subquery_coupe = DB::table('inventories')
            ->select('make', DB::raw('MIN(price) as min_price'), DB::raw('COUNT(*) as count'))
            ->where('body_formated', 'coupe')
            ->where('type', 'new')
            ->groupBy('make');

        $coupes = DB::table(DB::raw("({$subquery_coupe->toSql()}) as sub"))
            ->mergeBindings($subquery_coupe)
            ->select('make', 'min_price', 'count')
            ->get();

        $subquery_van = DB::table('inventories')
            ->select('make', DB::raw('MIN(price) as min_price'), DB::raw('COUNT(*) as count'))
            ->where('body_formated', 'minivan')
            ->where('type', 'new')
            ->groupBy('make');

        $vans = DB::table(DB::raw("({$subquery_van->toSql()}) as sub"))
            ->mergeBindings($subquery_van)
            ->select('make', 'min_price', 'count')
            ->get();
        // return $vans;

        $subquery_hatchback = DB::table('inventories')
            ->select('make', DB::raw('MIN(price) as min_price'), DB::raw('COUNT(*) as count'))
            ->where('body_formated', 'hatchback')
            ->where('type', 'new')
            ->groupBy('make');

        $hatchbacks = DB::table(DB::raw("({$subquery_hatchback->toSql()}) as sub"))
            ->mergeBindings($subquery_hatchback)
            ->select('make', 'min_price', 'count')
            ->get();

        $subquery_minivan = DB::table('inventories')
            ->select('make', DB::raw('MIN(price) as min_price'), DB::raw('COUNT(*) as count'))
            ->where('body_formated', 'minivan')
            ->where('type', 'new')
            ->groupBy('make');

        $minivans = DB::table(DB::raw("({$subquery_minivan->toSql()}) as sub"))
            ->mergeBindings($subquery_minivan)
            ->select('make', 'min_price', 'count')
            ->get();

        $subquery_wagon = DB::table('inventories')
            ->select('make', DB::raw('MIN(price) as min_price'), DB::raw('COUNT(*) as count'))
            ->where('body_formated', 'Station Wagon')
            ->where('type', 'new')
            ->groupBy('make');

        $wagons = DB::table(DB::raw("({$subquery_wagon->toSql()}) as sub"))
            ->mergeBindings($subquery_wagon)
            ->select('make', 'min_price', 'count')
            ->get();
        // dd($suvs, $sedans);
        return view('frontend.newcarforsale', compact('vehicles', 'trucks', 'suvs', 'sedans', 'coupes', 'vans', 'wagons', 'hatchbacks', 'minivans'));
    }


    public function getLocation(Request $request)
    {
        // $minutes = 7 * 24 * 60;
        // Cookie::queue('zipcode', '787201', $minutes);
        // return response()->json([
        //     'zipcode' => '787201',
        // ]);

        $ip = $request->ip();
        $position = Location::get($ip);
        return $this->setZipCode($position, $ip);
    }

    public function setZipCode($position, $ip)
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

    public function success_message()
    {
        return view('frontend.success.success');
    }

    public function fail_message()
    {
        return view('frontend.fail.fail');
    }

    public function research()
    {
        $news = News::with('user')->orderByDesc('id')->where('status', '1')->get();
        $tips = Tips::orderByDesc('id')->where('status', '1')->get();
        // $sels = Blog::where('type', 'Selling your car')->where('status', '1')->get();
        // $shops = Blog::where('type', 'Shopping & negotiating')->where('status', '1')->get();
        // $owners = Blog::where('type', 'Ownership & maintenance')->where('status', '1')->get();
        $sels = Blog::where('type', 1)->orderByDesc('id')->where('status', '1')->get();
        $shops = Blog::where('type', 2)->orderByDesc('id')->where('status', '1')->get();
        $owners = Blog::where('type', 3)->orderByDesc('id')->where('status', '1')->get();
        $faqs = Faq::where('status', '1')->where('type', 'research')->get();

        return view('frontend.research', compact('news', 'sels', 'shops', 'owners', 'faqs','tips'));
    }

    public function article_details($slug = null)
    {
        $encodedSlug = urlencode($slug);
        $art = Blog::where('slug', 'LIKE', "%{$encodedSlug}%")->first();

        // $art = Blog::where('slug',$slug)->first();
        // dd($art, $slug, $encodedSlug);
        $artId = $art->id;

        $rels = Blog::whereNot('id', $artId)->limit(6)->get();
        return view('frontend.article-details', compact('art', 'rels'));
    }

    public function tipsDetails($slug)
    {
        // dd($slug);
        try {
            $data = Tips::where('slug', $slug)->firstOrFail();

            $news = Tips::whereNot('slug', $slug)->orderBy('created_at', 'desc')->get();
            return view('frontend.news.tips_details', compact('data', 'news'));
        } catch (ModelNotFoundException $e) {
            abort(404, 'News article not found.');
        }
    }

    public function arpRateByAjax(Request $request)
    {
        $setting = Setting::where('data_type', 1)
            ->orderByDesc('value')
            ->pluck('value', 'key');
        return response()->json($setting);
    }

    public function arpRateValue(Request $request)
    {
        if ($request->action == 'search-rate') {
            $arpRateValue = Setting::where('id', $request->arpRate)->where('data_type', 1)->select('id', 'key', 'value')->first();
            return $arpRateValue->value;
        }
    }

    private function fetchCountyName($zipCode) 
    {
        $countyName = LocationZip::where('zip_code', $zipCode)->value('county');
        if($countyName){
            $data =  $countyName;
        }else{
            $data = 'countyName';
        }
        return $countyName;

        // this part not run yet 
        $apiUrl = "https://api.example.com/get-county?zip=" . $zipCode; // Replace with actual API URL
    
        try {
            $response = Http::get($apiUrl); // Using Laravel HTTP Client
    
            if ($response->successful()) {
                return $response->json()['county'] ?? null;
            }
        } catch (\Exception $e) {
            // \Log::error("API request failed: " . $e->getMessage());
        }
    
        return null;
    }


    private function getItemByDistance($zipCode, $zip_radios, $query)
    {

        $zipCode = $zipCode ?? '78228';
        $zip_radios = $zip_radios ?? 75;

        $message = ''; // Initialize message variable

        // if (!$zipCode) {
        //     return [
        //         'inventories' => $query->orderByDesc('id')->paginate(20),
        //         'message' => $message
        //     ];
        // }

        // Check if zipCode exists in location_zips
        $zipLocation = DB::table('location_zips')->where('zip_code', $zipCode)->first();

        if ($zipLocation) {
            $latitude = $zipLocation->latitude;
            $longitude = $zipLocation->longitude;
        } else {
            // Fetch from OpenCage API if not found in database
            $countryCode = 'us';
            $apiKey = "4b84ff4ad9a74c79ad4a1a945a4e5be1";
            $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key={$apiKey}";

            $response = file_get_contents($url);
            $zip_location_data = json_decode($response, true);

            if (isset($zip_location_data['results'][0]['geometry'])) {

                $state_name = $zip_location_data['results'][0]['components']['state'] ?? 'UA';
                $state_code = $zip_location_data['results'][0]['components']['state_code'] ?? 'UA';
                $city_name = $zip_location_data['results'][0]['components']['_normalized_city'] ?? 'UA';
                $latitude = $zip_location_data['results'][0]['geometry']['lat'] ?? null;
                $longitude = $zip_location_data['results'][0]['geometry']['lng'] ?? null;
                $zipCode = $zip_location_data['results'][0]['components']['postcode'] ?? $zipCode;
                $url = $zip_location_data['results'][0]['annotations']['OSM']['url'] ?? null;

                // // Check if state already exists
                // $existingState = DB::table('location_states')->where('state_name', $state_name)->first();

                // // If the state exists, use the existing state_id and batch_no
                // if ($existingState) {
                //     $location_state_id = $existingState->id;  // Use the existing state's ID // Use the existing batch_no
                // } else {
                //     // Get the maximum batch number from location_states
                //     $maxBatchNo = DB::table('location_states')->max('batch_no');
                //     // Set new batch number (if exists, increment by 1; otherwise, set to 100)
                //     $batch_no = $maxBatchNo ? $maxBatchNo + 1 : 100;

                //     // Insert into location_states and get ID
                //     $location_state_id = DB::table('location_states')->insertGetId([
                //         'state_name' => $state_name,    // Full state name
                //         'short_name' => $state_code,    // State code
                //         'sales_tax' => 8,
                //         'status' => 1,
                //         'batch_no' => $batch_no,
                //         'created_at' => now(),
                //         'updated_at' => now()
                //     ]);
                // }

                // // Check if city already exists
                // $existingCity = DB::table('location_cities')
                //                 ->where('location_state_id', $location_state_id)
                //                 ->where('city_name', $city_name)
                //                 ->first();

                // // If the city exists, use the existing city_id, otherwise insert the new city
                // if ($existingCity) {
                //     $location_city_id = $existingCity->id;  // Use the existing city's ID
                // } else {
                //     // Insert into location_cities and get ID
                //     $location_city_id = DB::table('location_cities')->insertGetId([
                //         'location_state_id' => $location_state_id,
                //         'city_name' => $city_name,
                //         'latitude' => $latitude,
                //         'longitude' => $longitude,
                //         'sales_tax' => 8,
                //         'status' => 1,
                //         'created_at' => now(),
                //         'updated_at' => now()
                //     ]);
                // }

                // // Check if location_zip already exists for the given city and zip code
                // $existingZip = DB::table('location_zips')
                //                 ->where('location_city_id', $location_city_id)
                //                 ->where('zip_code', $zipCode)
                //                 ->first();

                // // If the location_zip exists, do not insert, else insert new location_zip
                // if (!$existingZip) {
                //     DB::table('location_zips')->insert([
                //         'location_city_id' => $location_city_id,
                //         'latitude' => $latitude,
                //         'longitude' => $longitude,
                //         'zip_code' => $zipCode,
                //         'sales_tax' => 8,
                //         'src_url' => $url,
                //         'status' => 1,
                //         'created_at' => now(),
                //         'updated_at' => now()
                //     ]);
                // }
            } else {
                // If API fails, return default paginated results
                return [
                    'inventories' => $query->orderByDesc('id')->paginate(20),
                    'message' => "Invalid Zip Code"
                ];
            }
        }

        // Filtering by distance
        $zipCodeQuery = clone $query;
        $zipCodeQuery->select([
            'main_inventories.id',
            'main_inventories.deal_id',
            'main_inventories.vin',
            'main_inventories.year',
            'main_inventories.make',
            'main_inventories.model',
            'main_inventories.price',
            'main_inventories.title',
            'main_inventories.miles',
            'main_inventories.price_rating',
            'main_inventories.zip_code',
            'main_inventories.payment_price'
        ])->selectRaw(
            "(3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
        sin(radians(?)) * sin(radians(latitude)))) AS distance",
            [$latitude, $longitude, $latitude]
        );

        $zipCodeQuery->having('distance', '<=', $zip_radios);
        $zipCodeQuery->orderBy('distance', 'asc');

        $zipCodeInventories = $zipCodeQuery->get();

        if (!$zipCodeInventories->isEmpty()) {
            $inventories = $zipCodeQuery->paginate(20);
            $maxDistance = $zipCodeInventories->max('distance');

            if ($maxDistance > $zip_radios) {
                $message = $this->generateMessage($zip_radios, $maxDistance);
            }
        } else {
            $inventories = $query->paginate(20);
            $message = $this->generateNoMatchMessage($zip_radios);
        }

        return [
            'inventories' => $inventories,
            'message' => $message
        ];
    }


    // private function getItemByDistance($zipCode, $zip_radios, $query)
    // {
    //     $message = ''; // Initialize the message variable

    //     if (!$zipCode) {
    //         return [
    //             'inventories' => $query->orderByDesc('id')->paginate(20),
    //             'message' => $message
    //         ];
    //     }

    //     $countryCode = 'us';
    //     $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
    //     $response = file_get_contents($url);
    //     $zip_location_data = json_decode($response, true);

    //     try {
    //         if (!empty($zip_radios) && is_string($zip_radios)) {
    //             if (isset($zip_location_data['results'][0]['geometry'])) {
    //                 $latitude = $zip_location_data['results'][0]['geometry']['lat'];
    //                 $longitude = $zip_location_data['results'][0]['geometry']['lng'];

    //                 // Filtering by distance
    //                 $zipCodeQuery = clone $query;
    //                 $zipCodeQuery->select([
    //                     'main_inventories.id',
    //                     'main_inventories.deal_id',
    //                     'main_inventories.vin',
    //                     'main_inventories.year',
    //                     'main_inventories.make',
    //                     'main_inventories.model',
    //                     'main_inventories.price',
    //                     'main_inventories.title',
    //                     'main_inventories.miles',
    //                     'main_inventories.price_rating',
    //                     'main_inventories.zip_code',
    //                     'main_inventories.payment_price'
    //                 ])->selectRaw(
    //                     "(3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
    //                 sin(radians(?)) * sin(radians(latitude)))) AS distance",
    //                     [$latitude, $longitude, $latitude]
    //                 );
    //                 $zipCodeQuery->having('distance', '<=', $zip_radios);
    //                 $zipCodeQuery->orderBy('distance', 'asc');

    //                 $zipCodeInventories = $zipCodeQuery->get();

    //                 if (!$zipCodeInventories->isEmpty()) {
    //                     $inventories = $zipCodeQuery->paginate(20);
    //                     $maxDistance = $zipCodeInventories->max('distance');

    //                     if ($maxDistance > $zip_radios) {
    //                         $message = $this->generateMessage($zip_radios, $maxDistance);
    //                     }
    //                 } else {
    //                     $inventories = $query->paginate(20);
    //                     $message = $this->generateNoMatchMessage($zip_radios);
    //                 }
    //             } else {
    //                 $inventories = $query->orderByDesc('id')->paginate(20);
    //             }
    //         } else {
    //             // If no zip radius is set, find exact matches
    //             $zipCodeQuery = clone $query;
    //             $zipCodeQuery->where('zip_code', $zipCode);
    //             $zipCodeInventories = $zipCodeQuery->get();

    //             if ($zipCodeInventories->isEmpty()) {
    //                 $radiusOptions = [10, 25, 50, 100];
    //                 $foundInventories = false;
    //                 $lastRadiusChecked = '';

    //                 if (isset($zip_location_data['results'][0]['geometry'])) {
    //                     $latitude = $zip_location_data['results'][0]['geometry']['lat'];
    //                     $longitude = $zip_location_data['results'][0]['geometry']['lng'];

    //                     foreach ($radiusOptions as $radius) {
    //                         $zipCodeQuery = clone $query;
    //                         $zipCodeQuery->select([
    //                             'main_inventories.id',
    //                             'main_inventories.deal_id',
    //                             'main_inventories.vin',
    //                             'main_inventories.year',
    //                             'main_inventories.make',
    //                             'main_inventories.model',
    //                             'main_inventories.price',
    //                             'main_inventories.title',
    //                             'main_inventories.miles',
    //                             'main_inventories.price_rating',
    //                             'main_inventories.zip_code',
    //                             'main_inventories.payment_price'
    //                         ])->selectRaw(
    //                             "(3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
    //                         sin(radians(?)) * sin(radians(latitude)))) AS distance",
    //                             [$latitude, $longitude, $latitude]
    //                         );
    //                         $zipCodeQuery->having('distance', '<=', $radius);
    //                         $zipCodeQuery->orderBy('distance', 'asc');

    //                         if ($zipCodeQuery->count() > 0) {
    //                             $inventories = $zipCodeQuery->orderByDesc('id')->paginate(20);
    //                             $foundInventories = true;
    //                             break;
    //                         } else {
    //                             $lastRadiusChecked = $radius;
    //                         }
    //                     }
    //                 }

    //                 if (!$foundInventories) {
    //                     $inventories = $query->orderByDesc('id')->paginate(20);
    //                     $message = $this->generateNoMatchMessage($lastRadiusChecked);
    //                 }
    //             } else {
    //                 $inventories = $zipCodeQuery->orderByDesc('id')->paginate(20);
    //             }
    //         }
    //     } catch (\Exception $e) {
    //         Log::error("Error processing zip code {$zipCode}: " . $e->getMessage());
    //         $message = 'An error occurred while processing your request. Please try again.';
    //         $inventories = $query->orderByDesc('id')->paginate(20);
    //     }

    //     return [
    //         'inventories' => $inventories,
    //         'message' => $message
    //     ];
    // }

    // Helper function for generating messages
    private function generateMessage($zip_radios, $maxDistance)
    {
        return "
        <section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:3px\" class=\"sptb2\">
            <div style=\"border-radius:5px\" class=\"container bg-white p-5\">
                <div class=\"text-center\">
                    <h3 class=\"mb-2\">You searched {$zip_radios} miles...</h3>
                    <p class=\"mb-2\">Showing results within {$maxDistance} miles.</p>
                </div>
            </div>
        </section>";
    }

    // Helper function for no match messages
    private function generateNoMatchMessage($radius)
    {
        return "
        <section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px\" class=\"sptb2\">
            <div style=\"border-radius:5px\" class=\"container bg-white p-5 match\">
                <div class=\"text-center\">
                    <h3 class=\"mb-2\">No exact matches within {$radius} miles...</h3>
                    <p class=\"mb-2\">Modify your filters or click \"Save Search\" to be notified when more matches are available.</p>
                    <a href=\"#\" class=\"mb-2 clearfilterAjax\" style=\"text-decoration:underline;font-weight:bold;font-size:15px\" id=\"clearfilterAjax\">Clear all filters.</a>
                </div>
            </div>
        </section>";
    }
}
