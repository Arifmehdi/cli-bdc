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
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Http;

class ListingController extends Controller
{
    use Notify;

    private $inventoryService;
    private $dealerService;

    // Cache constants
    const CACHE_PREFIX = 'bdc_';
    const CACHE_TTL = 1440; // 24 hours in minutes
    const ZIP_CACHE_KEY = 'zip_county_data';
    const COUNTY_CACHE_PREFIX = 'county_data_';

    public function __construct(InventoryServiceInterface $inventoryService, DealerService $dealerService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealerService = $dealerService;
    }

    public function auto(Request $request, $param = null)
    {
        // // Handle clear requests
        // if ($request->input('clear') === 'flush' || $request->input('clear') === 'newCar') {
        //     Cookie::queue(Cookie::forget('searchData'));
        //     return response()->json(['success' => $request->input('clear')]);
        // }

                $clear = $request->input('clear');
        $zipCode = $request->input('zip');

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

        // Get basic vehicle data
        $vehicles = VehicleMake::orderBy('make_name')->where('status', 1)->pluck('id', 'make_name');
        $searchBody = $request->query('homeBodySearch');

        // Get price and mileage ranges
        $priceRange = $this->getPriceRange();
        $mileageRange = $this->getMileageRange();

        // Handle AJAX requests
        if ($request->ajax()) {
            return $this->handleAjaxRequest($request);
        }

        // Return view for non-AJAX requests
        return view('frontend.auto', [
            'vehicles' => $vehicles,
            'vehicles_body' => $this->getDistinctBodyTypes(),
            'searchBody' => $searchBody,
            'vehicles_fuel_other' => $this->getDistinctFuelTypes(),
            'make_data' => $request->input('make'),
            'states' => LocationState::orderBy('state_name')->pluck('state_name', 'id'),
            'cachefuel' => $this->getDistinctFuelTypes(),
            'messageCookieData' => $request->cookie('messageCookieData')
        ]);
    }

    /**
     * Handle AJAX requests for vehicle listings
     */
    protected function handleAjaxRequest(Request $request)
    {
        $cacheKey = $this->generateCacheKey($request);

        // Try to get cached results
        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        // Process request and filter data
        $filteredData = $this->processFilters($request);

        // Paginate results
        $paginatedResults = $this->paginateResults($filteredData, $request);

        // Prepare response
        $response = $this->prepareAjaxResponse($paginatedResults, $request);

        // Cache the response
        Cache::put($cacheKey, $response, self::CACHE_TTL);

        return response()->json($response);
    }

    /**
     * Generate a unique cache key based on request parameters
     */
    protected function generateCacheKey(Request $request): string
    {
        return self::CACHE_PREFIX . 'listing_' . md5(json_encode($request->all()));
    }

    /**
     * Process filters and return filtered data
     */
    protected function processFilters(Request $request)
    {
        $zipInfo = $this->getZipCodeInfo($request);
        $jsonData = $this->getCountyData($request, $zipInfo);

        return $this->applyFilters($jsonData, $request, $zipInfo);
    }

    /**
     * Get zip code information with caching
     */
    protected function getZipCodeInfo(Request $request): array
    {
        $zipCode = $request->weblocationNewInput ?? $request->mobilelocation;

        if ($request->webRadios === 'Nationwide') {
            return [
                'county' => 'Nationwide',
                'latitude' => null,
                'longitude' => null
            ];
        }

        $cacheKey = self::COUNTY_CACHE_PREFIX . $zipCode;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($zipCode) {
            $countyData = LocationZip::where('zip_code', $zipCode)->first();

            return $countyData ? [
                'county' => $countyData->county,
                'latitude' => $countyData->latitude,
                'longitude' => $countyData->longitude,
                'zip_code' => $zipCode
            ] : [
                'county' => 'Unknown',
                'latitude' => null,
                'longitude' => null,
                'zip_code' => $zipCode
            ];
        });
    }

    /**
     * Get county data from cache or file
     */
    protected function getCountyData(Request $request, array $zipInfo)
    {
        $jsonFileName = $request->webRadios === 'Nationwide'
            ? 'nationwide_county'
            : str_replace(' ', '_', strtolower($zipInfo['county'])) . '_county';

        $cacheKey = self::CACHE_PREFIX . 'county_' . $jsonFileName;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($jsonFileName) {
            $filePath = storage_path("app/{$jsonFileName}.json");
            return file_exists($filePath)
                ? collect(json_decode(file_get_contents($filePath), true))
                : collect([]);
        });
    }

    /**
     * Apply all filters to the data
     */
    protected function applyFilters($data, Request $request, array $zipInfo)
    {
        $filters = [
            'distance' => $this->getDistanceFilter($zipInfo, $request),
            'make' => strtolower($request->input('webMakeFilterMakeInput') ?? $request->input('secondFilterMakeInputNew', '')),
            'model' => strtolower($request->input('webModelFilterInput') ?? $request->input('secondFilterModelInputNew', '')),
            'body' => $this->getBodyFilter($request),
            'fuels' => $this->getFuelFilters($request),
            'exterior' => $this->getExteriorFilters($request),
            'interior' => $this->getInteriorFilters($request),
            'drivetrain' => $this->getDrivetrainFilters($request),
            'transmission' => $this->getTransmissionFilters($request),
            'condition' => $this->getConditionFilters($request),
            'price' => $this->getPriceFilters($request),
            'mileage' => $this->getMileageFilters($request),
            'year' => $this->getYearFilters($request)
        ];

        return $data->filter(function ($item) use ($filters) {
            foreach ($filters as $key => $filter) {
                if ($filter !== null && !$this->applyFilter($item, $key, $filter)) {
                    return false;
                }
            }
            return true;
        })->sortByDesc('stock_date_formated');
    }

    /**
     * Apply sorting if requested
     */
    protected function applySorting($data, Request $request)
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

        if ($request->has('selected_sort_search') && isset($sortMapping[$request->selected_sort_search])) {
            [$sortField, $sortDirection] = $sortMapping[$request->selected_sort_search];
            return $data->sortBy($sortField, SORT_REGULAR, $sortDirection === 'desc')->values();
        }

        return $data;
    }

    /**
     * Paginate the filtered results
     */
    protected function paginateResults($data, Request $request): LengthAwarePaginator
    {
        $page = $request->get('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        return new LengthAwarePaginator(
            $data->slice($offset, $perPage)->values(),
            $data->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    /**
     * Prepare the AJAX response
     */
    protected function prepareAjaxResponse(LengthAwarePaginator $paginator, Request $request): array
    {
        $totalCount = number_format($paginator->total());
        $currentCount = $paginator->count();
        $singleCount = ($paginator->perPage() * ($paginator->currentPage() - 1)) + $currentCount;

        $view = $paginator->isEmpty()
            ? view('frontend.auto_ajax', [
                'inventories' => $paginator,
                'total_count' => $totalCount,
                'single_inventories_count' => $singleCount,
                'message' => 'Your search is not show now'
            ])->render()
            : view('frontend.cache_auto_ajax', [
                'inventories' => $paginator,
                'total_count' => $totalCount,
                'single_inventories_count' => $singleCount,
                'message' => '',
                'messageData' => '!!!'
            ])->render();

        return [
            'view' => $view,
            'pagination' => $paginator->links()->toHtml(),
            'total_count' => $totalCount,
            'message' => $paginator->isEmpty() ? 'Your search is not show now' : ''
        ];
    }

    // Helper methods for getting filter values
    protected function getBodyFilter(Request $request): ?string
    {
        if (!empty($request->input('webBodyFilter'))) {
            return strtolower($request->input('webBodyFilter'));
        }
        if (!empty($request->input('mobileBody'))) {
            return strtolower($request->input('mobileBody'));
        }
        return null;
    }

    protected function getFuelFilters(Request $request): array
    {
        $fuels = $request->input('autoWebFuelCheckbox') ?? $request->input('autoMobileFuelCheckbox', []);
        return array_map('strtolower', (array)$fuels);
    }

    protected function getExteriorFilters(Request $request): array
    {
        $colors = $request->input('autoWebExteriorColorCheckbox') ?? $request->input('autoMobileExteriorColorCheckbox', []);
        return array_map('strtolower', (array)$colors);
    }

    protected function getInteriorFilters(Request $request): array
    {
        $colors = $request->input('autoWebInteriorColorCheckbox') ?? $request->input('autoMobileInteriorColorCheckbox', []);
        return array_map('strtolower', (array)$colors);
    }

    protected function getDrivetrainFilters(Request $request): array
    {
        $drivetrains = $request->input('autoWebDriveTrainCheckbox') ?? $request->input('autoMobileDriveTrainCheckbox', []);
        return $this->mapDrivetrains($drivetrains);
    }

    protected function getTransmissionFilters(Request $request): array
    {
        $transmissions = $request->input('autoWebTransmissionCheckbox') ?? $request->input('autoMobileTransmissionCheckbox', []);
        return array_map('strtolower', (array)$transmissions);
    }

    protected function getConditionFilters(Request $request): array
    {
        $conditions = $request->input('autoWebConditionCheckbox') ?? $request->input('autoMobileTypeCheckbox', []);
        return array_map('strtolower', (array)$conditions);
    }

    protected function getPriceFilters(Request $request): array
    {
        return [
            'min' => $request->input('rangerMinPriceSlider') ?? $request->input('mobileRangerMinPriceSlider'),
            'max' => $request->input('rangerMaxPriceSlider') ?? $request->input('mobileRangerMaxPriceSlider')
        ];
    }

    protected function getMileageFilters(Request $request): array
    {
        return [
            'min' => $request->input('rangerMileageMinPriceSlider') ?? $request->input('mobileMileageRangerMinPriceSlider'),
            'max' => $request->input('rangerMileageMaxPriceSlider') ?? $request->input('mobileMileageRangerMaxPriceSlider')
        ];
    }

    protected function getYearFilters(Request $request): array
    {
        return [
            'min' => $request->input('rangerYearMinPriceSlider') ?? $request->input('mobileYearRangerMinPriceSlider'),
            'max' => $request->input('rangerYearMaxPriceSlider') ?? $request->input('mobileYearRangerMaxPriceSlider')
        ];
    }

    protected function getDistanceFilter(array $zipInfo, Request $request): ?array
    {
        if ($request->webRadios === 'Nationwide') {
            return null;
        }

        return [
            'latitude' => $zipInfo['latitude'],
            'longitude' => $zipInfo['longitude'],
            'radius' => $request->webRadios ?? $request->mobileRadios
        ];
    }

    // Other helper methods
    protected function getPriceRange(): array
    {
        return [
            'min' => (int)MainInventory::where('price', '!=', 'N/A')->min('price'),
            'max' => (int)MainInventory::where('price', '!=', 'N/A')->max('price')
        ];
    }

    protected function getMileageRange(): array
    {
        return [
            'min' => MainInventory::where('miles', '!=', 'N/A')->min('miles'),
            'max' => MainInventory::where('miles', '!=', 'N/A')->max('miles')
        ];
    }

    protected function getDistinctBodyTypes(): array
    {
        $bodies = MainInventory::distinct()->pluck('body_formated')->toArray();
        sort($bodies);
        return $bodies;
    }

    protected function getDistinctFuelTypes(): array
    {
        $fuels = MainInventory::distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();
        sort($fuels);
        return $fuels;
    }

    protected function mapDrivetrains(array $drivetrains): array
    {
        $mapping = [
            '4WD' => ['Four-wheel Drive', '4WD'],
            'AWD' => ['All-wheel Drive', 'AWD'],
            'FWD' => ['Front-wheel Drive', 'FWD'],
            'RWD' => ['Rear-wheel Drive', 'RWD'],
            'Other' => ['Unknown', '–', '----']
        ];

        $result = [];
        foreach ($drivetrains as $drive) {
            if (isset($mapping[$drive])) {
                $result = array_merge($result, $mapping[$drive]);
            }
        }

        return array_unique($result);
    }

    protected function applyFilter(array $item, string $filterType, $filterValue): bool
    {
        // If filter value is empty, skip filtering for this type
        if (empty($filterValue)) {
            return true;
        }

        switch ($filterType) {
            case 'distance':
                // Check if we have valid coordinates
                if (empty($filterValue['latitude']) || empty($filterValue['longitude'])) {
                    return true;
                }

                $distance = $this->calculateDistance(
                    $filterValue['latitude'],
                    $filterValue['longitude'],
                    $item['latitude'] ?? null,
                    $item['longitude'] ?? null
                );

                return $distance <= ($filterValue['radius'] ?? PHP_INT_MAX);

            case 'make':
            case 'model':
            case 'body':
                $field = $filterType === 'body' ? 'body_formated' : $filterType;
                return isset($item[$field]) &&
                    str_contains(strtolower($item[$field]), strtolower($filterValue));

            case 'fuels':
                if (!isset($item['fuel'])) {
                    return false;
                }
                $itemFuel = strtolower($item['fuel']);
                foreach ((array)$filterValue as $fuel) {
                    if (str_contains($itemFuel, strtolower($fuel))) {
                        return true;
                    }
                }
                return false;

            case 'exterior':
            case 'interior':
                $field = $filterType . '_color';
                return isset($item[$field]) &&
                    in_array(strtolower($item[$field]), array_map('strtolower', (array)$filterValue));

            case 'transmission':
                return isset($item['transmission']) &&
                    in_array(strtolower($item['transmission']), array_map('strtolower', (array)$filterValue));

            case 'condition':
                return isset($item['type']) &&
                    in_array(strtolower($item['type']), array_map('strtolower', (array)$filterValue));

            case 'drivetrain':
                if (!isset($item['drive_info'])) {
                    return false;
                }
                $itemDrive = strtolower($item['drive_info']);
                foreach ((array)$filterValue as $drive) {
                    if (str_contains($itemDrive, strtolower($drive))) {
                        return true;
                    }
                }
                return false;

            case 'price':
            case 'mileage':
            case 'year':
                $field = $filterType === 'year' ? 'year' : ($filterType === 'price' ? 'price' : 'miles');

                if (!isset($item[$field])) {
                    return false;
                }

                $min = $filterValue['min'] ?? null;
                $max = $filterValue['max'] ?? null;
                $value = $item[$field];

                return (is_null($min) || $value >= $min) &&
                    (is_null($max) || $value <= $max);

            default:
                return true;
        }
    }
    protected function calculateDistance(?float $lat1, ?float $lon1, ?float $lat2, ?float $lon2): float
    {
        // Validate input coordinates
        if ($lat1 === null || $lon1 === null || $lat2 === null || $lon2 === null) {
            return PHP_FLOAT_MAX; // Return maximum distance if coordinates are invalid
        }

        $earthRadius = 6371; // Earth radius in kilometers

        try {
            // Convert degrees to radians
            $latFrom = deg2rad($lat1);
            $lonFrom = deg2rad($lon1);
            $latTo = deg2rad($lat2);
            $lonTo = deg2rad($lon2);

            // Calculate differences
            $latDelta = $latTo - $latFrom;
            $lonDelta = $lonTo - $lonFrom;

            // Haversine formula
            $angle = 2 * asin(
                sqrt(
                    pow(sin($latDelta / 2), 2) +
                        cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
                )
            );

            return $angle * $earthRadius;
        } catch (\Throwable $e) {
            // Log error if needed
            // error_log("Distance calculation failed: " . $e->getMessage());
            return PHP_FLOAT_MAX; // Return maximum distance on error
        }
    }
}
