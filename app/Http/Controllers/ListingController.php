<?php

namespace App\Http\Controllers;

use App\Interface\InventoryServiceInterface;
use App\Service\DealerService;
use App\Models\LocationCity;
use App\Models\LocationState;
use App\Models\LocationZip;
use App\Models\MainInventory;
use App\Models\UserTrack;
use App\Models\VehicleMake;
use App\Traits\Notify;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class ListingController extends Controller
{
    use Notify;

    // Redis key constants
    const REDIS_ZIP_COUNTY_KEY = 'inventory:zip_county_data';
    const REDIS_NATIONWIDE_KEY = 'inventory:nationwide_county';
    const REDIS_COUNTY_PREFIX = 'inventory:county:';
    const REDIS_COUNTY_IDS_PREFIX = 'inventory:county_ids:';
    const REDIS_FILTER_PREFIX = 'inventory:filters:';
    const REDIS_VEHICLE_PREFIX = 'inventory:vehicle:';
    const COOKIE_CACHE_TIME_SIX_MONTH = 6 * 30 * 24 * 60;

    protected $cacheTime;

    public function __construct()
    {
        // $this->cacheTime = now()->addRealHours((int)env('REDIS_CACHE_TIME', 24));
        $this->cacheTime = (int)env('REDIS_CACHE_TIME', 24) * 3600;
        $this->initializeRedisData();
    }

    private function initializeRedisData()
    {
        if (!Redis::exists(self::REDIS_FILTER_PREFIX . 'makes')) {
            $this->refreshFilterData();
        }
    }

    public function refreshFilterData()
    {
        $startTime = microtime(true);
        $cacheTimeSeconds = $this->cacheTime; // 24 hours in seconds

        // Delete existing keys to prevent type conflicts
        Redis::del([
            self::REDIS_FILTER_PREFIX . 'makes',
            self::REDIS_FILTER_PREFIX . 'body_types',
            self::REDIS_FILTER_PREFIX . 'fuel_types',
            self::REDIS_FILTER_PREFIX . 'price_range',
            self::REDIS_FILTER_PREFIX . 'mileage_range',
            self::REDIS_NATIONWIDE_KEY
        ]);

        // Store makes as a set WITH expiration
        $makesKey = self::REDIS_FILTER_PREFIX . 'makes';
        $makes = VehicleMake::orderBy('make_name')->where('status', 1)->pluck('make_name');
        Redis::sadd($makesKey, ...$makes);
        Redis::expire($makesKey, $cacheTimeSeconds);

        // Store body types as a set WITH expiration
        $bodyTypesKey = self::REDIS_FILTER_PREFIX . 'body_types';
        $bodyTypes = MainInventory::distinct()->pluck('body_formated')->toArray();
        Redis::sadd($bodyTypesKey, ...$bodyTypes);
        Redis::expire($bodyTypesKey, $cacheTimeSeconds);

        // Store fuel types as a set WITH expiration
        $fuelTypesKey = self::REDIS_FILTER_PREFIX . 'fuel_types';
        $fuelTypes = MainInventory::distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();
        sort($fuelTypes);
        Redis::sadd($fuelTypesKey, ...$fuelTypes);
        Redis::expire($fuelTypesKey, $cacheTimeSeconds);

        // Store price range as a hash WITH expiration
        $priceRangeKey = self::REDIS_FILTER_PREFIX . 'price_range';
        $priceRange = [
            'max' => (int)MainInventory::where('price', '!=', 'N/A')->max('price'),
            'min' => (int)MainInventory::where('price', '!=', 'N/A')->min('price')
        ];
        Redis::hmset($priceRangeKey, $priceRange);
        Redis::expire($priceRangeKey, $cacheTimeSeconds);

        // Store mileage range as a hash WITH expiration
        $mileageRangeKey = self::REDIS_FILTER_PREFIX . 'mileage_range';
        $mileageRange = [
            'max' => MainInventory::where('miles', '!=', 'N/A')->max('miles'),
            'min' => MainInventory::where('miles', '!=', 'N/A')->min('miles')
        ];
        Redis::hmset($mileageRangeKey, $mileageRange);
        Redis::expire($mileageRangeKey, $cacheTimeSeconds);

        $nationwideKey = self::REDIS_NATIONWIDE_KEY;
        $vehicles = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'body_formated', 'fuel', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'transmission')
            ->with([
                'dealer' => function ($query) {
                    $query->select('id', 'dealer_id', 'name', 'state', 'city', 'zip');
                },
                'additionalInventory' => function ($query) {
                    $query->select('id', 'main_inventory_id', 'local_img_url');
                },
                'mainPriceHistory' => function ($query) {
                    $query->select('id', 'main_inventory_id', 'change_amount');
                }
            ])->get();

        // Clear existing nationwide data
        Redis::del($nationwideKey);

        foreach ($vehicles as $vehicle) {
            $vin = $vehicle->vin;
            // Add to nationwide set
            Redis::sadd(self::REDIS_NATIONWIDE_KEY, $vin);

            // Store vehicle details in hash WITH expiration
            $vehicleKey = self::REDIS_VEHICLE_PREFIX . $vin;
            Redis::hmset($vehicleKey, $vehicle->toArray());
            Redis::expire($vehicleKey, $cacheTimeSeconds);


            // Index by make WITH expiration
            $makeKey = self::REDIS_FILTER_PREFIX . 'make:' . strtolower($vehicle->make);
            Redis::sadd($makeKey, $vin);
            Redis::expire($makeKey, $cacheTimeSeconds);

            // Index by model WITH expiration
            $modelKey = self::REDIS_FILTER_PREFIX . 'model:' . strtolower($vehicle->model);
            Redis::sadd($modelKey, $vin);
            Redis::expire($modelKey, $cacheTimeSeconds);

            // Index by body type WITH expiration
            $bodyKey = self::REDIS_FILTER_PREFIX . 'body:' . strtolower($vehicle->body_formated);
            Redis::sadd($bodyKey, $vin);
            Redis::expire($bodyKey, $cacheTimeSeconds);

        }
        // Set expiration on the nationwide key
        Redis::expire($nationwideKey, $cacheTimeSeconds);

        Log::info('Filter data refreshed in Redis with expiration', [
            'execution_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            'cache_time_seconds' => $cacheTimeSeconds
        ]);
    }

    private function setCacheExpiration($key, $expireTime = null)
    {
        $expireTime = $expireTime ?? $this->cacheTime;
        Redis::expire($key, $expireTime);
    }

    public function auto(Request $request, $param = null)
    {
        if ($clear = $request->input('clear')) {
            return $this->handleClearRequest($clear);
        }

        $searchData = $this->getSearchDataFromRedis();

        if ($zipcode = $request->input('weblocationNewInput') ?? $request->input('mobilelocation')) {
            Cookie::queue('zipcode', $zipcode, self::COOKIE_CACHE_TIME_SIX_MONTH);
        }

        if ($request->ajax()) {
            return $this->handleAjaxRequest($request);
        }

        return $this->showAutoListView($request, $searchData);
    }

    private function getSearchDataFromRedis(): array
    {
        try {
            // Initialize default values
            $data = [
                'make_data' => [],
                'vehicles_body' => [],
                'vehicles_fuel' => [],
                'price_range' => ['min' => 0, 'max' => 100000],
                'mileage_range' => ['min' => 0, 'max' => 200000],
                'states' => LocationState::orderBy('state_name')->pluck('state_name', 'id'),
                'messageCookieData' => request()->cookie('messageCookieData')
            ];

            // Check and get makes
            $makesKey = self::REDIS_FILTER_PREFIX . 'makes';
            if (Redis::exists($makesKey) && Redis::type($makesKey) === 'set') {
                $data['make_data'] = Redis::smembers($makesKey) ?: [];
            }

            // Check and get body types
            $bodyTypesKey = self::REDIS_FILTER_PREFIX . 'body_types';
            if (Redis::exists($bodyTypesKey) && Redis::type($bodyTypesKey) === 'set') {
                $data['vehicles_body'] = Redis::smembers($bodyTypesKey) ?: [];
            }

            // Check and get fuel types
            $fuelTypesKey = self::REDIS_FILTER_PREFIX . 'fuel_types';
            if (Redis::exists($fuelTypesKey) && Redis::type($fuelTypesKey) === 'set') {
                $data['vehicles_fuel'] = Redis::smembers($fuelTypesKey) ?: [];
            }

            // Check and get price range
            $priceRangeKey = self::REDIS_FILTER_PREFIX . 'price_range';
            if (Redis::exists($priceRangeKey) && Redis::type($priceRangeKey) === 'hash') {
                $priceData = Redis::hgetall($priceRangeKey);
                if (!empty($priceData)) {
                    $data['price_range'] = [
                        'min' => $priceData['min'] ?? $data['price_range']['min'],
                        'max' => $priceData['max'] ?? $data['price_range']['max']
                    ];
                }
            }

            // Check and get mileage range
            $mileageRangeKey = self::REDIS_FILTER_PREFIX . 'mileage_range';
            if (Redis::exists($mileageRangeKey) && Redis::type($mileageRangeKey) === 'hash') {
                $mileageData = Redis::hgetall($mileageRangeKey);
                if (!empty($mileageData)) {
                    $data['mileage_range'] = [
                        'min' => $mileageData['min'] ?? $data['mileage_range']['min'],
                        'max' => $mileageData['max'] ?? $data['mileage_range']['max']
                    ];
                }
            }

            return $data;
        } catch (\Exception $e) {
            Log::error('Failed to get search data from Redis: ' . $e->getMessage());

            // Fallback to database if Redis fails
            return $this->getSearchDataFromDatabase();
        }
    }

    private function handleClearRequest(string $clearType)
    {
        Cookie::queue(Cookie::forget('searchData'));
        return response()->json([
            'success' => $clearType === 'flush' ? 'clear' : 'newcar'
        ]);
    }

    private function handleAjaxRequest(Request $request)
    {
        $startTime = microtime(true);
        $queryCacheKey = $this->generateQueryCacheKey($request);

        if ($cachedResult = Cache::get($queryCacheKey)) {
            Log::info('Using cached query result', ['cache_key' => $queryCacheKey]);
            return $cachedResult;
        }

        $isNationwide = $request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide';
        $result = $isNationwide
            ? $this->handleRedisNationwideSearch($request)
            : $this->handleRedisCountySearch($request);

        Cache::put($queryCacheKey, $result, $this->cacheTime);

        Log::info('Inventory Query Performance', [
            'execution_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
            'query_type' => $isNationwide ? 'nationwide' : 'county',
            'cache_key' => $queryCacheKey
        ]);

        return $result;
    }

    private function handleRedisNationwideSearch(Request $request)
    {
        $allVins = Redis::smembers(self::REDIS_NATIONWIDE_KEY);

        if (empty($allVins)) {
            return $this->handleDirectDatabaseQuery($request);
        }

        $filteredVins = $this->applyRedisFilters($request, $allVins);
        $totalCount = count($filteredVins);

        if ($totalCount === 0) {
            return response()->json([
                'view' => $this->generateNoMatchMessage('nationwide'),
                'pagination' => '',
                'total_count' => 0,
                'message' => 'No matches found'
            ]);
        }

        $page = $request->page ?? 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $paginatedVins = array_slice($filteredVins, $offset, $perPage);

        // Get full vehicle data from database for the paginated VINs
        $vehicles = MainInventory::whereIn('vin', $paginatedVins)
            ->with([
                'dealer' => function ($query) {
                    $query->select('id', 'dealer_id', 'name', 'state', 'city', 'zip');
                },
                'additionalInventory' => function ($query) {
                    $query->select('id', 'main_inventory_id', 'local_img_url');
                },
                'mainPriceHistory' => function ($query) {
                    $query->select('id', 'main_inventory_id', 'change_amount');
                }
            ])
            ->get()
            ->map(function ($vehicle) {
                // Convert to array and ensure proper naming
                $data = $vehicle->toArray();

                // Handle relationships
                $data['dealer'] = $vehicle->dealer ? $vehicle->dealer->toArray() : null;
                $data['additional_inventory'] = $vehicle->additionalInventory->toArray();
                $data['main_price_history'] = $vehicle->mainPriceHistory->toArray();

                // Convert back to object if needed (depends on your view)
                return (object)$data;
            });

        $paginator = new LengthAwarePaginator(
            $vehicles,
            $totalCount,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->formatAjaxResponse($paginator, $request);
    }


    private function handleRedisCountySearch(Request $request)
    {
        $locationData = $this->getLocationDataForSearch($request);
        $countyKey = self::REDIS_COUNTY_PREFIX . str_replace(' ', '_', strtolower($locationData['county']));

        if (!Redis::exists($countyKey) || Redis::type($countyKey) !== 'set') {
            return $this->handleDirectDatabaseQuery($request);
        }

        $countyVins = Redis::smembers($countyKey);

        if (empty($countyVins)) {
            return $this->handleDirectDatabaseQuery($request);
        }

        $filteredVins = $this->applyRedisFilters($request, $countyVins);
        $totalCount = count($filteredVins);

        if ($totalCount === 0) {
            return response()->json([
                'view' => $this->generateNoMatchMessage($request->radius ?? 75),
                'pagination' => '',
                'total_count' => 0,
                'message' => 'No matches found in this area'
            ]);
        }

        $page = $request->page ?? 1;
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $paginatedVins = array_slice($filteredVins, $offset, $perPage);

        $vehicles = [];
        foreach ($paginatedVins as $vin) {
            if ($vehicleData = Redis::hget($countyKey, $vin)) {
                $vehicles[] = json_decode($vehicleData, true);
            }
        }

        $paginator = new LengthAwarePaginator(
            $vehicles,
            $totalCount,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->formatAjaxResponse($paginator, $request);
    }

    private function applyRedisFilters(Request $request, array $initialVins): array
    {
        $filterSets = [];
        $filteredVins = $initialVins;

        // Make filter
        if ($make = $this->getFilterValue($request, ['webMakeFilterMakeInput', 'secondFilterMakeInputNew'])) {
            $makeKey = self::REDIS_FILTER_PREFIX . 'make:' . strtolower($make);
            if (Redis::exists($makeKey)) {
                $filterSets[] = $makeKey;
            }
        }

        // Model filter
        if ($model = $this->getFilterValue($request, ['webModelFilterInput', 'secondFilterModelInputNew'])) {
            $modelKey = self::REDIS_FILTER_PREFIX . 'model:' . strtolower($model);
            if (Redis::exists($modelKey)) {
                $filterSets[] = $modelKey;
            }
        }

        // Body type filter
        if ($body = $this->getFilterValue($request, ['webBodyFilter', 'mobileBody'])) {
            $bodyKey = self::REDIS_FILTER_PREFIX . 'body:' . strtolower($body);
            if (Redis::exists($bodyKey)) {
                $filterSets[] = $bodyKey;
            }
        }

        // If we have filter sets, intersect them
        if (!empty($filterSets)) {
            $filteredVins = Redis::sinter(...$filterSets);
            $filteredVins = array_intersect($filteredVins, $initialVins);
        }

        // Apply price filter
        $priceMin = $request->input('rangerMinPriceSlider') ?? $request->input('mobileRangerMinPriceSlider');
        $priceMax = $request->input('rangerMaxPriceSlider') ?? $request->input('mobileRangerMaxPriceSlider');

        if ($priceMin || $priceMax) {
            $filteredVins = $this->applyPriceFilter($filteredVins, $priceMin, $priceMax);
        }

        return $filteredVins;
    }

    private function applyPriceFilter(array $vins, $minPrice, $maxPrice): array
    {
        $minPrice = $minPrice ? (int)$minPrice : 0;
        $maxPrice = $maxPrice ? (int)$maxPrice : PHP_INT_MAX;
        $filtered = [];

        foreach ($vins as $vin) {
            $vehicleKey = self::REDIS_VEHICLE_PREFIX . $vin;

            try {
                // First check if the key exists and is a hash
                if (Redis::exists($vehicleKey) && Redis::type($vehicleKey) === 'hash') {
                    $price = Redis::hget($vehicleKey, 'price');

                    if ($price !== null && $price !== false) {
                        $price = (int)$price;
                        if ($price >= $minPrice && $price <= $maxPrice) {
                            $filtered[] = $vin;
                        }
                    }
                } else {
                    // If the vehicle data isn't properly stored in Redis, we'll need to get it from DB
                    $vehicle = MainInventory::select('price')
                        ->where('vin', $vin)
                        ->first();

                    if ($vehicle && $vehicle->price !== null) {
                        $price = (int)$vehicle->price;
                        if ($price >= $minPrice && $price <= $maxPrice) {
                            $filtered[] = $vin;

                            // Store the price in Redis for future use
                            Redis::hset($vehicleKey, 'price', $price);
                            Redis::expire($vehicleKey, $this->cacheTime);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to check price for VIN {$vin}: " . $e->getMessage());
                continue;
            }
        }

        return $filtered;
    }



    private function generateQueryCacheKey(Request $request): string
    {
        $urlComponents = parse_url($request->requestURL);
        parse_str($urlComponents['query'] ?? '', $queryParams);

        $params = array_merge(
            $request->only([
                'webRadios',
                'mobileRadios',
                'weblocationNewInput',
                'mobilelocation',
                'webMakeFilterMakeInput',
                'secondFilterMakeInputNew',
                'webModelFilterInput',
                'secondFilterModelInputNew',
                'webBodyFilter',
                'mobileBody',
                'rangerMinPriceSlider',
                'rangerMaxPriceSlider',
                'mobileRangerMinPriceSlider',
                'mobileRangerMaxPriceSlider',
                'selected_sort_search',
                'page'
            ]),
            [
                'condition' => $queryParams['conditions'] ?? null,
                'transmission' => $queryParams['transmission'] ?? null,
                'exteriorColor' => $queryParams['exteriorColors'] ?? null,
                'interiorColor' => $queryParams['interiorColors'] ?? null,
                'body' => $queryParams['body'] ?? null,
                'driveTrain' => $queryParams['driveTrains'] ?? null,
                'fuel' => $queryParams['hfuel'] ?? $queryParams['fuelTypes'] ?? null
            ]
        );

        return 'inventory_query_' . md5(serialize($params));
    }

private function formatAjaxResponse($paginatedResults, Request $request)
{
    $total_count = number_format($paginatedResults->total());
    $current_page_count = $paginatedResults->count();
    $perPage = $paginatedResults->perPage();
    $currentPage = $paginatedResults->currentPage();

    $startData = number_format(($currentPage - 1) * $perPage + 1);
    $end = number_format($startData + $current_page_count - 1);
    $range_display = "$startData - $end";
    $range_with_total = "$startData – $end of $total_count";

    $message = $paginatedResults->isEmpty()
        ? $this->generateNoMatchMessage($request->radius ?? 'nationwide')
        : '';

    $view = view('frontend.cache_auto_ajax', [
        'inventories' => $paginatedResults,
        'total_count' => $total_count,
        'range_display' => $range_display,
        'range_with_total' => $range_with_total,
        'current_page_count' => $current_page_count,
        'message' => $message,
        'messageData' => '@'
    ])->render();

    return response()->json([
        'view' => $view,
        'pagination' => $paginatedResults->links()->toHtml(),
        'total_count' => $total_count,
        'range_display' => $range_display,
        'range_with_total' => $range_with_total,
        'start' => $startData,
        'end' => $end,
        'message' => $message,
        'messageData' => '@'
    ]);
}


    private function handleDirectDatabaseQuery(Request $request)
    {
        $result = $this->directServerQuery($request);

        // Ensure we have a paginator instance
        if (!($result['inventories'] instanceof LengthAwarePaginator)) {
            // Fallback handling if not a paginator
            return response()->json([
                'view' => 'Error: Invalid pagination data',
                'pagination' => '',
                'total_count' => 0,
                'messageData' => 'Error loading results'
            ]);
        }

        $total_count = number_format($result['inventories']->total());
        $current_page_count = $result['inventories']->count();

        // Calculate the correct item range
        $perPage = $result['inventories']->perPage();
        $currentPage = $result['inventories']->currentPage();
        $firstItem = ($currentPage - 1) * $perPage + 1;
        $lastItem = $firstItem + $current_page_count - 1;

        $view = view('frontend.auto_ajax', [
            'inventories' => $result['inventories'],
            'total_count' => $total_count,
            'single_inventories_count' => "$firstItem-$lastItem", // Display as range
            'messageData' => $result['message']
        ])->render();

        return response()->json([
            'view' => $view,
            'pagination' => $result['inventories']->links()->toHtml(),
            'total_count' => $total_count,
            'messageData' => $result['message']
        ]);
    }

    private function showAutoListView(Request $request, array $searchData)
    {
        return view('frontend.auto', [
            'vehicles_body' => $searchData['vehicles_body'],
            'vehicles_fuel_other' => $searchData['vehicles_fuel'],
            'make_data' => $searchData['make_data'],
            'states' => $searchData['states'],
            'messageCookieData' => $searchData['messageCookieData']
        ])->withCookie($searchData['messageCookieData']);
    }

    private function getLocationDataForSearch(Request $request): array
    {
        $zipCode = $request->weblocationNewInput ?? $request->mobilelocation;
        $countyData = $this->getCountyDataByZip($zipCode);

        return [
            'zip_code' => $zipCode,
            'county' => $countyData['county'],
            'latitude' => $countyData['latitude'],
            'longitude' => $countyData['longitude']
        ];
    }

    private function getCountyDataByZip(?string $zipCode = null): array
    {
        if (empty($zipCode)) {
            return [
                'county' => 'Unknown',
                'zip_code' => null,
                'latitude' => null,
                'longitude' => null
            ];
        }

        if (!Redis::exists(self::REDIS_ZIP_COUNTY_KEY)) {
            return [
                'county' => 'Unknown',
                'zip_code' => $zipCode,
                'latitude' => null,
                'longitude' => null
            ];
        }

        $countyData = json_decode(Redis::get(self::REDIS_ZIP_COUNTY_KEY), true);
        $matchingData = collect($countyData)->where('zip_code', $zipCode)->first();

        return $matchingData ?: [
            'county' => 'Unknown',
            'zip_code' => $zipCode,
            'latitude' => null,
            'longitude' => null
        ];
    }

    private function getFilterValue(Request $request, array $keys, string $cookieKey = null): ?string
    {
        foreach ($keys as $key) {
            if (!empty($request->input($key))) {
                return strtolower($request->input($key));
            }
        }
        return $cookieKey ? strtolower($request->cookie($cookieKey)) : null;
    }

    private function generateNoMatchMessage($radius)
    {
        $radiusText = ($radius === 'nationwide') ? 'nationwide' : "within {$radius} miles";
        return "<section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px\" class=\"sptb2\">
                <div style=\"border-radius:5px\" class=\"container bg-white p-5 match\">
                    <div class=\"text-center\">
                        <h3 class=\"mb-2\">No exact matches {$radiusText}...</h3>
                        <p class=\"mb-2\">Modify your filters or click \"Save Search\" to be notified when more matches are available.</p>
                        <a href=\"#\" class=\"mb-2 clearfilterAjax\" style=\"text-decoration:underline;font-weight:bold;font-size:15px\" id=\"clearfilterAjax\">Clear all filters.</a>
                    </div>
                </div>
            </section>";
    }

    protected function directServerQuery($request)
    {
        // Your original tracking logic
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

        // // Use the service for initial query
        // $query = $this->inventoryService->getItemByFilterOnly($request, $dealer_id);
        $query = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'body_formated', 'fuel', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'transmission')
            ->with([
                'dealer' => function ($query) {
                    $query->select('id', 'dealer_id', 'name', 'state', 'city', 'zip');
                },
                'additionalInventory' => function ($query) {
                    $query->select('id', 'main_inventory_id', 'local_img_url');
                },
                'mainPriceHistory' => function ($query) {
                    $query->select('id', 'main_inventory_id', 'change_amount');
                }
            ]);

        // dd($request->all(), $request->rangerMaxPriceSlider);

        // Handle city/state filtering
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

        // Extract URL parameters
        $urlComponents = parse_url($request->requestURL);
        $queryParams = [];
        if (isset($urlComponents['query'])) {
            parse_str($urlComponents['query'], $queryParams);
        }

        $homeBodySearch = $queryParams['homeBodySearch'] ?? null;
        $make = $queryParams['make'] ?? $queryParams['secondFilterMakeInputNew'] ?? null;
        $model = $queryParams['model'] ?? $queryParams['secondFilterModelInputNew'] ?? null;
        $body = $queryParams['body'] ?? null;
        $condition =  $queryParams['conditions'] ?? $queryParams['autoMobileTypeCheckbox'] ?? null;
        $transmission =  $queryParams['transmission'] ?? $queryParams['autoMobileTransmissionCheckbox']  ?? null;
        $exteriorColor =  $queryParams['exteriorColors'] ?? $queryParams['autoMobileExteriorColorCheckbox'] ?? null;
        $interiorColor = $queryParams['interiorColors'] ?? $queryParams['autoMobileInteriorColorCheckbox'] ?? null;
        $drivetrain = $queryParams['driveTrains'] ?? $queryParams['autoMobileDriveTrainCheckbox'] ?? null;
        $fuel = $queryParams['hfuel'] ?? $queryParams['fuelTypes'] ?? $queryParams['fuel'] ?? null;
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
        }
        if ($model != null) {
            $query->where('model', $model);
        }
        if ($body != null) {
            $query->where('body_formated', $body);
        }

        if ($condition != null) {
            $this->applyWhereInFromCommaString($query, $condition, 'type');
        }
        if ($transmission != null) {
            $this->applyWhereInFromCommaString($query, $transmission, 'transmission');
        }
        if ($exteriorColor != null) {
            $this->applyWhereInFromCommaString($query, $exteriorColor, 'exterior_color');
        }
        if ($interiorColor != null) {
            $this->applyWhereInFromCommaString($query, $interiorColor, 'interior_color');
        }
        if ($drivetrain != null) {
            $this->applyWhereInFromCommaString($query, $drivetrain, 'drive_info');
        }
        if ($fuel != null) {
            $this->applyWhereInFromCommaString($query, $fuel, 'fuel');
        }


        if (($request->rangerMinPriceSlider != null || $request->rangerMaxPriceSlider != null)) {

            $minValue = ($request->rangerMinPriceSlider != null) ? $request->rangerMinPriceSlider : 0;
            $maxValue = ($request->rangerMaxPriceSlider != null) ? $request->rangerMaxPriceSlider : 300000;

            // dd($request->rangerMileageMinPriceSlider, $request->rangerMileageMaxPriceSlider);
            if ($minValue > 150000) {
                $query->whereNotNull('price');
            } else {
                $query->whereBetween('price', [$minValue, $maxValue]);
            }
        }

        if ($request->mobileRangerMinPriceSlider != null || $request->mobileRangerMaxPriceSlider != null) {
            $minValue = ($request->mobileRangerMinPriceSlider != null) ? $request->mobileRangerMinPriceSlider : 0;
            $maxValue = ($request->mobileRangerMaxPriceSlider != null) ? $request->mobileRangerMaxPriceSlider : 300000;

            if ($minValue > 150000) {
                $query->whereNotNull('price');
            } else {
                $query->whereBetween('price', [$minValue, $maxValue]);
            }

            // if ($maxValue < 150000) {
            //     // If the max value is less than 150000, use a normal between range query
            //     $query->whereBetween('price', [$minValue, $maxValue]);
            // } else {
            //     // If the max value is 150000 or more, show all vehicles with miles >= minValue
            //     $query->where('price', '>=', $minValue);
            // }
        }

        if ($request->rangerMileageMinPriceSlider != null || $request->rangerMileageMaxPriceSlider != null) {

            $minValue = ($request->rangerMileageMinPriceSlider != null) ? $request->rangerMileageMinPriceSlider : 0;
            $maxValue = ($request->rangerMileageMaxPriceSlider != null) ? $request->rangerMileageMaxPriceSlider : 150000;

            if ($maxValue < 150000) {
                // If the max value is less than 150000, use a normal between range query
                $query->whereBetween('miles', [$minValue, $maxValue]);
            } else {
                // If the max value is 150000 or more, show all vehicles with miles >= minValue
                $query->where('miles', '>=', $minValue);
            }
        }

        if ($request->mobileMileageRangerMinPriceSlider != null || $request->mobileMileageRangerMaxPriceSlider != null) {
            $minValue = ($request->mobileMileageRangerMinPriceSlider != null) ? $request->mobileMileageRangerMinPriceSlider : 0;
            $maxValue = ($request->mobileMileageRangerMaxPriceSlider != null) ? $request->mobileMileageRangerMaxPriceSlider : 1000000;
            if ($maxValue < 150000) {
                // If the max value is less than 150000, use a normal between range query
                $query->whereBetween('miles', [$minValue, $maxValue]);
            } else {
                // If the max value is 150000 or more, show all vehicles with miles >= minValue
                $query->where('miles', '>=', $minValue);
            }
            // $query->whereBetween('miles', [$minValue, $maxValue]);
        }

        if ($request->rangerYearMinPriceSlider != null || $request->rangerYearMaxPriceSlider != null) {
            $minValue = ($request->rangerYearMinPriceSlider != null) ? $request->rangerYearMinPriceSlider : 1980;
            $maxValue = ($request->rangerYearMaxPriceSlider != null) ? $request->rangerYearMaxPriceSlider : 2025;

            $query->whereBetween('year', [$minValue, $maxValue]);
        }


        if ($request->mobileYearRangerMinPriceSlider != null || $request->mobileYearRangerMaxPriceSlider != null) {
            $minValue = ($request->mobileYearRangerMinPriceSlider != null) ? $request->mobileYearRangerMinPriceSlider : 1980;
            $maxValue = ($request->mobileYearRangerMaxPriceSlider != null) ? $request->mobileYearRangerMaxPriceSlider : 2025;

            $query->whereBetween('year', [$minValue, $maxValue]);
        }



        $query->whereNotNull('price')->where('price', '>', 1);
        $message = '';

        // Optimize the select and relationships
        $query->select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'body_formated', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'payment_price')
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
            ]);

        $zipCodeData = [
            'zip_code_data' => $zipCode,
            'zip_radios_data' => $zip_radios,
            'query_data' => $query,
        ];

        if ($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide') {
            $message = null;
            $inventories = $query->paginate(20);
        } else {
            $result = $this->getItemByDistance($zipCodeData['zip_code_data'], $zipCodeData['zip_radios_data'], $zipCodeData['query_data']);
            $inventories = $result['inventories'];
            $message = $result['message'];
        }

        return ['inventories' => $inventories, 'message' => $message];
    }

    protected function applyWhereInFromCommaString($query, $values, string $column)
    {
        if ($values !== null) {
            $valuesArray = is_string($values) ? explode(',', $values) : (array) $values;
            $valuesArray = array_map('trim', $valuesArray);
            $query->whereIn($column, $valuesArray);
        }
    }

    private function getItemByDistance($zipCode, $zip_radios, $query)
    {
        $zipCode = $zipCode ?? '78228';
        $zip_radios = $zip_radios ?? 75;
        $message = '';

        // Check if zipCode exists in location_zips
        $zipLocation = DB::table('location_zips')->where('zip_code', $zipCode)->first();

        if ($zipLocation) {
            $latitude = $zipLocation->latitude;
            $longitude = $zipLocation->longitude;
            Cookie::queue('zipcode', $zipCode, self::COOKIE_CACHE_TIME_SIX_MONTH);
        } else {
            $apiKey = '4b84ff4ad9a74c79ad4a1a945a4e5be1';
            $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},us&key={$apiKey}";

            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['results'][0]['geometry'])) {
                    $geometry = $data['results'][0]['geometry'];
                    $components = $data['results'][0]['components'] ?? [];

                    // Safely get state and county information
                    $state_info = $components['state'] ?? 'Unknown';
                    $short_name_info = $components['state_code'] ?? 'UA';
                    $county_info = $components['county'] ?? 'Unknown';

                    $location_state_data = LocationState::firstOrCreate(
                        ['state_name' => $state_info],
                        ['short_name' => $short_name_info]
                    );
                    $location_state_id = $location_state_data->id;

                    $location_cities_data = LocationCity::firstOrCreate(
                        [
                            'location_state_id' => $location_state_id,
                            'city_name' => $state_info
                        ],
                        [
                            'latitude' => $geometry['lat'],
                            'longitude' => $geometry['lng']
                        ]
                    );

                    $location_zips_data = LocationZip::firstOrCreate(
                        [
                            'location_city_id' => $location_cities_data->id,
                            'zip_code' => $zipCode
                        ],
                        [
                            'county' => $county_info,
                            'latitude' => $geometry['lat'],
                            'longitude' => $geometry['lng'],
                            'sales_tax' => 8,
                            'url' => $url
                        ]
                    );

                    Cookie::queue('zipcode', $zipCode, self::COOKIE_CACHE_TIME_SIX_MONTH);

                    // Set the coordinates for distance calculation
                    $latitude = $geometry['lat'];
                    $longitude = $geometry['lng'];
                } else {
                    // If API fails or doesn't return geometry, return default results
                    return [
                        'inventories' => $query->orderByDesc('id')->paginate(20),
                        'message' => "Invalid Zip Code"
                    ];
                }
            } else {
                // If API request fails, return default results
                return [
                    'inventories' => $query->orderByDesc('id')->paginate(20),
                    'message' => "Unable to validate zip code"
                ];
            }
        }

        // If we have coordinates, filter by distance
        if (isset($latitude) && isset($longitude)) {
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

        // Fallback if no coordinates available
        return [
            'inventories' => $query->orderByDesc('id')->paginate(20),
            'message' => "Location data not available"
        ];
    }

    // optional
    private function ensureDataFreshness($key)
    {
        if (!Redis::exists($key)) {
            Log::info("Redis key expired or missing: {$key}");
            $this->refreshFilterData();
            return false;
        }

        $ttl = Redis::ttl($key);
        if ($ttl < 0) { // Key exists but has no expiration
            Redis::expire($key, $this->cacheTime);
            Log::info("Set expiration on key without TTL: {$key}");
        }

        return true;
    }
}
