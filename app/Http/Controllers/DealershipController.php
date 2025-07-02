<?php

namespace App\Http\Controllers;

use App\Interface\InventoryServiceInterface;
use App\Models\Dealer;
use App\Models\LocationCity;
use App\Models\LocationState;
use App\Models\MainInventory;
use App\Models\User;
use App\Models\UserTrack;
use App\Models\VehicleMake;
use App\Service\DealerService;
use App\Traits\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\App;

class DealershipController extends Controller
{
    use Notify;
    private $inventoryService;
    private $dealerService;
    public function __construct(InventoryServiceInterface $inventoryService, DealerService $dealerService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealerService = $dealerService;
    }



    public function showFindDealership(Request $request)
    {
        $cacheFilePath = storage_path('app/cached_dealers.json');

        // Fetch dealers from cache or database
        $cachedDealers = $this->getCachedDealers($cacheFilePath, $request);
        // Apply filters if they exist
        if ($request->ajax() && !$cachedDealers->isEmpty()) {
            return $this->handleAjaxRequest($cachedDealers, $request);
        }

        return view('frontend.dealer.index');
    }

    private function getCachedDealers($cacheFilePath, $request)
    {
        if (!file_exists($cacheFilePath)) {
            return $this->fetchDealersFromDatabase($request);
        }

        try {
            $dealersJson = file_get_contents($cacheFilePath);
            $dealers = json_decode($dealersJson, false); // Decode as object
            return collect($dealers);
        } catch (\Exception $e) {
            // Log the error and fetch from database as fallback
            // \Log::error('Failed to read or decode cached dealers: ' . $e->getMessage());
            return $this->fetchDealersFromDatabase($request);
        }
    }

    private function fetchDealersFromDatabase($request)
    {

        // $query = User::with(['roles', 'mainInventories'])
        //     ->where('status', 1)
        //     ->whereHas('roles', function ($query) {
        //         $query->where('name', 'dealer');
        //     })
        //     ->select('id', 'dealer_id', 'name', 'phone', 'address', 'city', 'state', 'zip','brand_website')
        //     ->distinct()
        //     ->orderByRaw("
        //         CASE
        //             WHEN name REGEXP '^[0-9]' THEN 1
        //             ELSE 0
        //         END,
        //         name ASC
        //     ");

        // $query = User::with([
        //     'roles' => function ($query) {
        //         $query->select('id', 'name'); // Select specific columns from the roles table
        //     },
        //     'mainInventories' => function ($query) {
        //         $query->select('id', 'deal_id', 'inventory_status')
        //             ->where('inventory_status', '!=', "Sold")
        //             ->orWhereNull('inventory_status');; // Select specific columns from the mainInventories table
        //     }
        // ])

        // new 100 % work
        // $query = Dealer::with([
        //     'roles' => function ($query) {
        //         $query->select('id', 'name'); // Select specific columns from the roles table
        //     },
        //     'mainInventories' => function ($query) {
        //         $query->select('id', 'deal_id', 'inventory_status')
        //             ->where('inventory_status', '!=', "Sold")
        //             ->orWhereNull('inventory_status');; // Select specific columns from the mainInventories table
        //     }
        // ])
        //     ->where('status', 1)
        //     ->whereHas('roles', function ($query) {
        //         $query->where('name', 'dealer');
        //     })
        //     ->select('id', 'dealer_id', 'name', 'phone', 'address', 'city', 'state', 'zip', 'brand_website')
        //     ->distinct()
        //     ->orderByRaw("
        //     CASE
        //         WHEN name REGEXP '^[0-9]' THEN 1
        //         ELSE 0
        //     END,
        //     name ASC
        // ");
        // $dealers = $query->get();

        // Cache the dealers to a JSON file
        // $this->cacheDealersToFile($dealers);

        $query = Dealer::with([
            'mainInventories' => function ($query) {
                $query->select('id', 'deal_id', 'inventory_status')
                    ->where('inventory_status', '!=', "Sold")
                    ->orWhereNull('inventory_status');
            }
        ])
            ->where('status', 1) // Keep status filter if needed
            ->select('id', 'dealer_id', 'name', 'phone', 'address', 'city', 'state', 'zip', 'brand_website')
            ->distinct()
            ->orderByRaw("
        CASE
            WHEN name REGEXP '^[0-9]' THEN 1
            ELSE 0
        END,
        name ASC
    ");

        $dealers = $query->get();

        return $dealers;
    }

    private function cacheDealersToFile($dealers)
    {
        $cacheFilePath = storage_path('app/cached_dealers.json');
        try {
            file_put_contents($cacheFilePath, json_encode($dealers));
        } catch (\Exception $e) {
            // \Log::error('Failed to cache dealers to file: ' . $e->getMessage());
        }
    }


    private function handleAjaxRequest($cachedDealers, $request)
    {
        $target_city = $request->city;
        $target_state = $request->state;
        $target_name = $request->name;

        $filteredDealers = $cachedDealers->filter(function ($dealer) use ($target_city, $target_state, $target_name) {
            $matches = true;

            if ($target_city && $dealer->city != $target_city) {
                $matches = false;
            }
            if ($target_state && $dealer->state != $target_state) {
                $matches = false;
            }
            if ($target_name && !str_contains(strtolower($dealer->name), strtolower($target_name))) {
                $matches = false;
            }

            return $matches;
        });

        // Paginate the filtered results
        $page = $request->input('page', 1);
        $perPage = 20;
        $paginatedDealers = new LengthAwarePaginator(
            $filteredDealers->forPage($page, $perPage),
            $filteredDealers->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Fetch cities and states for filters
        $cities = LocationCity::where('status', 1)
            ->orderBy('city_name', 'asc')
            ->distinct()
            ->pluck('city_name', 'id');

        $states = User::whereNotNull('state')
            ->distinct()
            ->orderBy('state', 'asc')
            ->pluck('state');

        $full_state_name = LocationState::whereIn('short_name', $states)->pluck('short_name', 'state_name');
        $state_names = $full_state_name;

        $select_cities = User::whereNotNull('city')
            ->distinct('city')
            ->orderBy('city', 'asc')
            ->where('state', $target_state)
            ->pluck('city')->toArray();

        // Pagination values
        $current_page_count = $paginatedDealers->count();
        $total_count = number_format($paginatedDealers->total());
        $single_dealer_count = ($paginatedDealers->perPage() * ($paginatedDealers->currentPage() - 1)) + $current_page_count;
        $dealers = $paginatedDealers;

        $view = view('frontend.dealer.dealer_ajax', compact('dealers', 'total_count', 'single_dealer_count', 'cities', 'select_cities', 'state_names', 'target_city', 'target_state', 'target_name'))->render();

        return response()->json([
            'view' => $view,
            'pagination' => $paginatedDealers->links()->toHtml(),
            'total_count' => $total_count,
            'select_cities' => $select_cities,
        ]);
    }






    public function showFindDealership02(Request $request)
    {

        // $dealers = $this->dealerService->getCachedDealers();
        // foreach($dealers as $dealer){

        //     dd($dealer);
        // }
        // $cachedDealers = Cache::get('cached_dealers', collect());

        $cacheFilePath = storage_path('app/cached_dealers.json');

        // // Check if the cache file exists
        // if (!file_exists($cacheFilePath)) {
        //     return response()->json(['error' => 'Cache file not found'], 404);
        // }

        // Read the JSON file
        $dealersJson = file_get_contents($cacheFilePath);

        // Decode the JSON into an object (instead of an array)
        $dealers = json_decode($dealersJson); // No second argument or pass `false`
        $cachedDealers = collect($dealers);


        // // If cache is empty, fetch data from the database and cache it again
        // if ($cachedDealers->isEmpty()) {
        // $query = User::with(['roles', 'inventories'])
        //     $query = User::with(['roles', 'mainInventories'])
        //         ->where('status', 1)
        //         ->whereHas('roles', function ($query) {
        //             $query->where('name', 'dealer');
        //         })
        //         ->select('id', 'dealer_id', 'name', 'phone', 'address', 'city', 'state', 'zip','brand_website')
        //         ->distinct()
        //         ->orderByRaw("
        //             CASE
        //                 WHEN name REGEXP '^[0-9]' THEN 1
        //                 ELSE 0
        //             END,
        //             name ASC
        //         ");

        //     $cachedDealers = $query->get();
        //     // Cache::put('cached_dealers', $cachedDealers, now()->addMinutes(60));
        //     Cache::put('cached_dealers', $cachedDealers, now()->addDay());
        // }


        // Apply filters if they exist
        if (!$cachedDealers->isEmpty()) {
            if ($request->ajax()) {
                $target_city = $request->city;
                $target_state = $request->state;
                $target_name = $request->name;

                $filteredDealers = $cachedDealers->filter(function ($dealer) use ($target_city, $target_state, $target_name) {
                    $matches = true;

                    if ($target_city && $dealer->city != $target_city) {
                        $matches = false;
                    }
                    if ($target_state && $dealer->state != $target_state) {
                        $matches = false;
                    }
                    if ($target_name && !str_contains(strtolower($dealer->name), strtolower($target_name))) {
                        $matches = false;
                    }

                    return $matches;
                });

                // Paginate the filtered results manually
                $page = $request->input('page', 1); // Get the current page from the request
                $perPage = 20; // Number of items per page
                $paginatedDealers = new LengthAwarePaginator(
                    $filteredDealers->forPage($page, $perPage),
                    $filteredDealers->count(),
                    $perPage,
                    $page,
                    ['path' => $request->url(), 'query' => $request->query()]
                );

                $cities = LocationCity::where('status', 1)
                    ->orderBy('city_name', 'asc')
                    ->distinct()
                    ->pluck('city_name', 'id');

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

                // Pagination values
                $current_page_count = $paginatedDealers->count();
                $total_count = number_format($paginatedDealers->total());
                $single_dealer_count = ($paginatedDealers->perPage() * ($paginatedDealers->currentPage() - 1)) + $current_page_count;

                $dealers = $paginatedDealers;
                $view = view('frontend.dealer.dealer_ajax', compact('dealers', 'total_count', 'single_dealer_count', 'cities', 'select_cities', 'state_names', 'target_city', 'target_state', 'target_name'))->render();

                return response()->json([
                    'view' => $view,
                    'pagination' => $paginatedDealers->links()->toHtml(),
                    'total_count' => $total_count,
                    'select_cities' => $select_cities,
                ]);
            }

            return view('frontend.dealer.index');
        } else {

            $query = User::with([
                'roles' => function ($query) {
                    $query->select('id', 'name'); // Select specific columns from the roles table
                },
                'mainInventories' => function ($query) {
                    $query->select('id', 'deal_id'); // Select specific columns from the mainInventories table
                }
            ])
                ->where('status', 1)
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'dealer');
                })
                ->select('id', 'dealer_id', 'name', 'phone', 'address', 'city', 'state', 'zip', 'brand_website')
                ->distinct()
                ->orderByRaw("
                CASE
                    WHEN name REGEXP '^[0-9]' THEN 1
                    ELSE 0
                END,
                name ASC
            ");

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
                    ->distinct()
                    ->pluck('city_name', 'id');

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

            return view('frontend.dealer.index');
        }
    }


    // public function auto(Request $request, $param = null)
    // public function dealerinfo(Request $request, $stockId, $dealer_name, $id = null)
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
    //     $vehicles = VehicleMake::orderBy('make_name')
    //         ->where('status', 1)
    //         ->pluck('id', 'make_name');

    //     // Get min/max price and miles in a single query
    //     $inventoryStats = MainInventory::selectRaw(
    //         'MAX(CASE WHEN price != "N/A" THEN price END) as max_price,
    //             MIN(CASE WHEN price != "N/A" THEN price END) as min_price,
    //             MAX(CASE WHEN miles != "N/A" THEN miles END) as max_miles,
    //             MIN(CASE WHEN miles != "N/A" THEN miles END) as min_miles'
    //     )
    //         ->first();

    //     // Fetch distinct body_formated and fuel values separately
    //     $vehicles_body = MainInventory::whereNotNull('body_formated')
    //         ->distinct()
    //         ->pluck('body_formated')
    //         ->sort()
    //         ->values()
    //         ->toArray();

    //     $vehicles_fuel_other = MainInventory::whereNotNull('fuel')
    //         ->distinct()
    //         ->pluck('fuel')
    //         ->sort()
    //         ->values()
    //         ->toArray();

    //     // Assign values
    //     $price_max = (int) $inventoryStats->max_price;
    //     $price_min = (int) $inventoryStats->min_price;
    //     $miles_max = (int) $inventoryStats->max_miles;
    //     $miles_min = (int) $inventoryStats->min_miles;

    //     // Dump and check the results

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

    //         // if (!$existingRecord) {
    //         //     $history_saved = new UserTrack();
    //         //     $history_saved->type = $type;
    //         //     $history_saved->links = $currentUrl;
    //         //     $history_saved->title = $homeInventorySearch . ' ' . $homeMakeSearch . ' ' . $homeModelSearch . ' ' . $homeBestMakeSearch;
    //         //     $history_saved->image = $image;
    //         //     $history_saved->ip_address = $ip_address;
    //         //     $history_saved->user_id = $user_id;
    //         //     $history_saved->save();
    //         // }


    //         $mainInventory = MainInventory::select(
    //             'id',
    //             'year',
    //             'make',
    //             'model',
    //             'price',
    //             'miles',
    //             'price_rating',
    //             'zip_code',
    //             'latitude',
    //             'longitude',
    //             'payment_price',
    //             'type',
    //             'inventory_status'
    //         )
    //             ->with([
    //                 'dealer' => function ($query) {
    //                     $query->select(
    //                         'id',
    //                         'dealer_id',
    //                         'name',
    //                         'state',
    //                         'brand_website',
    //                         'rating',
    //                         'review',
    //                         'phone',
    //                         'city',
    //                         'zip',
    //                         'role_id'
    //                     );
    //                 },
    //             ])
    //             ->where(function ($query) {
    //                 $query->where('inventory_status', '!=', 'Sold')
    //                     ->orWhereNull('inventory_status'); // Include NULL values
    //             })
    //             ->whereNotNull('main_inventories.price') // Ensure price is not null
    //             ->whereHas('dealer', function ($query) use ($id) { // Correct filtering for dealer
    //                 $query->where('id', $id);
    //             });


    //         $homeBodySearch = $queryParams['homeBodySearch'] ?? null;
    //         $make = $queryParams['make'] ?? null;
    //         $model = $queryParams['model'] ?? null;
    //         $location = $queryParams['location'] ?? null;
    //         $zipCode = $queryParams['zip'] ?? null;
    //         $zip_radios = $queryParams['radius'] ?? null;
    //         $mobileLocation = $request->input('mobilelocation') ?? null;
    //         $webLocation = $request->input('weblocationNewInput') ?? null;
    //         $location = $webLocation ?? $mobileLocation ?? null;

    //         $message = '';

    //         $zipCodeData = [
    //             'zip_code_data' => $zipCode,
    //             'zip_radios_data' => $zip_radios,
    //             'query_data' => $mainInventory,
    //         ];

    //         $mainInventoryIds =  $this->inventoryService->getItemByFilter($request, null, $mainInventory);
    //         $matchingIds = $mainInventoryIds->pluck('id')->toArray();

    //         // if ($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide') {
    //         //     // $inventories = $mainInventory->paginate(20);
    //         //     $message = 'null';
    //         //     $mainInventoryIds =  $this->inventoryService->getItemByFilter($request, null, $mainInventory)
    //         //     ->pluck('id')->toArray();

    //         // }else{
    //         //     $distance_data = $this->getItemByDistance($zipCodeData['zip_code_data'], $zipCodeData['zip_radios_data'], $zipCodeData['query_data']);
    //         //     $mainInventoryData = $distance_data['inventories'];
    //         //     $message = $distance_data['message'];

    //         //     // $inventories = $this->inventoryService->getItemByFilter($request, $dealer_id, $mainInventoryData);

    //         //     $mainInventoryIds =  $this->inventoryService->getItemByFilter($request, null, $mainInventoryData)
    //         //     ->pluck('id')->toArray();
    //         // }
    //         // Return both $message and $mainInventoryIds as an array
    //         // return [
    //         //     'message' => $message,
    //         //     'mainInventoryIds' => $mainInventoryIds,
    //         // ];
    //         // });

    //         $message = '';
    //         // dd($matchingIds);
    //         // // Parse URL parameters for dealer city/state filtering
    //         // $urlData = parse_url($request->input('requestURL'));
    //         // if (isset($urlData['query'])) {
    //         //     parse_str($urlData['query'], $queryParams);
    //         //     if (isset($queryParams['homeDealerCitySearch']) && isset($queryParams['homeDealerStateSearch'])) {
    //         //         $city_data = $queryParams['homeDealerCitySearch'];
    //         //         $state_data = $queryParams['homeDealerStateSearch'];

    //         //         if (!empty($city_data)) {
    //         //             $query->whereHas('dealer', function ($q) use ($city_data) {
    //         //                 $q->where('city', 'like', '%' . $city_data . '%');
    //         //             });
    //         //         }

    //         //         if (!empty($state_data)) {
    //         //             $query->whereHas('dealer', function ($q) use ($state_data) {
    //         //                 $q->where('state', 'like', '%' . $state_data . '%');
    //         //             });
    //         //         }
    //         //     }
    //         // }

    //         // // Extract more parameters from URL
    //         // $urlComponents = parse_url($request->requestURL);
    //         // $queryParams = [];
    //         // if (isset($urlComponents['query'])) {
    //         //     parse_str($urlComponents['query'], $queryParams);
    //         // }



    //         // if ($location !== null) {
    //         //     queueZipCodeCookie($location);
    //         // } else {
    //         //     Cookie::queue(Cookie::forget('zipcode'));
    //         // }

    //         // if ($make != null) {
    //         //     $query->where('make', $make);
    //         //     $global_make = $make;
    //         // }

    //         // if ($model != null) {
    //         //     $query->where('model', $model);
    //         // }




    //         // Use Cache::remember for final inventory results
    //         // $cacheDuration = 30; // Cache for 30 minutes



    //         // cache main data start here

    //         // if ($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide') {
    //         //     $cacheKey .= '_nationwide';
    //         //     $cacheResult = Cache::remember($cacheKey, $cacheDuration, function() use ($query) {
    //         //         return [
    //         //             'inventories' => $query->paginate(20),
    //         //             'message' => null
    //         //         ];
    //         //     });

    //         //     $inventories = $cacheResult['inventories'];
    //         //     $message = $cacheResult['message'];
    //         // } else {
    //         //     $cacheKey .= '_' . ($zipCode ?? 'nozip') . '_' . ($zip_radios ?? 'noradius');

    //         //     // We can't cache paginated results directly, so we'll cache the query preparation
    //         //     $zipCodeResult = Cache::remember($cacheKey, $cacheDuration, function() use ($zipCodeData) {
    //         //         return $this->getItemByDistance($zipCodeData['zip_code_data'], $zipCodeData['zip_radios_data'], $zipCodeData['query_data']);
    //         //     });

    //         //     $inventories = $zipCodeResult['inventories'];
    //         //     $message = $zipCodeResult['message'];
    //         // }

    //         // $filteredInventories = collect($inventories);
    //         // dd($filteredInventories);

    //         $inventories = MainInventory::whereIn('id', $matchingIds)->select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'inventory_status')
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
    //             })
    //             ->whereNotNull('main_inventories.price') // Add this line
    //             ->paginate(20);
    //             // ->where('main_inventories.price', '>', 1)->paginate(20);
    //         // $inventories = $inventories->paginate(20);

    //         $current_page_count = $inventories->count();
    //         $total_count = number_format($inventories->total());
    //         $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;

    //         $view = view('frontend.dealer_auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message'))->render();

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

    //     return view('frontend.dealer', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'stockId', 'dealer_name', 'id', 'cachefuel'));
    // }



    public function dealerinfo(Request $request, $param = null)
    {
        // 1. Strictly get VINs from the request
        $vinsString = $request->input('vins', '');
        $vinArray = !empty($vinsString) ? explode(',', $vinsString) : [];

        $clear = $request->input('clear');
        // $zipCode = $request->input('zip');   // no need for dealer ship method

        // Cookie::queue('zipcode', $zipCode, 60);

        if ($clear == 'flush') {

            Cookie::queue(Cookie::forget('searchData'));
            // Cookie::queue(Cookie::forget('zipcode'));
            return response()->json(['success' => 'clear']);
        }

        if ($clear == 'newCar') {
            Cookie::queue(Cookie::forget('searchData'));
            // Cookie::queue(Cookie::forget('zipcode'));
            return response()->json(['success' => 'newcar']);
        }


        // $radius = $request->input('radius', $zipCode ? 75 : null);   // no need for dealer ship method

        // this  old code start
        // $radius = $request->input('radius', 75); // Default to 75 miles

        // if (empty($request->zip)) {
        //     Cookie::queue(Cookie::forget('zipcode'));
        // }
        // this  old code end

        $searchBody = $request->query('homeBodySearch');
        // $inventores = Inventory::all();

        $vehicles = VehicleMake::orderBy('make_name')->where('status', 1)->pluck('id', 'make_name');

        $inventory_obj = MainInventory::query();
        $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
        $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();

        sort($vehicles_fuel_other);
        sort($vehicles_body);

        $price_max = (int)$inventory_obj->where('price', '!=', 'N/A')->max('price');
        $price_min = (int)$inventory_obj->where('price', '!=', 'N/A')->min('price');
        $miles_max = $inventory_obj->where('miles', '!=', 'N/A')->max('miles');
        $miles_min = $inventory_obj->where('miles', '!=', 'N/A')->min('miles');

        $global_make = '';

        $cookieData = '';
        if ($request->ajax()) {
            // if ($request->webRadios != 'Nationwide') {
            //     $zip_code_inform = $request->weblocationNewInput ?? $request->mobilelocation;


            //     // $city_name_data = $this->getCityByZipCode($zip_code_inform);
            //     $zip_code_info = $this->getCityByZipCodeOnCache($zip_code_inform);
            //     $city_name_data = $zip_code_info['county'];

            //     $json_file_name = str_replace(' ', '_', strtolower($city_name_data)) . '_county';

            //     if ($json_file_name == 'unknown_county') {
            //         $cacheFilePath = storage_path('app/bexar_county.json'); //san antomnio json
            //     } else {
            //         $search_city = $json_file_name . '.json';
            //         $cacheFilePath = storage_path('app/' . $search_city);
            //     }
            // } else {
            //     $cacheFilePath = storage_path('app/nationwide_county.json');
            // }

            // $radiusCalculation = $request->webRadios ?? $request->mobileRadios;

            // // Check if the file exists
            // if (file_exists($cacheFilePath) && $radiusCalculation < 200) {
            //     $cookieSetParam = $this->cookieSetParam($request);

            //     // $cachedDealers = $this->getCachedListings($cacheFilePath, $request);

            //     // Read the JSON file and decode it
            //     $jsonData = file_get_contents($cacheFilePath);
            //     $info = collect(json_decode($jsonData, true)); // Convert JSON to array

            //     $target_make = !empty($request->input('webMakeFilterMakeInput'))
            //         ? strtolower($request->input('webMakeFilterMakeInput'))
            //         : strtolower($request->input('secondFilterMakeInputNew', ''));

            //     $target_model = !empty($request->input('webModelFilterInput'))
            //         ? strtolower($request->input('webModelFilterInput'))
            //         : strtolower($request->input('secondFilterModelInputNew', ''));


            //     if (!empty($request->input('webBodyFilter'))) {
            //         // If webBodyFilter is not empty, use it
            //         $target_body = strtolower($request->input('webBodyFilter'));
            //     } elseif (!empty($request->input('mobileBody'))) {
            //         // If mobileBody is not empty, use it
            //         $target_body = strtolower($request->input('mobileBody'));
            //     } else {
            //         // If both are empty, use $cookieSetParam or set to null
            //         $target_body = $cookieSetParam['homebodySearch'] ?? null;
            //     }


            //     if (!empty($request->input('autoWebFuelCheckbox'))) {
            //         // Convert all selected fuels to lowercase and store as array
            //         $target_fuels = array_map('strtolower', $request->input('autoWebFuelCheckbox'));
            //     } elseif (!empty($request->input('autoMobileFuelCheckbox'))) {
            //         // Convert all selected fuels to lowercase and store as array
            //         $target_fuels = array_map('strtolower', $request->input('autoMobileFuelCheckbox'));
            //     } else {
            //         // If both are empty, use $cookieSetParam or set to null
            //         $target_fuel = $cookieSetParam['homefuelSearch'] ?? null;
            //         $target_fuels = $target_fuel ? [strtolower($target_fuel)] : [];
            //     }

            //     $target_exterior = !empty($request->input('autoWebExteriorColorCheckbox'))
            //         ? array_map('strtolower', (array) $request->input('autoWebExteriorColorCheckbox'))
            //         : array_map('strtolower', (array) $request->input('autoMobileExteriorColorCheckbox', []));

            //     $target_interior = !empty($request->input('autoWebInteriorColorCheckbox'))
            //         ? array_map('strtolower', (array) $request->input('autoWebInteriorColorCheckbox'))
            //         : array_map('strtolower', (array) $request->input('autoMobileInteriorColorCheckbox', []));

            //     // lkrejglkj gklerjgk lkgjsdflkgjlkdfgjlkd glkdf kjdlk glkdjfgk dflkgjdlkfg lkdfgj
            //     $drivetrain_info = !empty($request->input('autoWebDriveTrainCheckbox'))
            //         ? (array) $request->input('autoWebDriveTrainCheckbox')
            //         : (array) $request->input('autoMobileDriveTrainCheckbox', []);

            //     $target_drivetrain = $this->drivetrainMapping($drivetrain_info);

            //     $target_transmission = !empty($request->input('autoWebTransmissionCheckbox'))
            //         ? array_map('strtolower', (array) $request->input('autoWebTransmissionCheckbox'))
            //         : array_map('strtolower', (array) $request->input('autoMobileTransmissionCheckbox', []));

            //     $target_condition = !empty($request->input('autoWebConditionCheckbox'))
            //         ? array_map('strtolower', (array) $request->input('autoWebConditionCheckbox'))
            //         : array_map('strtolower', (array) $request->input('autoMobileTypeCheckbox', []));

            //     // **New Min/Max Filters**
            //     $min_price = $request->input('rangerMinPriceSlider', null) ?? $request->input('mobileRangerMinPriceSlider', null);
            //     $max_price = $request->input('rangerMaxPriceSlider', null) ?? $request->input('mobileRangerMaxPriceSlider', null);
            //     $min_mileage = $request->input('rangerMileageMinPriceSlider', null) ?? $request->input('mobileMileageRangerMinPriceSlider', null);
            //     $max_mileage = $request->input('rangerMileageMaxPriceSlider', null) ?? $request->input('mobileMileageRangerMaxPriceSlider', null);
            //     $min_year = $request->input('rangerYearMinPriceSlider', null) ?? $request->input('mobileYearRangerMinPriceSlider', null);
            //     $max_year = $request->input('rangerYearMaxPriceSlider', null) ?? $request->input('mobileYearRangerMaxPriceSlider', null);

            //     if ($request->webRadios != 'Nationwide') {
            //         $matchingData = $info->where('zip_code', $zip_code_inform);

            //         $latitude_infor = $zip_code_info['latitude'];
            //         $longitude_infor = $zip_code_info['longitude'];
            //         $zip_radios_inform = $request->webRadios ?? $request->mobileRadios;
            //     } else {
            //         $latitude_infor = null;
            //         $longitude_infor = null;
            //         $zip_radios_inform = null;
            //     }

            //     // Apply filters
            //     $filteredInfo = $info->filter(function ($data) use ($target_make, $target_model, $target_body, $target_fuels, $target_exterior, $target_interior, $target_drivetrain, $target_transmission, $target_condition, $min_price, $max_price, $min_mileage, $max_mileage, $min_year, $max_year, $latitude_infor, $longitude_infor, $zip_radios_inform) {


            //         // Ensure latitude and longitude exist before calculating distance
            //         if (!empty($latitude_infor) && !empty($longitude_infor) && !empty($data['latitude']) && !empty($data['longitude'])) {
            //             $distance = $this->calculateDistance($latitude_infor, $longitude_infor, $data['latitude'], $data['longitude']);
            //             if ($distance > $zip_radios_inform) {
            //                 return false;
            //             }
            //         }

            //         if (!empty($target_make) && !str_contains(strtolower($data['make']), $target_make)) {
            //             return false;
            //         }
            //         if (!empty($target_model) && !str_contains(strtolower($data['model']), $target_model)) {
            //             return false;
            //         }

            //         if (!empty($target_body) && !str_contains(strtolower($data['body_formated']), $target_body)) {
            //             return false;
            //         }

            //         // If fuel filters are set
            //         if (!empty($target_fuels)) {
            //             // Check if data fuel matches any of the target fuels
            //             $dataFuel = strtolower($data['fuel']);
            //             $matches = false;

            //             foreach ($target_fuels as $fuel) {
            //                 if (str_contains($dataFuel, $fuel)) {
            //                     $matches = true;
            //                     break;
            //                 }
            //             }

            //             if (!$matches) {
            //                 return false;
            //             }
            //         }

            //         if (!empty($target_exterior) && !in_array(strtolower($data['exterior_color']), $target_exterior)) {
            //             return false;
            //         }
            //         if (!empty($target_interior) && !in_array(strtolower($data['interior_color']), $target_interior)) {
            //             return false;
            //         }
            //         if (!empty($target_drivetrain) && !in_array(strtolower($data['drive_info']), $target_drivetrain)) {
            //             return false;
            //         }

            //         if (!empty($target_transmission) && !in_array(strtolower($data['transmission']), $target_transmission)) {
            //             return false;
            //         }
            //         if (!empty($target_condition) && !in_array(strtolower($data['type']), $target_condition)) {
            //             return false;
            //         }

            //         // **Min/Max Filters**
            //         if (!empty($min_price) && isset($data['price']) && $data['price'] < $min_price) {
            //             return false;
            //         }
            //         if (!empty($max_price) && isset($data['price']) && $data['price'] > $max_price) {
            //             return false;
            //         }
            //         if (!empty($min_mileage) && isset($data['miles']) && $data['miles'] < $min_mileage) {
            //             return false;
            //         }
            //         if (!empty($max_mileage) && isset($data['miles']) && $data['miles'] > $max_mileage) {
            //             return false;
            //         }
            //         if (!empty($min_year) && isset($data['year']) && $data['year'] < $min_year) {
            //             return false;
            //         }
            //         if (!empty($max_year) && isset($data['year']) && $data['year'] > $max_year) {
            //             return false;
            //         }
            //         return true;
            //     });


            //     // Apply sorting if requested
            //     if ($request->has('selected_sort_search')) {
            //         $sortMapping = [
            //             'datecreated|desc' => ['stock_date_formated', 'desc'],
            //             'datecreated|asc' => ['stock_date_formated', 'asc'],
            //             'searchprice|asc' => ['price', 'asc'],
            //             'searchprice|desc' => ['price', 'desc'],
            //             'mileage|asc' => ['miles', 'asc'],
            //             'mileage|desc' => ['miles', 'desc'],
            //             'modelyear|asc' => ['year', 'asc'],
            //             'modelyear|desc' => ['year', 'desc'],
            //             'payment|asc' => ['payment_price', 'asc'],
            //             'payment|desc' => ['payment_price', 'desc']
            //         ];

            //         if (isset($sortMapping[$request->selected_sort_search])) {
            //             [$sortField, $sortDirection] = $sortMapping[$request->selected_sort_search];

            //             $filteredInfo = $filteredInfo->sortBy(function ($item) use ($sortField) {
            //                 return $item[$sortField] ?? null;
            //             }, SORT_REGULAR, $sortDirection === 'desc');

            //             $filteredInfo = $filteredInfo->values();
            //         }
            //     }


            //     $message = '';
            //     $messageData = '!!!';
            //     $cookieData = Cookie::make('messageCookieData', $messageData, 60);
            //     // Paginate the data
            //     $page = $request->get('page', 1);
            //     $perPage = 20;
            //     $offset = ($page - 1) * $perPage;

            //     $paginatedItems = $filteredInfo->slice($offset, $perPage)->values();
            //     $inventories = new LengthAwarePaginator($paginatedItems, $filteredInfo->count(), $perPage, $page, [
            //         'path' => $request->url(),
            //         'query' => $request->query(),
            //     ]);

            //     $total_count = number_format($inventories->total());
            //     $current_page_count = $inventories->count();
            //     $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;

            //     $view = view('frontend.cache_auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message', 'messageData'))->render();

            //     return response()->json([
            //         'view' => $view,
            //         'pagination' => $inventories->links()->toHtml(),
            //         'total_count' => $total_count,
            //         'message' => $message
            //     ]);
            // } else {

            $messageData = '@';
            $cookieData = Cookie::make('messageCookieData', $messageData, 60);
            $infor = $this->directServerQuery($request, $request->vinData);
            $inventories = $infor['inventories'];
            $message = $infor['message'];
            // }

            $current_page_count = $inventories->count();

            $total_count = number_format($inventories->total());
            $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;

            $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message'))->render();
            return response()->json(['view' => $view, 'pagination' => $inventories->links()->toHtml(), 'total_count' => $total_count, 'message' => $message]);
        }



        $make_data = $request->input('make');
        $states = LocationState::orderBy('state_name')->pluck('state_name', 'id');
        $dealer_name = $request->name;

        $cachefuel = $vehicles_fuel_other;
        $messageCookieData = $request->cookie('messageCookieData');
        $h1Heading = $dealer_name . ' Listings';
        return view('frontend.dealer_auto', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'states', 'cachefuel', 'messageCookieData', 'vinArray', 'dealer_name', 'h1Heading'))->withCookie($messageCookieData);
    }


    protected function directServerQuery($request, $vinArray = null)
    {
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
        // $existingRecord = UserTrack::where([
        //     'ip_address' => $ip_address,
        //     'type' => $type,
        //     'title' => $homeInventorySearch,
        // ])->whereDate('created_at', $date)->exists();


        // $existingRecord = UserTrack::where([
        //     'ip_address' => $ip_address,
        //     'type' => $type,
        //     'title' => $homeInventorySearch,
        // ])
        //     ->whereDate('created_at', $date)
        //     ->first(['ip_address', 'type', 'title', 'created_at']);
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
        $query = $this->inventoryService->getItemByFilterOnly($request, $dealer_id);

        // dd($request->all());
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
        $location = $webLocation ??  $mobileLocation ?? null;
        //dd($location);

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
        // $query->where('price', '>', '1');
        $message = ''; // Initialize message variable

        $zip_location_data = null;

        // // if (!empty($vinArray)) {
        // //     $vinData = (array)$vinArray;
        // // }

        // $query = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'payment_price')
        //     ->with([
        //         'dealer' => function ($query) {
        //             $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id')
        //                 ->addSelect('id'); // Add id explicitly to avoid conflict
        //         },
        //         'additionalInventory' => function ($query) {
        //             $query->select('main_inventory_id', 'local_img_url')  // Only necessary columns
        //                 ->addSelect('id'); // Add id explicitly to avoid conflict
        //         },
        //         'mainPriceHistory' => function ($query) {
        //             $query->select('main_inventory_id', 'change_amount') // Only necessary columns
        //                 ->addSelect('id'); // Add id explicitly to avoid conflict
        //         }
        //     ])->whereIn('vin', $vinArray);

        $query = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'payment_price')
            ->with([
                'dealer' => function ($query) {
                    $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id')
                        ->addSelect('id');
                },
                'additionalInventory' => function ($query) {
                    $query->select('main_inventory_id', 'local_img_url')
                        ->addSelect('id');
                },
                'mainPriceHistory' => function ($query) {
                    $query->select('main_inventory_id', 'change_amount')
                        ->addSelect('id');
                }
            ])
            ->whereIn('vin', $vinArray);

            $priceRange = App::make('priceRange');
            $usedMinPrice = $priceRange['used']['minPrice'];
            $usedMaxPrice = $priceRange['used']['maxPrice'];

        // filter query added start here filter query added start here filter query added start here filter query added start here filter query added start here filter query added start here
        if (($request->rangerMinPriceSlider != null || $request->rangerMaxPriceSlider != null)) {
            $minValue = ($request->rangerMinPriceSlider != null) ? $request->rangerMinPriceSlider : $usedMinPrice;
            $maxValue = ($request->rangerMaxPriceSlider != null) ? $request->rangerMaxPriceSlider : $usedMaxPrice;

            // dd($request->rangerMileageMinPriceSlider, $request->rangerMileageMaxPriceSlider);
            if ($minValue > 450000) {
                $query->whereNotNull('price');
            } else {
                $query->whereBetween('price', [$minValue, $maxValue]);
            }
        }

        if ($request->rangerMileageMinPriceSlider != null || $request->rangerMileageMaxPriceSlider != null) {
            $minValue = ($request->rangerMileageMinPriceSlider != null) ? $request->rangerMileageMinPriceSlider : 0;
            $maxValue = ($request->rangerMileageMaxPriceSlider != null) ? $request->rangerMileageMaxPriceSlider : 300000;

            if ($maxValue < 300000) {
                // If the max value is less than 150000, use a normal between range query
                $query->whereBetween('miles', [$minValue, $maxValue]);
            } else {
                // If the max value is 150000 or more, show all vehicles with miles >= minValue
                $query->where('miles', '>=', $minValue);
            }
        }

        if ($request->rangerYearMinPriceSlider != null || $request->rangerYearMaxPriceSlider != null) {
            $minValue = ($request->rangerYearMinPriceSlider != null) ? $request->rangerYearMinPriceSlider : 1900;
            $maxValue = ($request->rangerYearMaxPriceSlider != null) ? $request->rangerYearMaxPriceSlider : 2025;

            $query->whereBetween('year', [$minValue, $maxValue]);
        }

        if ($request->autoWebFuelCheckbox != null) {
            // dd($request->autoWebFuelCheckbox);
            $fuelTypeMapping = [
                'Diesel' => ['Diesel', 'Diesel (B20 capable)'],
                'Electric' => ['Electric fuel type', 'BEV (battery electric vehicle)', 'MHEV (mild hybrid electric vehicle)', 'All-Electric'],
                'Flex Fuel' => ['Race Fuel', 'flex_fuel', 'Flexible Fuel', 'E85 Flex Fuel'],
                'Gasoline' => ['Plug-In/Gas', 'Gasoline Fuel', 'Gaseous', 'Gasoline fuel type', 'Gasolin Fuel', 'Gasoline', 'Regular Unleaded', 'Premium Unleaded', 'Gaseous Fuel Compatible', 'Ethanol'],
                'Hybrid' => ['Full Hybrid Electric (FHEV)', 'Electric Performance Hybrid', 'Hybrid Fuel', 'Gasoline/Mild Electric Hybrid', 'Hybrid'],
                'Hydrogen Fuel Cell' => ['Hydrogen Fuel Cell'],
                'Plug In Hybrid' => ['Plug-in Gas/Electric Hybrid', 'PHEV (plug-in hybrid electric vehicle)', 'Phev', 'Plug-In Hybrid'],
                'Compressed Natural Gas' => ['Natural Gas', 'Gas/CNG', 'Gasoline / Natural Gas', 'Compressed Natural Gas'],
                'Other' => ['Other', '–', '----', 'Unspecified']
            ];

            $selectedValues = $request->autoWebFuelCheckbox;
            $mappedFuelValues = [];

            foreach ($selectedValues as $value) {
                if (isset($fuelTypeMapping[$value])) {
                    $mappedFuelValues = array_merge($mappedFuelValues, $fuelTypeMapping[$value]);
                }
            }

            $mappedFuelValues = array_unique($mappedFuelValues);

            if ($request->has('allWebFuelName') && $request->allWebFuellName == 'allWebFuelValue') {
                // Logic for 'all' selection
            } else if ($request->has('autoWebFuelCheckbox')) {
                $query->whereIn('fuel', $mappedFuelValues);
            }
        }

        if ($request->autoWebExteriorColorCheckbox != null) {
            $query->where(function ($query) use ($request) {
                foreach ($request->autoWebExteriorColorCheckbox as $color) {
                    $query->orWhere('exterior_color', 'LIKE', '%' . $color . '%');
                }
            });
        }

        if ($request->autoWebInteriorColorCheckbox != null) {
            // dd($request->autoWebInteriorColorCheckbox);
            $exteriorColorMapping = [
                'Beigh' => ['Beige', 'Macchiato Beige/Black', 'Silk Beige/Black', 'Macchiato Beige/Space Gray', 'Teak/Light Shale', 'Silk Beige / Black', 'Chateau', 'Macchiato Beige', 'Amber Nappa', 'Macchiato Beige/Magma Gray', 'Atlas Beige', 'Chateau W/Linear Dark Mocha Wo', 'Jet/Tonal Stitch', 'Sand', 'Wicker Beige/Black', 'Pearl Beige', 'Sahara Beige w/Jet Black Accents', 'Cattle Tan/Black', 'Cardamom Beige', 'Glazed Caramel', 'Bisque', 'Cornsilk Beige', 'Dark Cashmere', 'Shetland Beige', 'Light Frost Beige/Black', 'Pistachio Beige', 'Ginger Beige/Espresso', 'Canberra Beige', 'Whisper Beige with Gideon accents', 'Siena Tan/Ebony/Siena Tan', 'Lt Oyster/Ebony/Lt Oyster', 'Ivory/Ebony/Ivory/Ivory', 'Pimento/Eb/Eb/Pim/Ebony', 'Shetland Beige & Black', 'Saiga Beige', 'Sand Beige', 'Stone Beige', 'Canberra Beige/Black', 'Wicker Beige/Global Black', 'Cornsilk Beige W/Brown', 'Parchment with Jet Black accents', 'Ginger Beige/Black', 'Silk Beige/Espresso Brown', 'Beige w/Nappa Leather Seati', 'Silk Beige', 'Light Oyster/Ebony', 'Pearl Beige W/Agate Gray', 'AMG Silk Beige', 'Whisper Beige w/Gideon Accents', 'Sonoma Beige Full Merino', 'Luxor Beige', 'Almond Beige/Mocha', 'Shale with Cocoa Accents', 'Light Oyster/Ebony/Ebony/Light Oyster', 'Silk Beige Black', 'Almond/Beige', 'Macchiato Beige/Grey', 'Tan / Red', 'Harvest Beige', 'Light Neutral w/Ebony Accents', 'Shara Beige', 'Black/Beige', 'Saddle/Black', 'Black/Limestone Beige', 'Cornsilk Beige / Brown', 'Beige/Black', 'Venetian Beige', 'Cornsilk Beige Leatherette Interior', 'Beige / Brown', 'Black/Lt Frost Beige', 'Macchiato Beige/Space Grey', 'Dark Beige', 'Beige/Black leatherette', 'Atlas Beige / Gray', 'Caraway/Ebony', 'Beige Leather Interior', 'Black/Luxor Beige', 'Beige / Black', 'Dark Beige/Titan Black', 'Beige Cloth Interior', 'Wicker Beige', 'Pimento / Ebony', 'Cattle Tan / Black', 'Cream Beige', 'Almond Beige', 'Atlas Beige/Light Carpet', 'Alpaca Beige Duragrain Interior', 'Sand Beige/Black', 'Savanna Beige', 'Cashmere and Ebony Leather Interior', 'Sahara Beige', 'Light Frost Beige/Mountain Brown', 'Canyon Brown/Lt Beige', 'Beige/Gray', 'Lt Frost Beige/Black', '2-Tone Black/Desert Beige', 'Shale With Ebony Interior Accents', 'Beige Tradizione', 'Atacama Beige/Basalt Black', 'Bei/Beige', 'Beige Two-tone', 'Black/Light Frost Beige Interior', 'Light Pebble Beige/Bark Brown Interior', 'Cirrus w/Dark Titanium Accents', 'Whisper Beige', 'Ginger Beige/Espresso Brown', 'Shale w/Cocoa Accents', 'Light Cashmere w/Medium Cashmere Accents', 'Velvet Beige', 'Ginger Beige', 'Light Pebble Beige/Bark Brown', 'Macchiato Beige / Black', 'designo Saddle Brown/Silk Beige', 'Veneto Beige', 'Mahogany/Silk Beige', 'Whisper Beige With Ebony Interior Accents', 'Havanna/Sand Beige', 'Soft Beige', 'Macchiato Beige/Espresso Brown', 'Cashmere Beige/Black', 'Pure Beige', 'Whisper Beige w/Ebony Accents', 'Dark Frost Beige/Medium Frost Beige', 'Whisper Beige seats with Ebony interior accents', 'Whisper Beige w/Jet Black Accents', 'Whisper Beige seats', 'Vintage Tan/Ebony', 'Desert Beige', 'Sahara Beige/Mocha', 'Light Beige', 'Cashmere/Ebony', 'Silk Beige/Black MB-Tex', 'Light Frost Beige/Canyon Brown', 'Almond/Ebony', 'Macchiato Beige / Bronze Brown Pearl', 'Whisper Beige W/ Ebony Accents', 'Velvet Beige / Brown', 'Glacier/Ebony/Glacier', 'Mojave Beige/Black', 'Caramel / Ebony', 'Beige/Tan', 'Light Frost Beige / Black', 'designo Macchiato Beige / Saddle Brown', 'Shetland Beige V-Tex Leatherette', 'designo Macchiato Beige', 'Calm Beige', 'Silk Beige/Espresso', 'Pebble Beige', 'Taupe/Pearl Beige', 'Glacier/Ebony', 'Mojave Beige', 'Macchiato Beige/Magma Grey', 'Macchiato Beige/Espresso', 'Coquina Beige', 'Macchiato Beige/Black MB-Tex', 'Macchiato Beige MB-Tex', 'ARTICO man-made leather macchiato beige / black', 'Lt Frost Beige/Mountain', 'Oxford Stone With Garnet Accents', 'Cashmere w/Cocoa Accents', 'Dark Frost Beige/Light Frost Beige', 'Macchiato Beige/Brown', 'Dune Beige', 'designo Silk Beige/Grey', 'Ceylon Beige', 'Beige leather cloth', 'Cornsilk Beige w/Brown Piping', 'Natural Beige/Black', 'Beige Cloth', 'Cornsilk Beige with Brown Piping', 'Beige cloth leather', 'Beige Velour', 'Sand Beige Leather', 'Light pebble beige Cloth', 'Light Beige - Leather', 'Light Oyster / Ebony', 'Lt Frost Beige/Brown', 'Sahara Beige Leather', 'Dark Beige/Black', 'Havanna/Sand Beige Leather', 'Soft Beige Leather', 'Parchment Beige', 'Choccachino/Ebony Accents', 'Luxor Beige/Saddle Brown Leath', 'Luxor Beige Leather', 'Frost Beige', 'Urban Ground/Beige', 'Canberra Beige/Black w/Contrast Stitching', 'Tan/Ebony/Tan/Ebony', 'Mountain Brown/Light Frost Beige', 'Zagora Beige', 'BEIGE LEATHER', 'Natural Beige / Black', 'Canyon Brown / Light Frost Beige', 'Macchiato Beige w/Diamond Stitching', 'Harvest Beige S', 'Leather Interior in Black/Luxor Beige', 'Leather Interior in Dark Night Blue/Limestone Beige', 'Standard Interior in Black/Luxor Beige', 'Two-Tone Exclusive Manufaktur Leather Interior in Graphite Blue and Choice of Color: Cream', 'Silk Beige / Espresso Brown', 'Latte/Ebony Stitch', 'Pimento/Eb/Pimento/Ebony', 'Whisper Beige with Gideon accent', 'Macchiato Beige / Space Grey Leather', 'Artico Almond Beige', 'Silk Beige/Expresso Brown Leather', 'Macchiato Beige Leather', 'Choccachino w/Cocoa Accents', 'Parchment Beige/Steel Gray Stitching', 'Beige Leatherette', 'Camel Beige', 'Pimento/Ebony/Ebony/Pimento/Cirrus', 'Mesa/Ebony', 'Sandstone Beige', 'Vanilla Beige', 'Light Frost Beige / Canyon Brown', 'Beige Connolly', 'Light Frost Beige', 'Rosewood/Ebony', 'Natural Beige', 'Dark Brown/Beige', 'Ivory/Ebony Stitch', 'Macchiato Beige / Black MB-Tex', 'Macchiato Beige / Black Leather', 'Khaki/Ebony', 'Macchiato Beige/Bronze Brown Pearl', 'Cashmere Beige', 'Beige/Brown', 'Polar Beige', 'Midrand Beige', 'Creme Beige', 'Shale with Brownston', 'Sahara Beige With Jet Black Accents', '2 Tone Beige AND Gray', 'Almond Beige/Black', 'Medium Pebble Beige / Cream', 'Dark Pebble Beige', 'Tuscan Beige'],
                'Black' => ['Dark Galvanized/Sky Cool Gray', 'Charcoal Nappa', 'Jet Black', 'Charcoal', 'Black', 'Dark Slate Gray', 'Blond With Black', 'Noir with Santorini Blue accents', 'Obsidian Rush', 'Macchiato/Black', 'Black/Alloy', 'AMG Sienna Brown/Black', 'Espresso Brown/Black', 'Jet Black with Jet Black accents', 'Jet Black with Red Accents', 'Amg Black Nappa', 'White w/Black', 'Ebony', 'Ebony Black', 'Jet Black w/Red Accents', 'Jet Black/Victory Red', 'Lunar Shadow (Jet Black/Taupe)', 'Black/Space Gray', 'Oyster Black', 'AMG Black', 'Light Platinum / Jet Black', 'Titan Black w/Red Stitching', 'Black w/Orange Stitching', 'Jet Black/Medium Dark Pewter', 'Black w/Linear Espresso Wood Trim', 'Black Pearl', 'Slate Black', 'Jet Black w/Kalahari Accents', 'Jet Black/Gray w/Blue Accents', 'Black/Black', 'Titan Black', 'Nougat Brown/Black', 'Global Black', 'Charcoal Black', 'Midnight Edition', 'Black/Rock Gray Stitching', 'Medium Dark Slate', 'Titan Black w/Blue', 'Jet Black/Dark Anderson Silver Metallic', 'Sea Salt/Black', 'Black/Alloy/Black', 'Black Onyx', 'Ebony/Dark Titanium', 'Oyster/Black', 'Black/White', 'Carbon Black', 'Black w/Brown', 'Blk Cloudtex &Clth', 'Black w/Rock Gray', 'Blk Lthette &Clth', 'Jet Black/Red Accents', 'Red/Black', 'Jet Black/Chai', 'Alloy/Black', 'Charcoal Black/Ebony', 'Black/Ivory', 'Black/Graphite', 'Ebony Bucket Seats', 'Black Sport Cloth40/Con/4', 'Nero (Black)', 'Black Leather', 'Black Dakota leather', "Black/Scarlet w/Shimamoku", "Ebony/Light Oyster Stitch", "Black w/Blue Stitch", "Black/Cloud Gray Fine Nappa premium leather", "Design Black Leather Interior", "Black w/Blue Stitching", "Black Cloth Interior", "Black Leather Interior", "Ebony w/Lunar Grey Stich", "Oyster/Ebony/Oyster", "Jet Black w/Jet Black Accents", "Black/Red", "Sakhir Orange/Black", "Sky Gray Jet Black With Santorini Blue Accents", "Sedona Sauvage With Jet Black Accents", "Dark Gray W/Black Onyx", "Black w/Hadori Aluminum", "Standard Interior in Black", "Leather Interior in Black", "Leather Package in Black", "Standard Interior in Black/ Mojave Beige", "Leather/Race-Tex Interior in Black with Red Stitching", "Leather Interior in Black/Bordeaux Red", "Leather Interior in Black/Pebble Grey", "Standard Interior in Black/Mojave Beige", "Leather Interior in Black/Alcantara® with Stitching in Platinum Grey", "Leather Package in Black with Deviated Stitching in Gentian Blue", "Two-Tone Exclusive Manufaktur Leather Interior in Black and Choice of Color: Cognac Brown", "Leather Interior in Black with Chalk Stitching with Checkered Sport-Tex Seat Centers", "Leather Package in Black/Bordeaux Red", "Black / Ceramic", "Ebony/Ebony/Ebony", "Black/Brown", "Ebony/Ebony/Ebony/Ivory", "Black Cloth", "Black w/Suede-Effect Fabric Seating", "Ebony/Ebony", "Ebony/Ebony/Ebony/Ebony", "Deep Garnet/Ebony", "Black/Bordeaux Red", "Black/Mojave Beige", "EBONY BLK UNIQUE CLOTH SEATS", "Caraway/Ebony/Caraway", "Gradient Black", "Black/Gray", "Ebony With Ebony Interior Accents", "Black/Tartufo", "Jet Black/Gray w/Red Accents", "Black / Graphite", "Ruby Red/Black", "Java/Black", "Black w/Red Stitching", "Black/New Saddle", "Jet Black/Kalahari", "Noir w/Santorini Blue Accents", "Jet Black/Mocha", "Ebony/Ebony/Ivory/Ivory", "Onyx Black - Semi Aniline", "Ebony W Windsor Seats", "Eclp/Ebony/Eclip/Ebony/Eb", "Black w/Leather Seating Surfaces w", "EBONY ACTIVEX MATRL SEATS", "Black w/Rock Gray Stitch", "BLACK SPORT CLOTH40/CON/40", "Black w/Black Top", "EBNY PART VNYL/CLTH&RED STITCH", "Ebony w/Light Oyster Inserts", "Black w/Nappa Leather Seating Surf", "EBONY BLK PERF LTH-TRM SEAT", "Black w/Leather Trimme", "Jet Black/Ceramic White Accents", "Cloth Bucket Seats or Black Sued", "Black w/Rock Gray Contrast Stitching", "Dark Galvanized/Ebony Accents", "Jet Black/Gray", "Demonic Red/Black", "Jet Black/Artemis", "Black w/Stitching", "Slate Black Leather", "Titan Black/Scalepaper Plaid", "Black w/MB-Tex Upholstery", "Titan Black w/Red Piping", "EBONY LTHR SEAT SURFACES", "designo Black", "EBONY ACTIVEX POWER SEATS", "BLACK STX CLOTH 40/CON/40", "F Sport Black", "EBONY ACTIVEX SEAT MATERIAL", "Black W/ Gray", "Black W/ Brown", "LINCOLN SOFT TOUCH EBONY", "CHARCOAL BLACK CLOTH SEATS", "EBONY PREMIUM CLOTH SEATS", "EBONY LUXURY LEATHER TRIM", "Black/Ash 2-Tone", "Jet Black/Light Titanium", "Jet Black/Nightshift Blue", "Galaxy Black", "EBONY LEATHER TRIM SEATS", "Wheat/Black", "Black/Light Graystone", "Black/Blue", "EBONY BLACK UNIQUE CLOTH", "Black w/Oyster Stitching", "Black/Sable Brown", "Ebony/Ebony/Ebony/Cirrus", "Piano Black", "Carbon Black Checkered", "Black w/Contrast Stitching", "Titan Black/Quartz", "Ebon/Ebony", "Black/Silverstone", "Ebony/Lunar", "Black / Red", "Black Nappa Leather", "Black Graphite", "LTHR-TRIM/VINYL BLACK SEATS", "Titan Black w/Blue Accents", "Black/Space Grey", "Ebony with Dark Plum interior accents", "Black w/Striated Black Trim", "Black/Excl. Stitch", "Ebony/Ebony/Mars Red", "Black / Magma Red", "Black w/Black Open Pore", "Satin Black", "Black Dakota w/Dark Oyster Highlight leather", "Titan Black w/Clark Plaid", "Obsidian Black w/Red", "Black/Sevilla Red", "EBONY PREM LEATHER TRIMMED", "EBONY CLOTH BUCKET SEATS", "BLACK SPORT CLOTH40/20/40", "EBONY LEATHER-TRIMMED SEATS", "2-Tone Black/Ceramique", "Black w/Oyster Contrast Stitching", "EBONY ACTIVEX TRIM SEATS", "EBONY ACTIVEX SEAT MTRL", "BLACK LTHR TRIM BUCKET SEAT", "BLACK SPORT 40/CONSOLE/40", "EBONY UNIQUE CLOTH SEATS", "designo Black/Black", "Obsidian Black", "Ebony Cloth Interior", "BLACK INT W/CARMELO LEATHER", "Black Anthracite", "Black Leatherette", "VINYL GRAY/BLACK SEATS", "EBONY/LT SLATE ACTIVEX SEAT", "Dark Titanium/Jet Black", "Off Black", "Black/Gun Metal", "Black/Red Leather", "Ebony/Ebony Accents", "Cloud/Ebony", "Black W/Grey Accents", "BLACK ACTIVEX/COPPER STITCH", "EBONY LEATHER", "EBONY LEATHER SEATS", "Ebony Trimmed Seats", "Ebony/Cirrus premium leather", "Black Dakota w/Contrast Stitching/Piping leather", "EBONY CLOTH SEATS", "Dark Charcoal w/Orange Accents", "Jet Black with Kalahari accents", "Black Kansas Leather", "Jet Black/Dark Ash", "Jet Black/Medium Titanium", "Jet Black/Titanium", "Black / Crescendo Red", "Charcoal Black/Cashmere Leather Interior", "Black / Ivory", "Black/Black/Black", "Titan Black / Quarzit", "Ebony premium leather", "EBONY ACTIVEX SEAT MATRL", "Black/Alcantara Inserts", "Jet Black/Jet Black", "VINYL BLACK SEATS", "EBONY LEATHER-TRIM SEATS", "Vintage Tan/Ebony/Ebony/Vintage Tan/Ebony", "Off-Black", "Black/Chestnut Brown Contrast Stitching", "Ebony / Ebony", "Chalk/Titan Black", "designo Black/Black Exclusive Nappa premium leather", "Black Mountain", "EBONY ROAST LEA-TRIM", "EBONY LEATHER SEAT SURFACES", "Ebony/Silver", "Ebony Black w/Red Accent Stitching", "Ebony Oxford Leather", "MINI Yours Carbon Black Lounge leather", "Jet Black/Jet Black Accents", "Black w/Red Accent Stitching cloth", "PREMIUM LEATHER EBONY", "Black w/Red", "Black/Dark Sienna Brown", "Black/Lizard Green", "Lounge Carbon Black leather", "Ebony Suede Leather", "Titan Black Leatherette Interior", "BLACK ONYX CLOTH SEATS", "Light Oyster/Light Oyster/Ebony", "Black Leather Seats", "Charcoal Black Leather Interior", "Black/Bordeaux", "Black / Gray", "Individual Platinum Black Full Merino Leather", "Morello Red w/Jet Black Accents", "BLACK LTHR TRIMMED BUCKET", "EBONY BLK UNIQUE CLOTH SEAT", "EBONY BLACK LTHR-TRIM SEATS", "Black SensaTec", "Jet Black / Dark Titanium", "Light Titanium/Ebony", "Black/Lava Blue Pearl Leather Interior", "Ebony cloth", "Black/Saddle Leather Interior", "Jet Black/Graystone", "Leather Package in Black/Garnet Red", "Leather Interior in Black with Checkered Sport-Tex Seat Centers", "Almond / Ebony", "Black w/Medium Dark Slate", "Black w/ Silver Crust", "Ebony Seats", "Black w/Blue", "Ebony/Medium Slate", "Blk/Black", "Blk/Grey", "Black/TURCHESE", "Black/Phoenix red"],
                'Blue' => ['Blue', 'Blue Stitching Leather', 'Navy/Beige', 'Blue w/StarTex Upholstery', 'Steel Blue', 'Night Blue/Black', 'Ultramarine Blue/Dune', 'Graphite Blue/Chalk', 'Indigo Blue', 'Ultramarine Blue', 'Fjord Blue', 'Night Blue/Dark Oyster', 'Raven Blue/Ebony', 'Admiral Blue/Light Slate', 'Sea Blue', 'Navy Blue', 'Admiral Blue', 'Coastal Blue', 'Blue/White', 'Yas Marina Blue', 'Navy w/Blue Stitching', 'Blue Gray', 'Blue Haze Metallic/Sandstorm', 'Rhapsody Blue', 'Deka Gray/Blue Highlight', 'Estoril Blue', 'Thunderbird Blue', 'Electric Blue', 'Meteor Blue', 'Deep Ocean Blue', 'Blue Bay / Sand', 'Nightshade Blue', 'Light Blue Gray', 'Navy/Gray', 'Gray/Blue', 'Graphite Blue', 'Marine Blue', 'Rhapsody Blue Recaro Seat', 'Yachting Blue', 'Blue / White', 'Medium Dark Flint Blue', 'Midnight Blue', 'Coastal Blue w/Alpine St', 'Raven Blue/Ebony Perforated Ultrafa', 'Dark Blue/Dune', 'Diamond Blue', 'ADMIRAL BLUE LEATHER SEATS', 'Leather Interior in Graphite Blue/Chalk', 'Leather Interior in Dark Night Blue/Chalk', 'Navy Pier W/Orange Stitch', 'ADMIRAL BLUE LT SLATE LEATH', 'Indigo Blue/Brown', 'Vivid Blue', 'Tension/Twilight Blue Dipped', 'Blue Agave', 'Midnight Blue With Grabber Blue Stitch', 'Navy/Harbour Grey', 'Charles Blue', 'Bugatti Light Blue Sport', 'Blue Accent', 'Neva Gray/Biscaya Blue', 'Aurora Blue', 'Light Blue', 'Indigo Blue / Brown', 'Deep Sea Blue/Silk Beige', 'Liberty Blue/Liberty Blue', 'Night Blue', 'Blue-Dark', 'Silver with Blue', 'Blue/Grey', 'Yacht Blue', 'Blue Leather', 'Blue Sterling', 'Deep Blue', 'Slate Blue', 'Tension Blue/Twilight Blue Dipped', 'Graphite Blue/Chalk Leather', 'Imperial Blue', 'Dark Pewter / Electric Blue', 'LAPIZ BLUE METALLIC BLUE', 'Dark Blue', 'Midnight Blue Grabber Blue Stitch', 'Brown/Indigo Blue', 'Neva Grey / Biscaya Blue MB-Tex', 'Blue & White', 'Metropol Blue', 'Aurora Blue / Alcantara', 'Royal Blue/Cream', 'Ocean Blue', 'Spectral Blue', 'Denim Blue', 'Klein Blue with Beluga', 'NAVY W/ORANGE', 'NAVY/ORANGE', 'Atlantic Blue', 'Beyond Blue', 'RHAPSODY BLUE RECARO', 'BLUE & TAN', 'TENSION/TWILIGHT BLUE DIPPED LEATHER', 'TWLIGHT BLUE', 'Tension Blue', 'Liberty Blue / Perlino', 'Saffron/Imperial Blue', 'PREM LTHR-TRMD BEYOND BLUE', 'Blue Haze', 'Aurora Blue/Electron Yellow', 'MANUFAKTUR Signature Yacht Blue', 'BLUE ACCENT / RECARO SEAT', 'Nightshift Blue', 'Dark Blue/Denim w/White Piping', 'Dk. Blue'],
                'Brown' => ["Brown", "Chestnut", "Saddle Brown", "Cognac", "Alpine Umber", "Nougat Brown", "Maroon Brown", "Tartufo", "Saddle", "Coffee", "Bahia Brown/Black", "Mocha", "Java", "Dark Brown", "Okapi Brown", "Espresso", "Tan", "Santos Brown", "Terracotta", "Caturra Brown", "Atmosphere/Brownstone", "Tan Leather", "Bahia Brown", "Saddle Brown Dakota Leather", "Touring Brown", "Brandy With Very Dark Atmosphere Accents", "Amaro Brown", "Espresso Brown", "Java Brown", "Santos Brown/Steel Gray Stitching", "Aragon Brown", "Brown/Beige", "Nut Brown/Black", "Kona Brown/Jet Black", "Kona Brown with Jet Black Accents", "Brandy w/Very Dark Atmosphere Accents", "AMG Saddle Brown", "Dark Saddle/Black", "Chestnut Brown", "Kona Brown Sauvage", "Saddle Brown/Black", "Dakota Saddle Brown Leath", "Marrakesh Brown", "designo Saddle Brown/Black", "Tartufo/Black", "Kona Brown / Jet Black", "DESERT BROWN TRIM", "Malt Brown", "Maroon Brown Perforated", "New Saddle/Black", "Nougat Brown / Black", "Beechwood/Off-Black", "Hazel Brown/Off-Black", "Brownstone/Jet Black", "Sienna Brown/Black", "Mauro Brown", "Saddle Brown / Black", "Balao Brown", "Cinnamon Brown Nevada Leather Interior", "Brown/Tan interior", "Chestnut Brown/Black", "Castano Dark Brown", "Saddle Brown Dakota w/Exclusive Stitching leather", "designo Light Brown", "Java Brown w/Tan", "Cinnamon With Jet Black Accents", "Glazed Caramel w/Black", "Dark Saddle / Black", "Moccasin/Black Contrast", "Noble Brown", "Taruma Brown", "Tera Excl Dalbergia Brown", "AMG Sienna Brown", "Portland Brown", "Sienna Brown", "Mountain Brown", "Kona Brown", "Shale / Brownstone", "Maroon Brown W Upholstery", "Cinnamon Brown", "Espresso Brown/Magma Grey", "Giga Brown/Carum Spice Gray", "Saddle Brown/Excl. Stitch", "Canyon Brown/Light Frost Beige", "Truffle Brown", "Maroon Brown/Havana Brown", "Desert Brown", "Ski Gray/Bark Brown", "Copper Brown/Atlas Grey", "Light Frost Brown", "Volcano Brown", "Ebony/Brown", "Tan/Brown", "Urban Brown/Glacier White", "Golden Brown", "White / Brown", "Tera Exclusive Dalbergia Brown", "Dark Brown/Ivory", "Tartufo Brown", "Vintage Brown", "Mud Gray/Terra Brown", "Dark Brown w/Grey Stitching", "Brown/Light Frost", "Canyon Brown/Lt Frost Beige", "Bark Brown/Ski Grey", "Cognac Lthr W/dark Brown", "Sable Brown / Neva Grey Nappa Leather", "Tan / Brown", "Tuscan Brown", "Saddle Brown w/Exclusive Stitching", "Tera Dalbergia Brown", "Mountain Brown/Light Mountain Brown", "Brown Leather", "Norias (Brown)", "Sarder Brown", "Bronze (Brown)", "Marsala Brown/Espresso", "Havana Brown", "Lt Mountain Brown/Brown", "Light Mountain Brown", "Brownstone", "Light Mountain Brown/Mountain Brown", "Cedar Brown", "Golden Oak", "Club Leather Interior in Truffle Brown/Cohiba Brown", "Bison Brown", "Sienna Brown MB-Tex", "Saddle Brown MB-Tex", "Espresso Brown MB-Tex", "Caramel/Ebony Accents", "Chestnut With Ebony Interior Accents", "Chestnut Brown/Ebony Accents", "Cognac/Dark Brown", "Dark Brown / Ivory", "Saddle Brown/Brown/Brown", "Brandy/Ebony Accents", "Shale with Brownstone accents", "Cognac w/Dark Brown Highlight", "Parchment w/Open-Pore Brown Walnut Trim", "Cognac w/Dk Brown Highlight", "Marrakesch Brown", "Brown w/Grey Topstitching", "Saddle Brown/Cream", "Taupe/Brown", "Madras Brown", "Noisette Brown", "Auburn Brown", "SIENNE BROWN", "Mocha Brown Leather", "Chaparral Brown", "Charcoal/Light Brown", "Saddle Brown/Dark Brown", "Espresso Brown/Magma Gray", "Dark Brown/Green", "Camel/Dark Green", "Brown / Beige", "Blk Vern Leath W/ Brown Stitch", "Marsala Brown/Espresso Brown", "Olea Club Leather in Truffle Brown", "Dark Brown w/Gray Stitching", "Giga Brown/Carum Spice", "Saddle Brown Leather", "Hazel Brown/", "Saddle Brown/Luxor Beige", "Brown/Pearl", "Brown/Lt Frost Beige", "Brownstone premium leather", "Bahia Brown Leather", "Saddle Brown/Excl. Stitch Leat", "Bahia Brown w/Grey Topstitching", "Kona Brown Sauvage Leather seats with mini-perfor", "Nut Brown / Espresso", "Brown 2-Tone", "Shale w/Brownstone a", "Audi BROWN", "Brown/Ebony", "Florence Brown", "designo Light Brown/", "Dark Sienna Brown/Bl", "Saddle Brown Br", "Marsala Brown", "STYLE Saddle Brown/B", "Kona Brown with Jet", "Cohiba Brown", "Hazel Brown", "Arabica Brown", "Palomino Brown", "Dk Brown w/Gray Stit", "Club Leather Interior in Truffle Brown", "Brown / Light Frost", "Nougat Brown Leather", "Wheat/Brown", "Brn/Brown", "Indigo/Dark Brown", "Arabica Brown/Almond White", "Sable Brown/Neva Grey", "Canyon Brown", "Brown Nv", "Truffle Brown/Cohiba", "Nutmeg Brown", "Cognac Brown", "Macchiato/Bronze Brown", "Dark Atmosphere/Loft Brown", "Earth Brown/Smoky Green", "Gray/Brown", "Ebony / Brown", "Natural Brown", "Dk. Brown", "Impala Brown", "Palomino w/ Open-Pore Brown Walnut Trim", "Palomino w/Wood Brown Trim", "Canyon Brown / Light Beige", "Pecan Brown", "Amarone Brown", "Brown / Indigo Blue", "Leather Exclusive Brown", "Dk Brown w/Gray Stitching", "Saddle Brown Nappa Leather", "Birch Nuluxe® With Open Pore Brown Walnut Trim (Premium)", "Dark Brown / Light Pebble Beige", "Sable Brown Pearl/Espresso Brown", "Palomino semi-aniline leather and Open-Pore Brown", "Espresso Brown 114", "Mocha w/ Orange Stitching", "Brown Bw", "Caturra Brown Kf2", "Gray w/Brown Bolsters", "Noisette Brown Leather", "Castano Brown", "Maroon Brown - RA30", "Maroon Brown - RC30", "LIGHT BROWN", "Club Leather Interior in Truffle Brown/Cohiba Brow", "Exclusive Manufaktur Interior in Cohiba Brown and", "Criollo Brown", "Brown DINAMICA w/Grey", "Porcelain/Espresso Brown", "Saddle Brown Dakota", "designo Auburn Brown", "Mauro Brown Vienna Leather", "Bison Brown/Mountain Brown", "Ebony/Brown w/Premium Leather", "Vermont Brown", "designo Saddle Brown", "brown/tan", "Leather Brown", "Brown Nappa Leather", "Truffle Brown/Cohiba Brown", "Khkc/Brown", "AMG Saddle Brown MB-Tex", "MANUFAKTUR Mahogany Brown / Macchiato Beige Exclusive Nappa Leather", "Dark Brown and Tan", "Espr Brown Perforated Veganza", "Murillo Brown", "Brown / Saddle Leather", "Portland Brown Full Leather Interior", "Club Leather Interior in Cohiba Brown", "Leather Interior in Saddle Brown/Luxor Beige"],
                'Gold' => ['Gold', 'Golden Oak & Black', 'Golden Oak/Black', 'Cream/Gold', 'Golden Oak & Black - CF', 'Agate Grey/Lime Gold'],
                'Gray' => ["Gray", "Cirrus", "Medium Gray", "Graphite", "Ash", "Dark Gray", "AMG Neva Gray", "Gray w/Yellow Stitching", "Grey", "Neva Grey/Black", "Espresso/Gray", "Wilderness Startex", "Titanium Gray", "Rock Gray", "Storm Gray", "Macchiato/Magmagray", "Diesel Gray/Black", "Ski Gray/Black", "Light Gray", "Titan Blk Clth", "Cement", "Slate Grey", "Steel Gray w/Anthracite", "Rotor Gray w/Anthracite", "Steel Gray w/Anthracite Stitching", "Gray/Black", "Grey/Carbon Black", "Graystone", "Steel Gray", "Cocoa/Dune", "Ash/Black", "Gideon/Very Dark Atmosphere", "Dark Slate 40 20 40", "Dark Charcoal", "Shale", "Dark Gray Leather Interior", "Gray Cloth Interior", "Aviator Gray", "Dark Galvanized Gray", "Sky Cool Gray", "Gray w/Orange Stitching", "Sky Gray With Santorini Blue Accents", "Rotor Gray", "Leather Interior in Agate Grey/Pebble Grey", "Sandstorm Gray w/Nappa Le", "Gray Cloth", "Slate Gray", "Cement Gray", "Gray Flannel", "Dark Ash Gray Sky Gray", "Dark Atmosphere/Medium Ash Gray", "Steel Grey", "Cocoa/ Light Ash Gray", "Medium Ash Gray", "Gray w/Leatherette Seating Surface", "Macchiato/Magma Grey", "Dark Galvanized Sky Gray", "Titanium Grey/Black", "Adelaide Grey", "Medium Earth Gray", "DARK GRAY CLOTH 40CONSOLE40", "Grey Flannel", "Lunar Gray", "Silverstone/Black", "Agate Gray", "Titanium Grey Pearl", "Dark Ash with Jet Black Interior Accents", "Greige/Black", "DARK PALAZZO GRAY VINYL", "DARK PALAZZO GRAY CLOTH", "PRFM GRAY ACTIVEX SEAT MTRL", "Medium Ash Gray/Jet Black", "Gray 2-Tone", "Dark Earth Gray", "Neva Gray/Sable Brown", "Grey/Blue", "Agate Grey", "Neva Grey/Sable Brown", "Ash Gray/Glacier White", "Magma Grey/Black", "Dark Atmosphere/ Medium Ash Gray", "MED GRAY CLOTH 40CONSOLE40", "Rock Gray/Gray", "Rock Gray / Black", "Diesel Gray / Black", "Gray / Black", "Dk Khaki/Lt Graystone", "SPACE GRAY ACTIVEX TRIM SEA", "Stonegray", "Gray Cloudtex & Cloth", "Rotor Gray w/Anthracite Stitching", "Gray w/Blue Bolsters", "Dark Palazzo Gray", "Earth Gray", "DARK EARTH GRAY CLOTH SEATS", "Light Blue Gray/Black", "Gray Dakota Leather", "Dark Walnut/Dark Ash Grey", "Medium Slate Gray", "LTH-TRM/VINYL GRAY/NAVY STS", "Dark Slate Gray/Med Slate Gray", "Light Titanium/Dark Titanium Accents", "Dark Space Gray", "Medium Greystone/Dark Slate", "Dark Gray/Camel", "Gray/Dark Gray Leather Interior", "Fog Gray 2-Tone", "Satellite Grey", "Dark Slate Gray/Medium Slate Gray Cloth Interior", "Birch w/Black Open Pore", "Giga Cassia/Spice Gray", "Metro Gray", "French Roast/Black", "Diesel Grey/Black", "Palazzo Grey", "Medium Titanium/Jet Black", "Ebony seats with Slate interior accents", "Grayblack", "Dark Earth Gray cloth", "Gray MB Tex", "Marble Gray", "Stratus Gray", "Dark Slate Grey/Med Slate Grey", "Standard Interior in Slate Grey", "Light Space Gray", "Gray/Green 3-Tone", "Dark Ash Gray/Sky Cool Gray", "Neva Gray", "Ebony w/Red Accent Stitching", "Alpine Gray", "Satellite Gray", "Space Gray", "Cashmere grey/Phoenix Red", "Creme Light/Black Accent", "Ash/Gray", "Dark/Medium Slate Gray", "Light Blue Gray / Black", "Magma Grey", "Pastel Slate Gray", "Gray / Silver", "Mega Carum Spice Gray", "Dark Walnut/Dark Ash Gray", "Quartz Gray", "Cloud Gray", "Dark Slate/Medium Graystone", "Crystal White/Silver Gray Pearl", "Medium Flint Gray", "Dark Galvanized /Sky Cool Gray", "Dark Space Gray w/ Navy Pier", "Dark Walnut/Very Dark Ash Gray", "Monaco Gray", "DARK PALAZZO GREY VINYL", "Medium Slate Gray Leather Cloth D5", "Gray / Dark Gray", "Slate/Graystone", "BLK W/GRAY INSERTS", "Dark Gray/Vanilla", "Dark Slate Gray Interior", "Dark Ash Gray", "Gray/Metallic", "Jade Gray", "Gray w/Pure White", "Rock Gray / Granite Gray", "Gray / White", "Lt Stone W/Gray Piping", "Gray (yth)", "Dark Gray w/Navy Pier", "Crystal Gray", "Light Titanium W/Ebony Accents", "Misty Gray", "Dark Walnut/Dark Ash Grey Forge Perforated Leather Seating Surf", "Montana Gray", "Gray/Beige", "Charcoal Gray", "Dark Slate Gray/Medium Slate Gray", "Magma Gray", "Agate Grey / Pebble Grey", "GRAY CLOTH 40/20/40", "Gray leather", "Ebony w/Smoke Gray", "MEDIUM GRAY CLOTH 40/20/40", "Medium Earth Gray cloth", "Silverstone II Atlas Grey", "Diesel Gray", "Grey Fabric", "Gray Leatherette", "Charcoal/Misty Gray", "Dark Gray/Onyx", "Standard Interior in Agate Grey", "Leather Interior in Slate Gray/Chalk", "Hai Gray", "Melange/Light Gray", "Two-Tone Gray Cloth", "Cocoa/Light Ash Gray", "Palazzo Gray", "Platinum Grey", "Neva Grey", "Dark Grey", "Leather Interior in Slate Grey", "Phantom Gray", "Mega Carum Spice Grey/Carum Spice Grey", "Agate Grey/Pebble Grey", "Medium Slate Gray/Light Shale", "Gravity Gray", "Grey 40 Console 40", "Moonstone/Grey Flannel", "Natural Gray", "Medium Slate Gray cloth leather", "Light Ash Gray/Ceramic White", "Greystone", "Graphite Grey", "Pebble Grey", "Neva Grey / Sable Brown MB-Tex", "Neva Grey / Biscaya Blue Leather", "Light Ash Gray", "Dark Walnut / Dark Ash Grey", "Lt Gray", "Dove Grey", "Radar Red/Dark Slate Gray", "Graphite Gray", "Stone Gray/Raven", "Dark Gray/Med Gray", "Dk/Lt Slate Gray", "Platinum Gray", "Dark Slate Gray/Saddle Tan", "Dark Palazzo Grey", "Dark Slate Gray/Medium Slate Gray Cloth Bucket", "Medium Ash Gray Premium cloth seat trim", "Flint Gray", "GREY CLOTH 40/20/40", "Dark/Light Slate Gray", "Dk/Med Slate Gray", "Med Slate Gray", "Dark Khaki/Light Graystone", "Gray / Blue", "Gray cloth leather", "Giga Cassia/Carum Spice Grey", "Gray with Orange Stitching", "Light Grey", "PALAZZO GREY CLOTH SEATS", "Gray Partial Leather", "Coral and Gray", "Gray - GR", "Pando Gray", "Dark Slate Gray Cloth", "Md Slate Gray/Lt Shale", "Art Gray", "Storm Gray Leatherette", "GYT/GRAY", "Dark Slate Gray / Medium Slate Gray", "AMG Neva Grey", "Ash / Gray", "Metropolis Gray", "Two-Tone Gray", "Cinder Grey/Ebony", "Crystal Grey", "Scivaro Gray", "Dark Ash Gray / Light Ash Gray", "Dark Graystone/Medium Graystone", "Moonrock Gray", "Everest Gray", "Light Gray Leather", "Cognac w/Granite Gray", "Dark Graystone / Medium Graystone", "Neva Grey/Biscaya Blue"],
                'Green' => ["Green", "Light Argento Metallic/Sage Green", "Green / Beige", "Forest Green/Beige", "Dark Green", "Sage Green", "Evergreen", "Green Pearlcoat", "GREEN / BLACK", "Carbon Black/GREEN", "Urban Green", "Rialto Green", "Agave Green", "Cactus Green", "Cumbrian Green", "Pine Green", "Nero Ade w/ Green and Orange", "Mori Green", "Sage Green w/Lime Accents", "Shadow Green", "Light green", "Dark Green/Glacier White", "Cumbrian Green Hide", "Forest Green", "Moss Green", "Dark Green 2-Tone"],
                'Orange' => ["Orange", "Orange Stitching w/Cloth Upholst", "Orange Stitching Leather", "Kyalami Orange/Black", "Saffrano Orange & Black", "Kyalami Orange", "Inferno Orange", "Sakhir Orange", "Orange/White", "SAKHIR ORANGE/BLK", "Orange Accent", "Burnt Orange", "Orange Zest", "CODE ORANGE", "LT.ORANGE"],
                'Pink' => ['Pink', 'Peony Pink', 'Club Pink Plaid/Black'],
                'Purple' => ['Purple', 'Dark Auburn With Jet Black Accents', 'Garnet Seats With Ebony Interior Accents', 'Q Deep Purple', 'Purple Silk'],
                'Red' => ["Red", "Bengal Red", "Tacora Red", "Bengal Red/Black", "Circuit Red", "Classic Red", "Red Amber", "Rioja Red", "Mars Red/Ebony/Mars Red", "Cockpit Red", "Red w/Ultra Suede Perforated SofTe", "Red Leather", "Red Stitch Leather", "Magma Red", "Burgundy Red", "Fiona Red", "Fiona Red/Black", "Flare Red", "Red & Zegna", "AMG Cranberry Red/Black", "Carmine Red/Black", "Exclusive Carmine Red/Black", "Tacora Red w/Contrast Stitching", "Classic Red/Black", "AMG Power Red/Black", "Garnet Red/Black", "Cranberry Red", "Arras Red", "Adrenaline Red", "Cranberry Red/Black", "Red/Black Bicolor", "Red Pepper/Black", "Mars Red/Flame Red Stitch", "Coral Red/Black", "Circuit Red w/Hadori Aluminum", "EBONY ACTIVEX/RED STITCHING", "Ruby Red", "Bordeaux Red", "Coral Red Dakota Leather", "Bordeaux Red/Black", "Red / Black", "Dream Red Leather Interior", "Charcoal w/Lava Red Stit", "Jet / Red", "Dark Ruby Red", "Porcelain/Titian Red", "Salsa Red", "Redwood", "Barcelona Red", "Circuit Red/Dark Graphite", "Charcoal w/Lava Red Stitch", "Red/tan", "Red/Tan/Yellow", "Rioja Red w/Dark Graphite Aluminum", "Crimson Red", "Monaco Red/Graphite", "Bk/Wred", "Red Merino", "Sevilla Red", "Spice Red", "Charcoal w/Lava Red", "designo Mystic Red", "Red / Tan", "Express Red", "Red Pepper", "Mahogany Red", "Ceramic White With Red Stitching", "Sporting Red", "Monaco Red", "Red Leather Interior", "Flamenco Red Metallic", "AMG Power Red", "AMG Cranberry Red", "Mugello Red", "RED NAPPA AND RED CARPET", "Mars Red / Ebony", "Red Oxide", "Jet / Jet / Redzone", "Ebony w/Red Stitch", "Red Recaro Nismo Leather", "Manufaktur Classic Red", "Circuit Red w/Scored Aluminum Trim", "Coral Red Boston Leather", "Showstop Red Lthr Trim Pw", "Garnet Red", "Ebony w/Red Stitching", "ADRENALINE RED FRONT LEATHER SEATING SURFACES", "Adrenaline Red Dipped", "Hotspur Red", "TACORA RED SENSATEC", "Charcoal / Red", "Charcoal w/Lava Red St", "BELUGA/RED", "Bordeaux Red w/ Chalk Stitching", "Commissioned Collection Phoenix Red", "Ebony/Red/Red Stitch", "MSO Bespoke Red", "Morello Red", "CARMINE RED LEATHER-TRIMMED", "Torch Red", "Custom White / Red Carbon Leathe", "Charcoal/Red Nappa Leather", "EBNY PART VNYL/CLTH&RED STI", "EBONY LTHR TRIM/RED STITCH", "Phoenix Red/arctic white", "Macchiato/Lounge Red", "Fox Red Novillo", "Berry Red", "Graphite with Red Stitch", "Consort Red", "Vermilion Red", "Red Accent", "Pimento Red/Ebony Inserts & Stitch", "Commissioned Collection Mugello Red", "Full Red", "Venom Red", "Circuit Red w/ Hadori Aluminum Trim", "Imola Red", "Phoenix Red", "Ebony With Red Stitching", "Ardent Red", "Red Leather Seating Surfaces", "White/Red", "Coral Red", "Red Rock", "Rioja Red w/Silver Performance Trim", "Red with Jet Stitching and Red Interior", "Chancellor Red", "Ebony with Mars Red Stitching", "Ebony/Mars Red", "Ebony/Pimento Red with Pimento Red Stitching", "Showstopper Red", "Brick Red/Cashmere Accents", "Signal Red", "Show Stopper Red", "Circuit Red w/Hadori Aluminum Trim", "Spicy Red", "Ebony/Eternal Red", "Carmine Red", "F-Sport Rioja Red", "Rioja Red w/Dark Graphite", "MAHGNY RED LTH SEAT SURFACE", "Circuit Red W/Hadori Alum", "Dark Knight/Spicy Red", "Flame Red Clearcoat", "Circut Red", "All Eclipse Red", "Mars Red/Ebony/Mars Red Leathe", "Cockpit Red leather", "Mars Red/Mars Red/Ebony", "Carrera Red", "Marsala Red", "Ebony/Red Stitch", "Checkered", "Red w/Amman Stitching", "Carbon and Martian Rock Red", "Pillar Box Red", "RED/BL", "Ebony w/Mars Red Stitch", "Pimento Red / Ebony / Ebony", "Anthracite/Red Leather", "Adrenaline Red/Jet B", "Red Line", "Circuit Red W/Satin Chrom", "Chill Red", "Fox Red", "Bengel Red", "Tacora Red Perforated SensaTec", "Burgundy Red perforated and quilted Veganza", "Chancellor Red/Ivory Leather", "Dk Charcoal/Red", "Two-Tone Exclusive Manufaktur Leather Interior in Bordeaux Red and Choice of Color: Cream", "Leather Interior in Bordeaux Red", "Redzone/Redzone Stitch", "Tacora Red w/Contrast Stitching/Piping", "Arras Red Design Selection", "Express Red Fine Nappa Leather", "NH-883PX/RED", "Vermillion Red", "Red/Red", "Circuit Red/F Aluminum", "Morello Red Dipped", "TORRED CLEARCOAT [RED]", "Ebony DuoLeather seats with Mars Red stitch", "Sevilla Red Two-Tone", "Red Nv", "F Sport Rioja Red", "Lords Red", "Claret Red", "Oxide Red", "Brick Red", "Red / Cream", "Flare Red With", "Circuit Red Nul", "Rioja Red Nulux", "Circut Red w/Satin Chrome Trim", "Ebony / Mars Red", "Ferrari Red", "Red Pepper Manufaktur Sin", "Charcoal & Red", "VELVET RED PEARLCOAT", "Ebony / Pimento Red / Ebony", "Circuit Red NuLuxe and Hadori Aluminum", "Amber Red", "Brick Red / Cashmere", "Circuit Red w/Naguri Aluminum Trim", "Merlot Red", "Red/White", "Red Mica", "BLK/RED", "Ceramic White w/Red Stitching", "RedL", "Sevilla Red 2-Tone", "KING RANCH RED BUCKET SEATS", "DESIGNO BENGEL RED", "Magma Red w/Anthracite Stitching", "Burgundy Red Perforated Veganz", "Pimento Red", "Cranberry Red Leather", "Cayenne Red", "EBONY W/ RED STITCH", "EBONY/ RED STITCHING", "EBONY W/RED STITCH ACCENTS", "Brick red & cashmere", "Deep Red", "Color to Sample Red & White", "REDWOOD VENETIAN LEATHER", "BENGAL RED NAPPA", "Red - RE", "Mars Red/Ebony/Mars Red - 301YG", "ADRENALIN RED", "Redwood Leather", "Sport Red", "Red Kj3", "SHOWSTOP RED LTHR-TRIM PWR", "Red Td1", "Red (td1)", "Adrenalin Red - 704", "Demonic Red Laguna Leather", "Red (d3l)", "Exclusive Carmine Red", "Rioja Red Eb33", "SHOWSTOPPER RED RECARO LTHR", "Rioja Red - EA21", "Lounge Championshipred", "Red Copper", "Charcoal with Lava Red Stitch with Front & Rear Leather Seat Trim (1st / 2nd Rows)", "Pure Red", "Mars Red with Flame Red Stitching", "Adrenaline Red Napa leather seating surfaces with", "Divine Red", "GTS Interior Package in Carmine Red", "MANUFAKTUR Signature Carmine Red", "AMG Classic Red / Black Exclusive Nappa Leather", "Boxster Red", "Onyx/Red", "Arras Red Valcona", "Dark Red", "Flare Red w/Ginsumi", "CHARCOAL VENTIRED NAPPA L", "Ebony/Flame Red Stitch", "Red & Zegna w/Amman", "designo Bengal Red", "White & Red", "Infrared"],
                'Silver' => ['Silver', 'Steel', 'Platinum', 'Silverstone', 'Light Platinum/Jet Black', 'Light Platinum w/Jet Black Accents', 'Silver w/Silver Trim', 'SILVER', 'Lunar Silver', 'Silverstone/Coffee', 'Dark Pewter w/Silver Trim', 'Silverstone/Vintage Coffee', 'Silver w/Blue Trim', 'IGNOT SILVER', 'Dark Pewter / Silver', 'Symphony Silver', 'Pyrite Silver Metallic', 'Manufaktur Cry Wte/Silver', 'LIQUID SILVER METALLIC S', 'Ingot Silver Metallic', 'Silver Pearl', 'Silverstone Sensafin', 'Sil/silver', 'Rhodium Silver', 'BLADE SILVER METALLIC', 'Silver / Blue', 'Silver with Silver Trim', 'Silver Bison', 'Shimmering Silver Pearl', 'Pastel Silver'],
                'White' => ['White', 'Blond', 'Ivory', 'Warm Ivory', 'Parchment', 'Medium Stone', 'Ivory/Ebony/Ivory/Ebony', 'Light Oyster', 'White/Black', 'Cashmere', 'Ivory Lth', 'Ceramic White', 'Almond', 'Ivory White', 'Gallery White', 'Smoke White', 'Designo Diamond White Metallic Vinyl', 'Tafeta White', 'Ivory White/Night Blue', 'Ivory White/Black', 'Off White', 'Deep White/Black', 'Opal White', 'Melange/Lt Gray', 'Glacier White', 'WHITE/BLK', 'Arctic White', 'Bespoke White', 'designo Platinum White', 'White Sands/Espresso', 'White Leather', 'Parchment White', 'Summit White', 'White/Ivory', 'White/Brown', 'Platinum White Pearl', 'Graphite/White Stitching', 'Ultra White', 'Giga Ivory White', 'Vianca White', 'Macchiato/Magmagrey', 'Grace White', 'GRACE WHITE / BLACK / PEONY PINK', 'Turchese/ARCTIC WHITE', 'TAILORED PURPLE/GRACE WHITE/BLACK', 'GRACE WHITE/COBALTO BLUE', 'Ivory White/Dark Oyster', 'Grace White/peony pink', 'Crystal White', 'AMG Neva Grey MB-Tex', 'White Sands', 'FUJI WHITE', 'F SPORT White', 'White Ivory Leather', 'Sea Salt/White', 'Platinum White', 'White / Tan', 'Grace White/Havana', 'White/Peppercorn', 'Pure White', 'Bright White Clearcoat', 'Ivory White/Atlas Grey', 'White Premium leath', 'Neva White/Magma Grey', 'Neva White/Magma Grey MB-Tex', 'White Frost Tricoat', 'Glacier White w/Copper', 'Opal White/Amaro Brown', 'Oxford White', 'Ivory White Nappa', 'Whi/White', 'designo Platinum White Pearl / Blac', 'Q Glacier White', 'Champagne/Blue', 'White w/Satin Chrome Trim', 'Ivory White Nappa Leather', 'Eminent White Pearl', 'Super White', 'Polar White', 'Ivory White w/Dark Oyster Highlight', 'White or Light Beige', 'Ivory White Ext Merino Leather', 'Veganza Perforated Qlt Smoke White', 'VCFU Smoke White Extended Merino L', 'Mandarin with Grace White', 'Grace White with Pine Green', 'White/white', 'MACCHIATO/ MAGMAGREY', 'Smoke White/Night Blue', 'White and blue', 'Macchiato/Magmagrey - 115', 'White D1s', 'offwhite leather', 'Ceramic White Pearl', 'White by Mulliner', 'Ivory White w/ Contrast Stitching', 'Nero-White Stitching', 'Graphite/White cloth', 'Ivory White Extended Merino Leather', 'Ivory White/Night Blue Extended Merino Leather', 'Cream (Off-White)', 'Steam (White)', 'Glacier White - GLW', 'Commissioned Collection Arctic White', 'Pearl White', 'White Diamond Pearl', 'Ivory White/Atlas Gray'],
                'Yellow' => ['Yellow', 'Amber', 'Yellow Stitching w/StarTex Uphols', 'Yellow w/Yellow Trim', 'Forge Yellow'],
                'Other' => ['Other', '–', '----', 'Unspecified']
            ];

            $selectedInteriorColorValues = $request->autoWebInteriorColorCheckbox;
            $mappedwebInteriorColorValues = [];

            foreach ($selectedInteriorColorValues as $value) {
                if (isset($exteriorColorMapping[$value])) {
                    $mappedwebInteriorColorValues = array_merge($mappedwebInteriorColorValues, $exteriorColorMapping[$value]);
                }
            }

            $mappedwebInteriorColorValues = array_unique($mappedwebInteriorColorValues);

            if ($request->has('allWebInteriorColorName') && $request->allWebInteriorColorName == 'allWebInteriorColorValue') {
                // Logic for 'all' selection
            } else if ($request->has('autoWebInteriorColorCheckbox')) {
                $query->whereIn('interior_color', $mappedwebInteriorColorValues);
            }
        }

        if ($request->has('autoWebConditionCheckbox')) {
            $selectedTypes = $request->autoWebConditionCheckbox;
            // dd($selectedTypes);
            $types = [];

            if (in_array('Certified', $selectedTypes)) {
                // $types[] = 'Certified Preowned';
                $types[] = 'preowned certified';
            }
            if (in_array('Preowned', $selectedTypes)) {
                $types[] = 'Used';
            }
            if (in_array('New', $selectedTypes)) {
                $types[] = 'New';
            }

            // Apply the query only if there are selected types
            if (!empty($types)) {
                $query->whereIn('type', $types);
            }
        }

        if ($request->autoWebTransmissionCheckbox != null) {
            if ($request->has('allWebTransmissionlName') && $request->allWebTransmissionlName == 'allWebTransmissionValue') {
            } else if ($request->has('autoWebTransmissionCheckbox')) {
                $Web_transmissions = $request->autoWebTransmissionCheckbox;
                $query->where(function ($subQuery) use ($Web_transmissions) {
                    foreach ($Web_transmissions as $transmission_info) {
                        if (trim($transmission_info) == 'automatic') {
                            $subQuery->orWhere('transmission', 'LIKE', '%automatic%')
                                ->orWhere('transmission', 'LIKE', '%variable%');
                        } else {
                            $subQuery->orWhere('transmission', 'LIKE', '%' . trim($transmission_info) . '%');
                        }
                    }
                });
            }
        }
        if ($request->autoWebDriveTrainCheckbox != null) {
            // Mapping array
            $driveTypeMapping = [
                '4WD' => ['Four-wheel Drive', '4WD'],
                'AWD' => ['All-wheel Drive', 'AWD'],
                'FWD' => ['Front-wheel Drive', 'FWD'],
                'RWD' => ['Rear-wheel Drive', 'RWD'],
                'Other' => ['Unknown', '–']
            ];

            $selectedValues = $request->autoWebDriveTrainCheckbox;
            $mappedValues = [];

            // Map selected checkboxes to database values
            foreach ($selectedValues as $value) {
                if (isset($driveTypeMapping[$value])) {
                    $mappedValues = array_merge($mappedValues, $driveTypeMapping[$value]);
                }
            }

            // Remove duplicate values
            $mappedValues = array_unique($mappedValues);

            // Debugging the mapped values
            // dd($mappedValues);

            if ($request->has('allWebDriveTrainlName') && $request->allWebFuellName == 'allWebDriveTrainValue') {
                // Your existing logic for this condition
            } else if ($request->has('autoWebDriveTrainCheckbox')) {
                // Apply the mapped values to your query
                $query->whereIn('drive_info', $mappedValues);
            }
        }

        if ($request->webMakeFilterMakeInput != null) {
            Cookie::queue(Cookie::forget('searchData'));
            $searchData = ['webMakeFilterMakeInput' => $request->webMakeFilterMakeInput];
            Cookie::queue('searchData', json_encode($searchData), 120);
            $query->where('make', $request->webMakeFilterMakeInput);
        }
        if ($request->webModelFilterInput != null) {
            $query->where('model', $request->webModelFilterInput);
        }
        if ($request->totalLoanAmountCalculation != null) {
            $format_price  = intVal(str_replace(',', '', $request->totalLoanAmountCalculation));
            $query->whereBetween('payment_price', [0, $format_price]);
        }

        if ($request->webBodyFilter != null) {
            $web_body = $request->webBodyFilter;
            $query->Where('body_formated', 'LIKE', '%' . $web_body . '%');
        }

        $sortMapping = [
            'datecreated|desc' => ['stock_date_formated', 'desc'],
            'datecreated|asc' => ['stock_date_formated', 'asc'],
            'searchprice|asc' => ['price', 'asc'],
            'searchprice|desc' => ['price', 'desc'],
            'mileage|asc' => ['miles', 'asc'],
            'mileage|desc' => ['miles', 'desc'],
            'modelyear|asc' => ['year', 'asc'],
            'modelyear|desc' => ['year', 'desc'],
            'payment|asc' => ['payment_price', 'asc'],
            'payment|desc' => ['payment_price', 'desc']
        ];



        // //Cookie::queue('selected_sort_search',$request->selected_sort_search, 120);
        // Session::put('selected_sort_search', $request->selected_sort_search);

        if (isset($sortMapping[$request->selected_sort_search])) {
            $query->orderBy($sortMapping[$request->selected_sort_search][0], $sortMapping[$request->selected_sort_search][1]);
        }


        // filter query added end  here filter query added end  here filter query added end  here filter query added end  here filter query added end  here filter query added end  here

        // $query->where('price', '>', '1');
        // $zipCodeData = [
        //     'zip_code_data' => $zipCode,
        //     'zip_radios_data' => $zip_radios,
        //     'query_data' => $query,
        // ];

        $inventories = $query->paginate(20);

        // if ($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide') {
        //     $message = null;
        //     $inventories = $query->paginate(20);
        // } else {
        //     $result = $this->getItemByDistance($zipCodeData['zip_code_data'], $zipCodeData['zip_radios_data'], $zipCodeData['query_data']);
        //     $inventories = $result['inventories'];
        //     $message = $result['message'];
        // }

        return ['inventories' => $inventories, 'message' => $message];
    }


    // public function dealerinfo(Request $request, $stockId, $dealer_name, $id = null)
    // {

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
    //     $searchBody = $request->query('homeBodySearch');
    //     // $inventores = Inventory::all();
    //     $vehicles_obj = VehicleMake::query();

    //     $vehicles = $vehicles_obj->where('status', 1)->get();
    //     $inventory_obj = MainInventory::query();
    //     $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
    //     $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();

    //     sort($vehicles_fuel_other);
    //     sort($vehicles_body);

    //     $price_max = $inventory_obj->max('price');
    //     $price_min = $inventory_obj->min('price');
    //     $miles_max = $inventory_obj->max('miles');
    //     $miles_min = $inventory_obj->min('miles');
    //     $global_make = '';

    //     if ($request->ajax()) {

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
    //         $existingRecord = UserTrack::where([
    //             'ip_address' => $ip_address,
    //             'type' => $type,
    //             'title' => $homeInventorySearch,
    //         ])->whereDate('created_at', $date)->exists();


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

    //         $query = $this->inventoryService->getItemByFilter($request, $id);

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
    //         if ($webLocation != null) {
    //             queueZipCodeCookie($webLocation);
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

    //         // $query->where('deal_id', $id);

    //         $message = ''; // Ensure $message is always defined

    //         $zipCode = $zipCode ?? '';
    //         if ($zipCode != null) {
    //             try {
    //                 if ($zip_radios != null) {
    //                     $countryCode = 'us';
    //                     $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
    //                     $response = file_get_contents($url);
    //                     $zip_location_data = json_decode($response, true);

    //                     if (isset($zip_location_data['results'][0]['geometry'])) {
    //                         $latitude = $zip_location_data['results'][0]['geometry']['lat'];
    //                         $longitude = $zip_location_data['results'][0]['geometry']['lng'];
    //                         $cityName = $zip_location_data['results'][0]['components']['city'] ?? '';

    //                         $zipCodeQuery = clone $query;
    //                         $zipCodeQuery->selectRaw(
    //                             "*, (3959 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"
    //                         );

    //                         $zipCodeQuery->having('distance', '<=', $zip_radios);

    //                         // Ensure the result is paginated
    //                         $zipCodeInventories = $zipCodeQuery->get();

    //                         if ($zipCodeInventories->isEmpty()) {
    //                             $inventories = $query->paginate(20);
    //                             $message = '
    //                             <section style="padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px" class="sptb2">
    //                                 <div style="border-radius:5px" class="container bg-white p-5">
    //                                     <div class="text-center">
    //                                         <h3 class="mb-2">No exact matches within the radius.</h3>
    //                                         <p class="mb-2">Modify your filters or click "Save Search" to be notified when more matches are available.</p>
    //                                         <a href="#" class="mb-2 clearfilterAjax" style="text-decoration:underline;font-weight:bold;font-size:15px" id="clearfilterAjax">Clear all filters.</a>
    //                                     </div>
    //                                 </div>
    //                             </section>';
    //                         } else {
    //                             $inventories = $zipCodeQuery->paginate(20);
    //                         }
    //                     }
    //                 } else {
    //                     // When no radius filter is applied
    //                     $zipCodeQuery = clone $query;
    //                     $zipCodeQuery->where('zip_code', $zipCode);
    //                     $zipCodeInventories = $zipCodeQuery->get();

    //                     if ($zipCodeInventories->isEmpty()) {
    //                         $inventories = $query->paginate(20);
    //                         $message = '
    //                             <section style="padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px" class="sptb2">
    //                                 <div style="border-radius:5px" class="container bg-white p-5">
    //                                     <div class="text-center">
    //                                         <h3 class="mb-2">No exact matches.</h3>
    //                                         <p class="mb-2">Modify your filters or click "Save Search" to be notified when more matches are available.</p>
    //                                         <a href="#" class="mb-2 clearfilterAjax" style="text-decoration:underline;font-weight:bold;font-size:15px" id="clearfilterAjax">Clear all filters.</a>
    //                                     </div>
    //                                 </div>
    //                             </section>';
    //                     } else {
    //                         // Ensure the result is paginated
    //                         $inventories = $zipCodeQuery->paginate(20);
    //                     }
    //                 }
    //             } catch (\Exception $e) {
    //                 // Handle any exceptions that occur during the API call or query building
    //                 $message = 'An error occurred while processing your request. Please try again.';
    //                 $inventories = $query->paginate(20); // Fallback to default pagination
    //             }
    //         } else {
    //             // No zip code provided, paginate default query
    //             $inventories = $query->paginate(20);
    //         }

    //         // inventory numberf calculation start here
    //         $current_page_count = $inventories->count();
    //         $total_count = number_format($inventories->total());
    //         $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;
    //         // inventory numberf calculation end  here
    //         // dd($inventories, $total_count, $single_inventories_count,$message,$stockId,$dealer_name,$id);  1000
    //         $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message', 'stockId', 'dealer_name', 'id'))->render();
    //         return response()->json(['view' => $view, 'pagination' => $inventories->links()->toHtml(), 'total_count' => $total_count, 'message' => $message]);
    //     }

    //     $make_data = $request->input('make');
    //     $cachefuel = MainInventory::distinct()->pluck('fuel')->toArray();
    //     return view('frontend.dealer', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'stockId', 'dealer_name', 'id', 'cachefuel'));
    // }


    // old dealerinfo data start here

    // public function dealerinfo(Request $request, $stockId, $dealer_name, $id = null)
    // {

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
    //     $searchBody = $request->query('homeBodySearch');
    //     // $inventores = Inventory::all();
    //     $vehicles_obj = VehicleMake::query();

    //     $vehicles = $vehicles_obj->where('status', 1)->get();
    //     $inventory_obj = MainInventory::query();
    //     $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
    //     $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();

    //     sort($vehicles_fuel_other);
    //     sort($vehicles_body);

    //     $price_max = $inventory_obj->max('price');
    //     $price_min = $inventory_obj->min('price');
    //     $miles_max = $inventory_obj->max('miles');
    //     $miles_min = $inventory_obj->min('miles');
    //     $global_make = '';

    //     if ($request->ajax()) {

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
    //         $existingRecord = UserTrack::where([
    //             'ip_address' => $ip_address,
    //             'type' => $type,
    //             'title' => $homeInventorySearch,
    //         ])->whereDate('created_at', $date)->exists();


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


    //         $mainInventory = MainInventory::select(
    //             'id', 'year', 'make', 'model', 'price', 'miles',
    //             'price_rating', 'zip_code', 'latitude', 'longitude',
    //             'payment_price', 'type', 'inventory_status'
    //         )
    //         ->with([
    //             'dealer' => function ($query) {
    //                 $query->select('id', 'dealer_id', 'name', 'state', 'brand_website',
    //                                'rating', 'review', 'phone', 'city', 'zip', 'role_id');
    //             },
    //         ])
    //         ->where(function ($query) {
    //             $query->where('inventory_status', '!=', 'Sold')
    //                 ->orWhereNull('inventory_status'); // Include NULL values
    //         })
    //         ->whereNotNull('main_inventories.price') // Ensure price is not null
    //         ->whereHas('dealer', function ($query) use ($id) { // Correct filtering for dealer
    //             $query->where('id', $id);
    //         });

    //         $query = $this->inventoryService->getItemByFilter($request, $id, $mainInventory);
    //         dd($query->get());
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
    //         if ($webLocation != null) {
    //             queueZipCodeCookie($webLocation);
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

    //         // $query->where('deal_id', $id);

    //         $message = ''; // Ensure $message is always defined

    //         $zipCode = $zipCode ?? '';
    //         if ($zipCode != null) {
    //             try {
    //                 if ($zip_radios != null) {
    //                     $countryCode = 'us';
    //                     $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
    //                     $response = file_get_contents($url);
    //                     $zip_location_data = json_decode($response, true);

    //                     if (isset($zip_location_data['results'][0]['geometry'])) {
    //                         $latitude = $zip_location_data['results'][0]['geometry']['lat'];
    //                         $longitude = $zip_location_data['results'][0]['geometry']['lng'];
    //                         $cityName = $zip_location_data['results'][0]['components']['city'] ?? '';

    //                         $zipCodeQuery = clone $query;
    //                         $zipCodeQuery->selectRaw(
    //                             "*, (3959 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"
    //                         );

    //                         $zipCodeQuery->having('distance', '<=', $zip_radios);

    //                         // Ensure the result is paginated
    //                         $zipCodeInventories = $zipCodeQuery->get();

    //                         if ($zipCodeInventories->isEmpty()) {
    //                             $inventories = $query->paginate(20);
    //                             $message = '
    //                             <section style="padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px" class="sptb2">
    //                                 <div style="border-radius:5px" class="container bg-white p-5">
    //                                     <div class="text-center">
    //                                         <h3 class="mb-2">No exact matches within the radius.</h3>
    //                                         <p class="mb-2">Modify your filters or click "Save Search" to be notified when more matches are available.</p>
    //                                         <a href="#" class="mb-2 clearfilterAjax" style="text-decoration:underline;font-weight:bold;font-size:15px" id="clearfilterAjax">Clear all filters.</a>
    //                                     </div>
    //                                 </div>
    //                             </section>';
    //                         } else {
    //                             $inventories = $zipCodeQuery->paginate(20);
    //                         }
    //                     }
    //                 } else {
    //                     // When no radius filter is applied
    //                     $zipCodeQuery = clone $query;
    //                     $zipCodeQuery->where('zip_code', $zipCode);
    //                     $zipCodeInventories = $zipCodeQuery->get();

    //                     if ($zipCodeInventories->isEmpty()) {
    //                         $inventories = $query->paginate(20);
    //                         $message = '
    //                             <section style="padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px" class="sptb2">
    //                                 <div style="border-radius:5px" class="container bg-white p-5">
    //                                     <div class="text-center">
    //                                         <h3 class="mb-2">No exact matches.</h3>
    //                                         <p class="mb-2">Modify your filters or click "Save Search" to be notified when more matches are available.</p>
    //                                         <a href="#" class="mb-2 clearfilterAjax" style="text-decoration:underline;font-weight:bold;font-size:15px" id="clearfilterAjax">Clear all filters.</a>
    //                                     </div>
    //                                 </div>
    //                             </section>';
    //                     } else {
    //                         // Ensure the result is paginated
    //                         $inventories = $zipCodeQuery->paginate(20);
    //                     }
    //                 }
    //             } catch (\Exception $e) {
    //                 // Handle any exceptions that occur during the API call or query building
    //                 $message = 'An error occurred while processing your request. Please try again.';
    //                 $inventories = $query->paginate(20); // Fallback to default pagination
    //             }
    //         } else {
    //             // No zip code provided, paginate default query
    //             $inventories = $query->paginate(20);
    //         }

    //         // inventory numberf calculation start here
    //         $current_page_count = $inventories->count();
    //         $total_count = number_format($inventories->total());
    //         $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;
    //         // inventory numberf calculation end  here
    //         // dd($inventories, $total_count, $single_inventories_count,$message,$stockId,$dealer_name,$id);  1000
    //         $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message', 'stockId', 'dealer_name', 'id'))->render();
    //         return response()->json(['view' => $view, 'pagination' => $inventories->links()->toHtml(), 'total_count' => $total_count, 'message' => $message]);
    //     }

    //     $make_data = $request->input('make');
    //     $cachefuel = MainInventory::distinct()->pluck('fuel')->toArray();
    //     return view('frontend.dealer', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'stockId', 'dealer_name', 'id', 'cachefuel'));
    // }
    // old dealerinfo data end here
}
