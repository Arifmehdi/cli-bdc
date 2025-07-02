<?php

namespace App\Http\Controllers;

use App\Interface\InventoryServiceInterface;
use App\Models\LocationCity;
use App\Models\LocationState;
use App\Models\LocationZip;
use App\Models\MainInventory;
use App\Models\UserTrack;
use App\Models\VehicleMake;
use App\Service\DealerService;
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
    const COOKIE_CACHE_TIME_SIX_MONTH = 6 * 30 * 24 * 60;

    protected $cacheTime; // Class property to store cache time
    private $inventoryService;
    private $dealerService;

    public function __construct(InventoryServiceInterface $inventoryService, DealerService $dealerService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealerService = $dealerService;
        $this->cacheTime = now()->addRealHours((int)env('REDIS_CACHE_TIME', 24)); // Cast to integer
    }

    /**
     * Display auto listings with filters
     */
    public function auto(Request $request, $param = null)
    {
        // // Store in cache
        // $success = Cache::put('amar_jonno', 'amar jonno valo thajbo canada ', $this->cacheTime);
        // dd($this->cacheTime, $cachedValue = Cache::get('amar_jonno'));

        if ($clear = $request->input('clear')) {
            return $this->handleClearRequest($clear);
        }

        // Prepare basic data
        $searchData = $this->prepareSearchData($request);

        // Handle AJAX requests
        if ($request->ajax()) {
            return $this->handleAjaxRequest($request, $searchData);
        }

        // Return regular view
        return $this->showAutoListView($request, $searchData);
    }

    /**
     * Handle clear filter requests
     */
    private function handleClearRequest(string $clearType)
    {
        Cookie::queue(Cookie::forget('searchData'));

        return response()->json([
            'success' => $clearType === 'flush' ? 'clear' : 'newcar'
        ]);
    }

    /**
     * Prepare search data and vehicle information
     */
    private function prepareSearchData(Request $request): array
    {
        $inventoryQuery = MainInventory::query();

        return [
            'vehicles' => VehicleMake::orderBy('make_name')->where('status', 1)->pluck('id', 'make_name'),
            'vehicles_body' => $inventoryQuery->distinct()->pluck('body_formated')->toArray(),
            'vehicles_fuel' => $this->getSortedFuelTypes($inventoryQuery),
            'price_range' => $this->getPriceRange($inventoryQuery),
            'mileage_range' => $this->getMileageRange($inventoryQuery),
            'make_data' => $request->input('make'),
            'states' => LocationState::orderBy('state_name')->pluck('state_name', 'id'),
            'searchBody' => $request->query('homeBodySearch'),
            'messageCookieData' => $request->cookie('messageCookieData')
        ];
    }

    /**
     * Get sorted fuel types
     */
    private function getSortedFuelTypes($query): array
    {
        $fuels = $query->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();
        sort($fuels);
        return $fuels;
    }

    /**
     * Get price range from inventory
     */
    private function getPriceRange($query): array
    {
        return [
            'max' => (int)$query->where('price', '!=', 'N/A')->max('price'),
            'min' => (int)$query->where('price', '!=', 'N/A')->min('price')
        ];
    }

    /**
     * Get mileage range from inventory
     */
    private function getMileageRange($query): array
    {
        return [
            'max' => $query->where('miles', '!=', 'N/A')->max('miles'),
            'min' => $query->where('miles', '!=', 'N/A')->min('miles')
        ];
    }

    /**
     * Handle AJAX listing requests
     */
    private function handleAjaxRequest(Request $request, array $searchData)
    {
        // Determine if we're doing a nationwide search
        $isNationwide = $request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide';

        if ($isNationwide) {
            $redisKey = self::REDIS_NATIONWIDE_KEY;
        } else {
            $locationData = $this->getLocationDataForSearch($request);
            $redisKey = $this->getCountyRedisKey($locationData['county']);
        }

        // Try to get data from Redis
        if ($inventoryData = Redis::get($redisKey)) {
            return $this->processCachedInventory($request, json_decode($inventoryData, true));
        }

        // Fallback to direct database query
        return $this->handleDirectDatabaseQuery($request);
    }

    /**
     * Get location data for search
     */
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

    /**
     * Get Redis key for county data
     */
    private function getCountyRedisKey(string $countyName): string
    {
        $normalized = str_replace(' ', '_', strtolower($countyName));

        return $normalized === 'unknown'
            ? self::REDIS_COUNTY_PREFIX . 'bexar' // Default to San Antonio
            : self::REDIS_COUNTY_PREFIX . $normalized;
    }

    /**
     * Process cached inventory data
     */
    private function processCachedInventory(Request $request, array $inventoryData)
    {
        $filteredData = $this->filterInventoryData(
            collect($inventoryData),
            $this->extractFiltersFromRequest($request)
        );

        // Apply sorting if requested
        if ($request->has('selected_sort_search')) {
            $filteredData = $this->applySorting($filteredData, $request->selected_sort_search);
        }

        // Paginate results
        $paginatedResults = $this->paginateResults($filteredData, $request);

        // Prepare response
        return $this->formatAjaxResponse($paginatedResults, $request);
    }

    /**
     * Extract filters from request
     */
    private function extractFiltersFromRequest(Request $request): array
    {
        return [
            'make' => $this->getFilterValue($request, [
                'webMakeFilterMakeInput',
                'secondFilterMakeInputNew'
            ]),
            'model' => $this->getFilterValue($request, [
                'webModelFilterInput',
                'secondFilterModelInputNew'
            ]),
            'body' => $this->getFilterValue($request, [
                'webBodyFilter',
                'mobileBody'
            ], 'homebodySearch'),
            'fuels' => $this->getArrayFilterValue($request, [
                'autoWebFuelCheckbox',
                'autoMobileFuelCheckbox'
            ], 'homefuelSearch'),
            'exterior_colors' => $this->getArrayFilterValue($request, [
                'autoWebExteriorColorCheckbox',
                'autoMobileExteriorColorCheckbox'
            ]),
            'interior_colors' => $this->getArrayFilterValue($request, [
                'autoWebInteriorColorCheckbox',
                'autoMobileInteriorColorCheckbox'
            ]),
            'drivetrains' => $this->getDrivetrainFilter($request),
            'transmissions' => $this->getArrayFilterValue($request, [
                'autoWebTransmissionCheckbox',
                'autoMobileTransmissionCheckbox'
            ]),
            'conditions' => $this->getArrayFilterValue($request, [
                'autoWebConditionCheckbox',
                'autoMobileTypeCheckbox'
            ]),
            'price_range' => [
                'min' => $request->input('rangerMinPriceSlider') ?? $request->input('mobileRangerMinPriceSlider'),
                'max' => $request->input('rangerMaxPriceSlider') ?? $request->input('mobileRangerMaxPriceSlider')
            ],
            'mileage_range' => [
                'min' => $request->input('rangerMileageMinPriceSlider') ?? $request->input('mobileMileageRangerMinPriceSlider'),
                'max' => $request->input('rangerMileageMaxPriceSlider') ?? $request->input('mobileMileageRangerMaxPriceSlider')
            ],
            'year_range' => [
                'min' => $request->input('rangerYearMinPriceSlider') ?? $request->input('mobileYearRangerMinPriceSlider'),
                'max' => $request->input('rangerYearMaxPriceSlider') ?? $request->input('mobileYearRangerMaxPriceSlider')
            ],
            'location' => $this->getLocationFilter($request)
        ];
    }

    /**
     * Get single filter value from request
     */
    private function getFilterValue(Request $request, array $keys, string $cookieKey = null): ?string
    {
        foreach ($keys as $key) {
            if (!empty($request->input($key))) {
                return strtolower($request->input($key));
            }
        }

        return $cookieKey ? strtolower($request->cookie($cookieKey)) : null;
    }

    /**
     * Get array filter value from request
     */
    private function getArrayFilterValue(Request $request, array $keys, string $cookieKey = null): array
    {
        foreach ($keys as $key) {
            if (!empty($request->input($key))) {
                return array_map('strtolower', (array)$request->input($key));
            }
        }

        return $cookieKey ? [strtolower($request->cookie($cookieKey))] : [];
    }

    /**
     * Get drivetrain filter values
     */
    private function getDrivetrainFilter(Request $request): array
    {
        $drivetrainInfo = !empty($request->input('autoWebDriveTrainCheckbox'))
            ? (array)$request->input('autoWebDriveTrainCheckbox')
            : (array)$request->input('autoMobileDriveTrainCheckbox', []);

        return $this->drivetrainMapping($drivetrainInfo);
    }

    /**
     * Get location filter data
     */
    private function getLocationFilter(Request $request): ?array
    {
        if ($request->webRadios == 'Nationwide') {
            return null;
        }

        $zipCode = $request->weblocationNewInput ?? $request->mobilelocation;
        $countyData = $this->getCountyDataByZip($zipCode);

        return [
            'zip_code' => $zipCode,
            'latitude' => $countyData['latitude'],
            'longitude' => $countyData['longitude'],
            'radius' => $request->webRadios ?? $request->mobileRadios
        ];
    }

    /**
     * Filter inventory data based on criteria
     */
    private function filterInventoryData($inventory, array $filters)
    {
        return $inventory->filter(function ($item) use ($filters) {
            // Location filter
            if ($filters['location'] && !$this->passesLocationFilter($item, $filters['location'])) {
                return false;
            }

            // Make/Model/Body filters
            if (!empty($filters['make']) && !str_contains(strtolower($item['make']), $filters['make'])) {
                return false;
            }

            if (!empty($filters['model']) && !str_contains(strtolower($item['model']), $filters['model'])) {
                return false;
            }

            if (!empty($filters['body']) && !str_contains(strtolower($item['body_formated']), $filters['body'])) {
                return false;
            }

            // Fuel filter
            if (!empty($filters['fuels']) && !$this->passesFuelFilter($item['fuel'], $filters['fuels'])) {
                return false;
            }

            // Color filters
            if (!empty($filters['exterior_colors']) && !in_array(strtolower($item['exterior_color']), $filters['exterior_colors'])) {
                return false;
            }

            if (!empty($filters['interior_colors']) && !in_array(strtolower($item['interior_color']), $filters['interior_colors'])) {
                return false;
            }

            // Drivetrain/Transmission/Condition filters
            if (!empty($filters['drivetrains']) && !in_array(strtolower($item['drive_info']), $filters['drivetrains'])) {
                return false;
            }

            if (!empty($filters['transmissions']) && !in_array(strtolower($item['transmission']), $filters['transmissions'])) {
                return false;
            }

            if (!empty($filters['conditions']) && !in_array(strtolower($item['type']), $filters['conditions'])) {
                return false;
            }

            // Range filters
            if (!$this->passesRangeFilter($item['price'], $filters['price_range'])) {
                return false;
            }

            if (!$this->passesRangeFilter($item['miles'], $filters['mileage_range'])) {
                return false;
            }

            if (!$this->passesRangeFilter($item['year'], $filters['year_range'])) {
                return false;
            }

            return true;
        });
    }

    /**
     * Check if item passes location filter
     */
    private function passesLocationFilter(array $item, array $location): bool
    {
        if (empty($item['latitude']) || empty($item['longitude'])) {
            return false;
        }

        $distance = $this->calculateDistance(
            $location['latitude'],
            $location['longitude'],
            $item['latitude'],
            $item['longitude']
        );

        return $distance <= $location['radius'];
    }

    /**
     * Check if item passes fuel filter
     */
    private function passesFuelFilter(?string $itemFuel, array $allowedFuels): bool
    {
        if (empty($itemFuel)) {
            return false;
        }

        $itemFuel = strtolower($itemFuel);

        foreach ($allowedFuels as $fuel) {
            if (str_contains($itemFuel, $fuel)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if value passes range filter
     */
    private function passesRangeFilter($value, ?array $range): bool
    {
        if (empty($range) || $value === null) {
            return true;
        }

        if (isset($range['min']) && $value < $range['min']) {
            return false;
        }

        if (isset($range['max']) && $value > $range['max']) {
            return false;
        }

        return true;
    }

    /**
     * Apply sorting to filtered data
     */
    private function applySorting($data, string $sortOption)
    {
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

        if (isset($sortMapping[$sortOption])) {
            [$field, $direction] = $sortMapping[$sortOption];

            return $data->sortBy(function ($item) use ($field) {
                return $item[$field] ?? null;
            }, SORT_REGULAR, $direction === 'desc')->values();
        }

        return $data;
    }

    /**
     * Paginate filtered results
     */
    private function paginateResults($data, Request $request)
    {
        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $paginatedItems = $data->slice($offset, $perPage)->values();

        return new LengthAwarePaginator(
            $paginatedItems,
            $data->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    /**
     * Format AJAX response
     */
    private function formatAjaxResponse($paginatedResults, Request $request)
    {
        $total_count = number_format($paginatedResults->total());
        $current_page_count = $paginatedResults->count();

        // Calculate the range for display
        $perPage = $paginatedResults->perPage();
        $currentPage = $paginatedResults->currentPage();
        $start = ($currentPage - 1) * $perPage + 1;
        $end = $start + $current_page_count - 1;

        $range_display = "$start-$end"; // Will show "1-20", "21-40", etc.

        $message = $paginatedResults->isEmpty()
            ? 'Your search did not return any results.'
            : '';

        $view = view('frontend.cache_auto_ajax', [
            'inventories' => $paginatedResults,
            'total_count' => $total_count,
            'range_display' => $range_display, // Pass the range to view
            'message' => $message
        ])->render();

        return response()->json([
            'view' => $view,
            'pagination' => $paginatedResults->links()->toHtml(),
            'total_count' => $total_count,
            'message' => $message
        ]);
    }

    /**
     * Handle direct database query when Redis fails
     */
    private function handleDirectDatabaseQuery(Request $request)
    {
        $result = $this->directServerQuery($request);

        $current_page_count = $result['inventories']->count();
        $total_count = number_format($result['inventories']->total());
        $single_inventories_count = ($result['inventories']->perPage() * ($result['inventories']->currentPage() - 1)) + $current_page_count;

        $view = view('frontend.auto_ajax', [
            'inventories' => $result['inventories'],
            'total_count' => $total_count,
            'single_inventories_count' => $single_inventories_count,
            'message' => $result['message']
        ])->render();

        return response()->json([
            'view' => $view,
            'pagination' => $result['inventories']->links()->toHtml(),
            'total_count' => $total_count,
            'message' => $result['message']
        ]);
    }

    /**
     * Show the main auto list view
     */
    private function showAutoListView(Request $request, array $searchData)
    {
        return view('frontend.auto', [
            'vehicles' => $searchData['vehicles'],
            'vehicles_body' => $searchData['vehicles_body'],
            'searchBody' => $searchData['searchBody'],
            'vehicles_fuel_other' => $searchData['vehicles_fuel'],
            'make_data' => $searchData['make_data'],
            'states' => $searchData['states'],
            'cachefuel' => $searchData['vehicles_fuel'],
            'messageCookieData' => $searchData['messageCookieData']
        ])->withCookie($searchData['messageCookieData']);
    }

    /**
     * Get county data by zip code from Redis
     */
    /**
     * Get county data by zip code from Redis
     */
    private function getCountyDataByZip(?string $zipCode = null): array
    {
        // If no zip code provided, return default/unknown data
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

    /**
     * Calculate distance between two points
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(
            sqrt(
                pow(sin($latDelta / 2), 2) +
                    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
            )
        );

        return $angle * $earthRadius;
    }


    /**
     * Map drivetrain values to database equivalents
     */
    private function drivetrainMapping(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $driveTypeMapping = [
            '4WD' => ['Four-wheel Drive', '4WD'],
            'AWD' => ['All-wheel Drive', 'AWD'],
            'FWD' => ['Front-wheel Drive', 'FWD'],
            'RWD' => ['Rear-wheel Drive', 'RWD'],
            'Other' => ['Unknown', '–', '----']
        ];

        $mappedValues = [];

        foreach ($data as $value) {
            if (isset($driveTypeMapping[$value])) {
                $mappedValues = array_merge($mappedValues, $driveTypeMapping[$value]);
            }
        }

        return array_unique($mappedValues);
    }
    protected function directServerQuery($request)
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
        $query = $this->inventoryService->getItemByFilterOnly($request, $dealer_id);

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
                // $query = MainInventory::with('dealer', 'additionalInventory');

                $query = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'transmission')
                ->with([
                    'dealer' => function ($query) {
                        $query->select('id', 'dealer_id', 'name', 'state','city', 'zip');
                    },
                    'additionalInventory' => function ($query) {
                        $query->select('id', 'main_inventory_id', 'local_img_url');
                    },
                    'mainPriceHistory' => function ($query) {
                        $query->select('id', 'main_inventory_id', 'change_amount');
                    }
                ]);
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


        $query->select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'payment_price')
            ->with([
                'dealer' => function ($query) {
                    $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id')
                        ->addSelect('id'); // Add id explicitly to avoid conflict
                },
                'additionalInventory' => function ($query) {
                    $query->select('main_inventory_id', 'local_img_url')  // Only necessary columns
                        ->addSelect('id'); // Add id explicitly to avoid conflict
                },
                'mainPriceHistory' => function ($query) {
                    $query->select('main_inventory_id', 'change_amount') // Only necessary columns
                        ->addSelect('id'); // Add id explicitly to avoid conflict
                }
            ]);


        $query->where('price', '>', '1');
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
    private function cookieSetParam($request, $id = null)
    {
        $requestURL = $request->requestURL;
        $urlComponents = parse_url($requestURL);
        $queryString = $urlComponents['query'] ?? '';
        parse_str($queryString, $queryParams);

        $lowestValue = $queryParams['lowestPrice'] ?? null;
        $bestDealValue = $queryParams['bestDeal'] ?? null;
        $lowestMileageValue = $queryParams['lowestMileage'] ?? null;
        $ownedValue = $queryParams['owned'] ?? null;
        $makeTypeSearchValue = $queryParams['makeTypeSearch'] ?? null;
        $homeBodySearch = $queryParams['body'] ?? null;
        $hfuelSearch = $queryParams['hfuel'] ?? null;
        $homepage = $queryParams['home'] ?? null;
        $hometypeSearch = $queryParams['homeBodySearch'] ?? null;

        $homeMakeSearch = $queryParams['make'] ?? null;
        $homeModelSearch = $queryParams['model'] ?? null;
        $homePriceSearch = $queryParams['maximum_price'] ?? null;
        $homeDealerCitySearch = $queryParams['homeDealerCitySearch'] ?? null;
        $homeDealerStateSearch = $queryParams['homeDealerStateSearch'] ?? null;
        $homeLocationSearch = $queryParams['zip'] ?? null;
        $homeRadiusSearch = $queryParams['radius'] ?? null;
        // $homeLocationSearch2 = $queryParams['homeLocationSearch2'] ?? null;
        $homeMileageSearch = $queryParams['maximum_miles'] ?? null;
        $homeMinMileageSearch = $queryParams['min-miles'] ?? null;
        $homeMaxMileageSearch = $queryParams['max-miles'] ?? null;


        $homeMinPayment = $queryParams['min_payment'] ?? null;
        $homeMaxPayment = $queryParams['max_payment'] ?? null;
        $homeMinYear = $queryParams['min_year'] ?? null;
        $homeMaxYear = $queryParams['max_year'] ?? null;
        $minPriceBody = $queryParams['min_price'] ?? null;
        $maxPriceBody = $queryParams['max_price'] ?? null;
        $hfuel = $queryParams['hfuel'] ?? null;

        $zipCode  = $homeLocationSearch;
        $countryCode = 'US';
        // dd($request->all(), $request->autoWebFuelCheckbox, $request->autoWebExteriorColorCheckbox, $request->autoMobileExteriorColorCheckbox, $request->autoMobileInteriorColorCheckbox); // autoWebExteriorColorCheckbox
        $searchData = [
            'firstzipFilter' => $request->firstzipFilter,
            'firstMakeFilter' => $request->firstMakeFilter,
            'firstModelFilter' => $request->firstModelFilter,
            'web_search_any' => $request->web_search_any,
            'makeCheckdata' => $request->makeCheckdata,
            'autoMaxBodyCheckbox' => $request->autoMaxBodyCheckbox,
            'autoMinYearCheckbox' => $request->autoMinYearCheckbox,
            'autoMaxYearCheckbox' => $request->autoMaxYearCheckbox,
            'rangerMinPriceSlider' => $request->rangerMinPriceSlider,
            'rangerMaxPriceSlider' => $request->rangerMaxPriceSlider,
            'rangerMileageMinPriceSlider' => $request->rangerMileageMinPriceSlider,
            'rangerMileageMaxPriceSlider' => $request->rangerMileageMaxPriceSlider,
            'rangerYearMinPriceSlider' => $request->rangerYearMinPriceSlider,
            'rangerYearMaxPriceSlider' => $request->rangerYearMaxPriceSlider,
            'totalLoanAmountCalculation' => $request->totalLoanAmountCalculation,
            'autoWebConditionCheckbox' => $request->autoWebConditionCheckbox,
            'autoWebTransmissionCheckbox' => $request->autoWebTransmissionCheckbox,
            'autoWebFuelCheckbox' => $request->autoWebFuelCheckbox,
            'autoWebDriveTrainCheckbox' => $request->autoWebDriveTrainCheckbox ??  $request->autoMobileDriveTrainCheckbox,
            'webColorFilter' => $request->webColorFilter,
            'webMakeFilterMakeInput' => $request->webMakeFilterMakeInput,
            'webBodyFilter' =>  $homeBodySearch,
            'mobileBody' => $homeBodySearch,
            // mobile version filter data
            'mobileRangerMinPriceSlider' => $request->mobileRangerMinPriceSlider,
            'mobileRangerMaxPriceSlider' => $request->mobileRangerMaxPriceSlider,
            'mobileMileageRangerMinPriceSlider' => $request->mobileMileageRangerMinPriceSlider,
            'mobileMileageRangerMaxPriceSlider' => $request->mobileMileageRangerMaxPriceSlider,
            'mobileYearRangerMinPriceSlider' => $request->mobileYearRangerMinPriceSlider,
            'mobileYearRangerMaxPriceSlider' => $request->mobileYearRangerMaxPriceSlider,
            'autoMobileTypeCheckbox' => $request->autoMobileTypeCheckbox,
            'secondFilterMakeInputNew' => $request->secondFilterMakeInputNew,
            'secondFilterModelInputNew' => $request->secondFilterModelInputNew,
            'autoMobileFuelCheckbox' => $request->autoMobileFuelCheckbox,
            'autoMobileTransmissionCheckbox' => $request->autoMobileTransmissionCheckbox,
            'mobileColorFilter' => $request->mobileColorFilter,

            // 'webExteriorColorFilter' => $request->webExteriorColorFilter,
            // 'webInteriorColorFilter' => $request->webInteriorColorFilter,
            'autoWebExteriorColorCheckbox' => $request->autoWebExteriorColorCheckbox,
            'autoWebInteriorColorCheckbox' => $request->autoWebInteriorColorCheckbox,

            'autoMobileExteriorColorCheckbox' => $request->autoMobileExteriorColorCheckbox,
            'autoMobileInteriorColorCheckbox' => $request->autoMobileInteriorColorCheckbox,

            // 'zipcode' =>
        ];

        // dd($homeLocationSearch);
        Cookie::queue('zipcode', $homeLocationSearch, self::COOKIE_CACHE_TIME_SIX_MONTH);
        // Cache::forever('zipcode', $homeLocationSearch);
        Cookie::queue('searchData', json_encode($searchData), self::COOKIE_CACHE_TIME_SIX_MONTH);
        // Cookie::queue('searchData', json_encode($searchData), 120);

        // dd($homeMinPayment,$homeMaxPayment,$homeMinYear,$homeMaxYear,$minPriceBody,$maxPriceBody,$hfuel,$zipCode,$countryCode);
        // dd($homeMakeSearch,$homeModelSearch,$homePriceSearch,$homeDealerCitySearch,$homeDealerStateSearch, $homeRadiusSearch,$homeMileageSearch,$homeMinMileageSearch,$homeMaxMileageSearch);
        // dd($requestURL,$urlComponents,$queryString,$lowestValue,$bestDealValue,$lowestMileageValue,$ownedValue,$makeTypeSearchValue,$homeBodySearch,$homepage,$hometypeSearch,);
        $data = [
            'homebodySearch' => $homeBodySearch,
            'homefuelSearch' => $hfuelSearch,
        ];
        return $data;
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