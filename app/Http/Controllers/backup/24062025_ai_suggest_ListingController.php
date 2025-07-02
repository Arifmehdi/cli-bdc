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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ListingController extends Controller
{
    use Notify;

    private $inventoryService;
    private $dealerService;
    public function __construct(InventoryServiceInterface $inventoryService, DealerService $dealerService)
    {
        $this->inventoryService = $inventoryService;
        $this->dealerService = $dealerService;
    }

    public function auto(Request $request, $param = null)
    {
        // Handle clear requests
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

        // For AJAX requests
        if ($request->ajax()) {
            return $this->handleAjaxRequest($request);
        }

        // For regular requests
        return $this->handleRegularRequest($request);
    }

    /**
     * Handle AJAX requests
     */
    protected function handleAjaxRequest(Request $request)
    {
        // Determine which data source to use
        if ($this->shouldUseJsonFile($request)) {
            return $this->handleJsonFileRequest($request);
        }
        return $this->handleDirectServerQuery($request);
    }

    /**
     * Handle regular page requests
     */
    protected function handleRegularRequest(Request $request)
    {
        $vehicles = VehicleMake::orderBy('make_name')->where('status', 1)->pluck('id', 'make_name');
        $inventory_obj = MainInventory::query();

        $data = [
            'vehicles' => $vehicles,
            'vehicles_body' => $inventory_obj->distinct()->pluck('body_formated')->toArray(),
            'vehicles_fuel_other' => $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray(),
            'searchBody' => $request->query('homeBodySearch'),
            'make_data' => $request->input('make'),
            'states' => LocationState::orderBy('state_name')->pluck('state_name', 'id'),
            'messageCookieData' => $request->cookie('messageCookieData')
        ];

        // Sort arrays
        sort($data['vehicles_fuel_other']);
        sort($data['vehicles_body']);

        return view('frontend.auto', $data);
    }

    /**
     * Check if we should use JSON file data
     */
    protected function shouldUseJsonFile(Request $request): bool
    {
        return file_exists($this->getJsonFilePath($request));
    }

    /**
     * Get the appropriate JSON file path based on request
     */
    protected function getJsonFilePath(Request $request): string
    {
        // Default file path (San Antonio)
        $defaultFile = storage_path('app/bexar_county.json');

        // If Nationwide is selected, return nationwide file
        if ($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide') {
            $nationwideFile = storage_path('app/nationwide_county.json');
            return file_exists($nationwideFile) ? $nationwideFile : $defaultFile;
        }

        // Get zip code from request
        $zipCode = $request->weblocationNewInput ?? $request->mobilelocation ?? null;

        // If no zip code provided, return default file
        if (empty($zipCode)) {
            return $defaultFile;
        }

        // Try to get county info from cache
        $zipCodeInfo = $this->getCityByZipCodeOnCache($zipCode);
        $countyName = $zipCodeInfo['county'] ?? 'unknown';

        // Generate JSON file name
        $jsonFileName = str_replace(' ', '_', strtolower($countyName)) . '_county';
        $filePath = storage_path('app/' . $jsonFileName . '.json');

        // Return file path if exists, otherwise default
        return file_exists($filePath) ? $filePath : $defaultFile;
    }
    /**
     * Handle JSON file data requests
     */
    protected function handleJsonFileRequest(Request $request)
    {

        $cacheKey = 'filtered_data_' . md5($this->getJsonFilePath($request) . serialize($request->all()));
        $cacheDuration = now()->addHours(1);

        $responseData = Cache::remember($cacheKey, $cacheDuration, function () use ($request) {
            $filteredData = $this->processJsonFile($request);

            if ($request->has('selected_sort_search')) {
                $filteredData = $this->applySorting($filteredData, $request->selected_sort_search);
            }

            return $filteredData;
        });

        $page = $request->get('page', 1);
        $perPage = 20;
        $paginatedItems = array_slice($responseData, ($page - 1) * $perPage, $perPage);

        $inventories = new LengthAwarePaginator(
            $paginatedItems,
            count($responseData),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return $this->buildAjaxResponse($inventories, 'frontend.cache_auto_ajax');
    }

    /**
     * Process JSON file with filters
     */
    protected function processJsonFile(Request $request): array
    {
        $filePath = $this->getJsonFilePath($request);
        $filters = $this->extractFiltersFromRequest($request);
        $zipInfo = $this->getZipCodeInfo($request);

        $filteredData = [];
        $stream = fopen($filePath, 'r');
        $buffer = '';
        $inObject = false;
        $objectDepth = 0;
        $objectCount = 0;
        $chunkSize = 8192; // 8KB chunks

        while (!feof($stream)) {
            $buffer .= fread($stream, $chunkSize);

            // Process complete JSON objects in buffer
            while (($pos = strpos($buffer, '{')) !== false) {
                $endPos = $this->findMatchingBrace($buffer, $pos);
                if ($endPos === false) break;

                $jsonStr = substr($buffer, $pos, $endPos - $pos + 1);
                $buffer = substr($buffer, $endPos + 1);

                $data = json_decode($jsonStr, true);

                if ($data && $this->matchesFilters($data, $filters, $zipInfo)) {
                    $filteredData[] = $data;
                }

                // Memory management
                if (++$objectCount % 1000 === 0) {
                    gc_collect_cycles();
                }
            }
        }

        fclose($stream);
        return $filteredData;
    }

    /**
     * Find matching brace for JSON parsing
     */
    protected function findMatchingBrace(string $str, int $start): int|false
    {
        $count = 1;
        for ($i = $start + 1; $i < strlen($str); $i++) {
            if ($str[$i] === '{') $count++;
            if ($str[$i] === '}') $count--;
            if ($count === 0) return $i;
        }
        return false;
    }

    /**
     * Extract all filters from request
     */
    protected function extractFiltersFromRequest(Request $request): array
    {
        $cookieSetParam = $this->cookieSetParam($request);

        return [
            'make' => !empty($request->input('webMakeFilterMakeInput'))
                ? strtolower($request->input('webMakeFilterMakeInput'))
                : strtolower($request->input('secondFilterMakeInputNew', '')),

            'model' => !empty($request->input('webModelFilterInput'))
                ? strtolower($request->input('webModelFilterInput'))
                : strtolower($request->input('secondFilterModelInputNew', '')),

            'body' => $this->getFilterValue($request, [
                'webBodyFilter',
                'mobileBody'
            ], $cookieSetParam['homebodySearch'] ?? null),

            'fuels' => $this->getCheckboxValues($request, [
                'autoWebFuelCheckbox',
                'autoMobileFuelCheckbox'
            ], $cookieSetParam['homefuelSearch'] ?? null),

            'exterior_colors' => $this->getCheckboxValues($request, [
                'autoWebExteriorColorCheckbox',
                'autoMobileExteriorColorCheckbox'
            ]),

            'interior_colors' => $this->getCheckboxValues($request, [
                'autoWebInteriorColorCheckbox',
                'autoMobileInteriorColorCheckbox'
            ]),

            'drivetrain' => $this->drivetrainMapping(
                $this->getCheckboxValues($request, [
                    'autoWebDriveTrainCheckbox',
                    'autoMobileDriveTrainCheckbox'
                ])
            ),

            'transmission' => $this->getCheckboxValues($request, [
                'autoWebTransmissionCheckbox',
                'autoMobileTransmissionCheckbox'
            ]),

            'condition' => $this->getCheckboxValues($request, [
                'autoWebConditionCheckbox',
                'autoMobileTypeCheckbox'
            ]),

            'min_price' => $request->input('rangerMinPriceSlider', null) ?? $request->input('mobileRangerMinPriceSlider', null),
            'max_price' => $request->input('rangerMaxPriceSlider', null) ?? $request->input('mobileRangerMaxPriceSlider', null),
            'min_mileage' => $request->input('rangerMileageMinPriceSlider', null) ?? $request->input('mobileMileageRangerMinPriceSlider', null),
            'max_mileage' => $request->input('rangerMileageMaxPriceSlider', null) ?? $request->input('mobileMileageRangerMaxPriceSlider', null),
            'min_year' => $request->input('rangerYearMinPriceSlider', null) ?? $request->input('mobileYearRangerMinPriceSlider', null),
            'max_year' => $request->input('rangerYearMaxPriceSlider', null) ?? $request->input('mobileYearRangerMaxPriceSlider', null),
        ];
    }

    /**
     * Get filter value from request with fallbacks
     */
    protected function getFilterValue(Request $request, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (!empty($request->input($key))) {
                return strtolower($request->input($key));
            }
        }
        return $default ? strtolower($default) : null;
    }

    /**
     * Get checkbox values from request
     */
    protected function getCheckboxValues(Request $request, array $keys, $default = null): array
    {
        foreach ($keys as $key) {
            if (!empty($request->input($key))) {
                return array_map('strtolower', (array)$request->input($key));
            }
        }
        return $default ? [strtolower($default)] : [];
    }

    /**
     * Get zip code info if needed
     */
    protected function getZipCodeInfo(Request $request): ?array
    {
        if ($request->webRadios != 'Nationwide') {
            $zip_code_inform = $request->weblocationNewInput ?? $request->mobilelocation;
            $zip_code_info = $this->getCityByZipCodeOnCache($zip_code_inform);

            return [
                'latitude' => $zip_code_info['latitude'],
                'longitude' => $zip_code_info['longitude'],
                'radius' => $request->webRadios ?? $request->mobileRadios
            ];
        }

        return null;
    }

    /**
     * Check if data matches all filters
     */
    protected function matchesFilters(array $data, array $filters, ?array $zipInfo): bool
    {

        // // Check distance first (most likely to filter out) marif san antonilo cache check
        // if ($zipInfo && !$this->isWithinRadius($data, $zipInfo)) {
        //     return false;
        // }

        // Check other filters
        return $this->matchesStringFilter($data['make'] ?? '', $filters['make']) &&
            $this->matchesStringFilter($data['model'] ?? '', $filters['model']) &&
            $this->matchesStringFilter($data['body_formated'] ?? '', $filters['body']) &&
            $this->matchesArrayFilter($data['fuel'] ?? '', $filters['fuels']) &&
            $this->matchesArrayFilter($data['exterior_color'] ?? '', $filters['exterior_colors']) &&
            $this->matchesArrayFilter($data['interior_color'] ?? '', $filters['interior_colors']) &&
            $this->matchesArrayFilter($data['drive_info'] ?? '', $filters['drivetrain']) &&
            $this->matchesArrayFilter($data['transmission'] ?? '', $filters['transmission']) &&
            $this->matchesArrayFilter($data['type'] ?? '', $filters['condition']) &&
            $this->matchesRangeFilter($data['price'] ?? null, $filters['min_price'], $filters['max_price']) &&
            $this->matchesRangeFilter($data['miles'] ?? null, $filters['min_mileage'], $filters['max_mileage']) &&
            $this->matchesRangeFilter($data['year'] ?? null, $filters['min_year'], $filters['max_year']);
    }

    /**
     * Check if location is within radius
     */
    protected function isWithinRadius(array $data, array $zipInfo): bool
    {
        if (empty($data['latitude']) || empty($data['longitude'])) {
            return false;
        }

        $distance = $this->calculateDistance(
            $zipInfo['latitude'],
            $zipInfo['longitude'],
            $data['latitude'],
            $data['longitude']
        );

        return $distance <= $zipInfo['radius'];
    }

    /**
     * Check string filter match
     */
    protected function matchesStringFilter(?string $value, ?string $filter): bool
    {
        if ($filter === null) return true;
        if ($value === null) return false;
        return str_contains(strtolower($value), $filter);
    }

    /**
     * Check array filter match
     */
    protected function matchesArrayFilter(?string $value, array $filters): bool
    {
        if (empty($filters)) return true;
        if ($value === null) return false;
        return in_array(strtolower($value), $filters);
    }

    /**
     * Check range filter match
     */
    protected function matchesRangeFilter($value, $min, $max): bool
    {
        if ($value === null) return true;
        if ($min !== null && $value < $min) return false;
        if ($max !== null && $value > $max) return false;
        return true;
    }

    /**
     * Apply sorting to data
     */
    protected function applySorting(array $data, string $sortOption): array
    {
        $sortMapping = [
            'datecreated|desc' => ['stock_date_formated', SORT_DESC],
            'datecreated|asc' => ['stock_date_formated', SORT_ASC],
            'searchprice|asc' => ['price', SORT_ASC],
            'searchprice|desc' => ['price', SORT_DESC],
            'mileage|asc' => ['miles', SORT_ASC],
            'mileage|desc' => ['miles', SORT_DESC],
            'modelyear|asc' => ['year', SORT_ASC],
            'modelyear|desc' => ['year', SORT_DESC],
            'payment|asc' => ['payment_price', SORT_ASC],
            'payment|desc' => ['payment_price', SORT_DESC]
        ];

        if (isset($sortMapping[$sortOption])) {
            [$sortField, $sortDirection] = $sortMapping[$sortOption];

            usort($data, function ($a, $b) use ($sortField, $sortDirection) {
                $valA = $a[$sortField] ?? null;
                $valB = $b[$sortField] ?? null;

                if ($sortDirection === SORT_ASC) {
                    return $valA <=> $valB;
                }
                return $valB <=> $valA;
            });
        }

        return $data;
    }

    /**
     * Handle direct server query fallback
     */
    protected function handleDirectServerQuery(Request $request)
    {
        $infor = $this->directServerQuery($request);
        return $this->buildAjaxResponse($infor['inventories'], 'frontend.auto_ajax', $infor['message']);
    }

    /**
     * Build standardized AJAX response
     */

    protected function buildAjaxResponse($inventories, string $view, ?string $message = ''): JsonResponse
    {
        $current_page_count = $inventories->count();
        $total_count = number_format($inventories->total());

        // Correct calculation for showing ranges
        $start_item = ($inventories->currentPage() - 1) * $inventories->perPage() + 1;
        $end_item = min($start_item + $inventories->perPage() - 1, $inventories->total());

        $view = view($view, [
            'inventories' => $inventories,
            'total_count' => $total_count,
            'single_inventories_count' => "$start_item-$end_item", // Now shows correct range
            'message' => $message ?? '',
            'messageData' => !empty($message) ? '@' : '!!!'
        ])->render();

        return response()->json([
            'view' => $view,
            'pagination' => $inventories->links()->toHtml(),
            'total_count' => $total_count,
            'message' => $message ?? ''
        ]);
    }


    private function fetchCountyName($zipCode)
    {
        $countyName = LocationZip::where('zip_code', $zipCode)->value('county');
        if ($countyName) {
            $data =  $countyName;
        } else {
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

        // dd($zipCode);
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

            Cookie::queue('zipcode', $zipCode, 60);
        } else {
            // Fetch from OpenCage API if not found in database
            // $countryCode = 'us';
            // $apiKey = "4b84ff4ad9a74c79ad4a1a945a4e5be1";
            // $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key={$apiKey}";

            // $response = file_get_contents($url);
            // $zip_location_data = json_decode($response, true);

            // if (isset($zip_location_data['results'][0]['geometry'])) {

            //     $state_name = $zip_location_data['results'][0]['components']['state'] ?? 'UA';
            //     $state_code = $zip_location_data['results'][0]['components']['state_code'] ?? 'UA';
            //     $city_name = $zip_location_data['results'][0]['components']['_normalized_city'] ?? 'UA';
            //     $latitude = $zip_location_data['results'][0]['geometry']['lat'] ?? null;
            //     $longitude = $zip_location_data['results'][0]['geometry']['lng'] ?? null;
            //     $zipCode = $zip_location_data['results'][0]['components']['postcode'] ?? $zipCode;
            //     $url = $zip_location_data['results'][0]['annotations']['OSM']['url'] ?? null;

            //     // // Check if state already exists
            //     // $existingState = DB::table('location_states')->where('state_name', $state_name)->first();

            //     // // If the state exists, use the existing state_id and batch_no
            //     // if ($existingState) {
            //     //     $location_state_id = $existingState->id;  // Use the existing state's ID // Use the existing batch_no
            //     // } else {
            //     //     // Get the maximum batch number from location_states
            //     //     $maxBatchNo = DB::table('location_states')->max('batch_no');
            //     //     // Set new batch number (if exists, increment by 1; otherwise, set to 100)
            //     //     $batch_no = $maxBatchNo ? $maxBatchNo + 1 : 100;

            //     //     // Insert into location_states and get ID
            //     //     $location_state_id = DB::table('location_states')->insertGetId([
            //     //         'state_name' => $state_name,    // Full state name
            //     //         'short_name' => $state_code,    // State code
            //     //         'sales_tax' => 8,
            //     //         'status' => 1,
            //     //         'batch_no' => $batch_no,
            //     //         'created_at' => now(),
            //     //         'updated_at' => now()
            //     //     ]);
            //     // }

            //     // // Check if city already exists
            //     // $existingCity = DB::table('location_cities')
            //     //                 ->where('location_state_id', $location_state_id)
            //     //                 ->where('city_name', $city_name)
            //     //                 ->first();

            //     // // If the city exists, use the existing city_id, otherwise insert the new city
            //     // if ($existingCity) {
            //     //     $location_city_id = $existingCity->id;  // Use the existing city's ID
            //     // } else {
            //     //     // Insert into location_cities and get ID
            //     //     $location_city_id = DB::table('location_cities')->insertGetId([
            //     //         'location_state_id' => $location_state_id,
            //     //         'city_name' => $city_name,
            //     //         'latitude' => $latitude,
            //     //         'longitude' => $longitude,
            //     //         'sales_tax' => 8,
            //     //         'status' => 1,
            //     //         'created_at' => now(),
            //     //         'updated_at' => now()
            //     //     ]);
            //     // }

            //     // // Check if location_zip already exists for the given city and zip code
            //     // $existingZip = DB::table('location_zips')
            //     //                 ->where('location_city_id', $location_city_id)
            //     //                 ->where('zip_code', $zipCode)
            //     //                 ->first();

            //     // // If the location_zip exists, do not insert, else insert new location_zip
            //     // if (!$existingZip) {
            //     //     DB::table('location_zips')->insert([
            //     //         'location_city_id' => $location_city_id,
            //     //         'latitude' => $latitude,
            //     //         'longitude' => $longitude,
            //     //         'zip_code' => $zipCode,
            //     //         'sales_tax' => 8,
            //     //         'src_url' => $url,
            //     //         'status' => 1,
            //     //         'created_at' => now(),
            //     //         'updated_at' => now()
            //     //     ]);
            //     // }
            // }


            $apiKey = '4b84ff4ad9a74c79ad4a1a945a4e5be1';
            $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},us&key={$apiKey}";

            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();

                if (isset($data['results'][0]['geometry'])) {
                    $geometry = $data['results'][0]['geometry'];

                    $state_info = $data['results'][0]['components']['state'];
                    $short_name_info = $data['results'][0]['components']['state_code'];
                    $county_info = $data['results'][0]['components']['county'];

                    $location_state_data = LocationState::firstOrCreate(
                        ['state_name' => $state_info], // Check if a record exists with the given state name
                        ['short_name' => $short_name_info] // Default value for short_name if the record is created
                    );
                    $location_state_id = $location_state_data->id;

                    $location_cities_data = LocationCity::firstOrCreate(
                        ['location_state_id' => $location_state_id],
                        ['city_name' => $state_info],
                        ['latitude' => $geometry['lat']],
                        ['longitude' => $geometry['lng']]
                    );
                    $location_cities_id = $location_cities_data->id;

                    $location_zips_data = LocationZip::firstOrCreate(
                        ['location_city_id' => $location_cities_id],
                        ['county' => $county_info],
                        ['latitude' => $geometry['lat']],
                        ['longitude' => $geometry['lng']],
                        ['zip_code' => $zipCode],
                        ['sales_tax' => 8],
                        ['url' => $url],
                    );
                    Cookie::queue('zipcode', $zipCode, 60);
                    // Return the newly created data
                    return [
                        'zip_code' => $location_zips_data->zip_code,
                        'latitude' => $location_zips_data->latitude,
                        'longitude' => $location_zips_data->longitude
                    ];
                } else {
                    // If API fails, return default paginated results
                    return [
                        'inventories' => $query->orderByDesc('id')->paginate(20),
                        'message' => "Invalid Zip Code"
                    ];
                }
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

    private function drivetrainMapping($data)
    {
        if ($data === null) {
            return []; // Return an empty array if data is null
        }

        // Mapping array
        $driveTypeMapping = [
            '4WD' => ['Four-wheel Drive', '4WD'],
            'AWD' => ['All-wheel Drive', 'AWD'],
            'FWD' => ['Front-wheel Drive', 'FWD'],
            'RWD' => ['Rear-wheel Drive', 'RWD'],
            'Other' => ['Unknown', '–', '----']
        ];

        $mappedValues = [];

        // Map selected checkboxes to database values
        foreach ($data as $value) {
            if (isset($driveTypeMapping[$value])) {
                $mappedValues = array_merge($mappedValues, $driveTypeMapping[$value]);
            }
        }

        // Remove duplicate values
        $mappedValues = array_unique($mappedValues);
        // dd($mappedValues);
        return $mappedValues; // Reset array keys
    }

    // private function getCityByZipCodeOnCache($zip_code_inform){
    //     // $zip_code_inform = 78702;

    //     $cacheFilePath = storage_path('app/zip_county_data.json');

    //     if (file_exists($cacheFilePath)) {
    //         // Read and decode the JSON file
    //         $jsonData = file_get_contents($cacheFilePath);
    //         $info = collect(json_decode($jsonData, true));

    //         // Check if the zip code exists in JSON data
    //         $matchingData = $info->where('zip_code', $zip_code_inform);
    //         if ($matchingData->isNotEmpty()) {
    //             $countyData = $matchingData->values()->first();
    //             // dd($countyData);
    //             return $countyData;
    //             // dd($matchingData, $matchingData->values()[0]['zip_code']);
    //             // return response()->json($matchingData->values()); // Return matching record
    //         }else{
    //             return [
    //                 'county' => 'Unknown',
    //                 'zip_code' => $zip_code_inform,
    //                 'latitude' => null,
    //                 'longitude' => null,
    //             ];
    //             // return 'Unknown';
    //             // dd('does not match ');
    //         }

    //     }else{
    //         dd('query fom direct server');
    //     }
    // }

    private function getCityByZipCodeOnCache($zip_code_inform)
    {
        $cacheFilePath = storage_path('app/zip_county_data.json');

        // First try to get from local cache
        if (file_exists($cacheFilePath)) {
            $jsonData = file_get_contents($cacheFilePath);
            $info = collect(json_decode($jsonData, true));

            $matchingData = $info->where('zip_code', $zip_code_inform);

            if ($matchingData->isNotEmpty()) {
                return $matchingData->values()->first();
            }
        }

        // If not found in cache, query from server
        return $this->queryZipCodeFromServer($zip_code_inform);
    }

    private function queryZipCodeFromServer($zip_code)
    {
        try {
            // Example API call - replace with your actual server endpoint
            $response = Http::timeout(10)->get('https://your-server-api.com/zipcodes', [
                'zip' => $zip_code
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Cache the new data for future use
                $this->updateZipCodeCache($zip_code, $data);

                return [
                    'county' => $data['county'] ?? 'Unknown',
                    'zip_code' => $zip_code,
                    'latitude' => $data['latitude'] ?? null,
                    'longitude' => $data['longitude'] ?? null
                ];
            }
        } catch (\Exception $e) {
            // Log error if needed
            Log::error("Failed to query zip code from server: " . $e->getMessage());
        }

        // Default fallback if all fails
        return [
            'county' => 'Unknown',
            'zip_code' => $zip_code,
            'latitude' => null,
            'longitude' => null
        ];
    }

    private function updateZipCodeCache($zip_code, $newData)
    {
        $cacheFilePath = storage_path('app/zip_county_data.json');
        $existingData = [];

        if (file_exists($cacheFilePath)) {
            $existingData = json_decode(file_get_contents($cacheFilePath), true) ?? [];
        }

        // Check if this zip already exists
        $exists = false;
        foreach ($existingData as &$item) {
            if ($item['zip_code'] == $zip_code) {
                $item = array_merge($item, $newData);
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $existingData[] = [
                'zip_code' => $zip_code,
                'county' => $newData['county'] ?? 'Unknown',
                'latitude' => $newData['latitude'] ?? null,
                'longitude' => $newData['longitude'] ?? null
            ];
        }

        // Save updated cache
        file_put_contents($cacheFilePath, json_encode($existingData));
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth radius in kilometers

        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    private function calculateDistanceData($zip_code_inform)
    {
        // $zip_code_inform = 78702;
        $cacheFilePath = storage_path('app/zip_county_data.json');
        if (file_exists($cacheFilePath)) {
            // Read and decode the JSON file
            $jsonData = file_get_contents($cacheFilePath);
            $info = collect(json_decode($jsonData, true));

            // Check if the zip code exists in JSON data
            $matchingData = $info->where('zip_code', $zip_code_inform);
            if ($matchingData->isNotEmpty()) {

                $firstMatch = $matchingData->first();
                // // Extract target latitude and longitude from the request
                $latitude = $firstMatch['latitude'];
                $longitude = $firstMatch['longitude'];
                $zip_radios = $firstMatch['zip_code'];
                $zip_radios = 75;

                // // Calculate distance for each item in the cached data
                // $info = $info->map(function ($data) use ($latitude, $longitude) {
                //     $data['distance'] = $this->calculateDistance($latitude, $longitude, $data['latitude'], $data['longitude']);
                //     return $data;
                // });
                $countyData = $matchingData->values()[0]['county'];
                // dd($countyData);
                return $countyData;
                // dd($matchingData, $matchingData->values()[0]['zip_code']);
                // return response()->json($matchingData->values()); // Return matching record
            } else {
                return 'Unknown';
                // dd('does not match ');
            }
        }
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
        Cookie::queue('zipcode', $homeLocationSearch, 6 * 30 * 24 * 60);
        // Cache::forever('zipcode', $homeLocationSearch);
        Cookie::queue('searchData', json_encode($searchData), 6 * 60 * 24 * 30);
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



    private function getCityByZipCode($zip_code_inform)
    {


        // LocationZip
        $austin_travis_zip_code = [
            "73301", "73344", "78617", "78645", "78651", "78652", "78653", "78660", "78669", "78691",
            "78701", "78702", "78703", "78704", "78705", "78708", "78709", "78710", "78711", "78712",
            "78713", "78714", "78715", "78716", "78718", "78719", "78720", "78721", "78722", "78723",
            "78724", "78725", "78726", "78727", "78728", "78730", "78731", "78732", "78733", "78734",
            "78735", "78736", "78737", "78738", "78739", "78741", "78742", "78744", "78745", "78746",
            "78747", "78748", "78749", "78750", "78751", "78752", "78753", "78754", "78755", "78756",
            "78757", "78758", "78759", "78760", "78761", "78762", "78763", "78764", "78765", "78766",
            "78767", "78768", "78769", "78772", "78773", "78774", "78778", "78779", "78783", "78799"
        ];

        $charlotte_mecklenburg_zip_codes = [
            "23915", "23917", "23919", "23924", "23927", "23950", "23968", "23970",
            "24529", "24580", "28031", "28036", "28070", "28078", "28104", "28105",
            "28106", "28126", "28130", "28134", "28201", "28202", "28203", "28204",
            "28205", "28206", "28207", "28208", "28209", "28210", "28211", "28212",
            "28213", "28214", "28215", "28216", "28217", "28218", "28219", "28220",
            "28221", "28222", "28223", "28224", "28226", "28227", "28228", "28229",
            "28230", "28231", "28232", "28233", "28234", "28235", "28236", "28237",
            "28241", "28242", "28243", "28244", "28246", "28247", "28250", "28253",
            "28254", "28255", "28256", "28258", "28260", "28262", "28263", "28265",
            "28266", "28269", "28270", "28272", "28273", "28274", "28275", "28277",
            "28278", "28280", "28281", "28282", "28284", "28285", "28287", "28288",
            "28289", "28290", "28296", "28297", "28299"
        ];

        $chicago_cook_zipCodes = [
            "55604", "55605", "55606", "55612", "55613", "55615", "60004", "60005",
            "60006", "60007", "60008", "60009", "60016", "60017", "60018", "60019",
            "60022", "60025", "60029", "60038", "60043", "60053", "60055", "60056",
            "60062", "60065", "60067", "60068", "60070", "60074", "60076", "60077",
            "60078", "60082", "60090", "60091", "60093", "60094", "60095", "60103",
            "60104", "60107", "60130", "60131", "60141", "60153", "60154", "60155",
            "60159", "60160", "60161", "60162", "60163", "60164", "60165", "60168",
            "60171", "60173", "60176", "60179", "60193", "60194", "60195", "60196",
            "60201", "60202", "60203", "60204", "60208", "60209", "60301", "60302",
            "60303", "60304", "60305", "60402", "60406", "60409", "60411", "60412",
            "60415", "60418", "60419", "60422", "60425", "60426", "60429", "60430",
            "60438", "60443", "60445", "60452", "60453", "60454", "60455", "60456",
            "60457", "60458", "60459", "60461", "60462", "60463", "60464", "60465",
            "60466", "60467", "60469", "60471", "60472", "60473", "60475", "60476",
            "60477", "60478", "60480", "60482", "60499", "60501", "60513", "60525",
            "60526", "60534", "60546", "60558", "60601", "60602", "60603", "60604",
            "60605", "60606", "60607", "60608", "60609", "60610", "60611", "60612",
            "60613", "60614", "60615", "60616", "60617", "60618", "60619", "60620",
            "60621", "60622", "60623", "60624", "60625", "60626", "60628", "60629",
            "60630", "60631", "60632", "60633", "60634", "60636", "60637", "60638",
            "60639", "60640", "60641", "60643", "60644", "60645", "60646", "60647",
            "60649", "60651", "60652", "60653", "60654", "60655", "60656", "60657",
            "60659", "60660", "60661", "60664", "60668", "60669", "60670", "60673",
            "60674", "60675", "60678", "60680", "60681", "60684", "60685", "60687",
            "60690", "60691", "60693", "60694", "60697", "60699", "60701", "60707",
            "60712", "60714", "60803", "60804", "60805", "60827", "31620", "31627",
            "31637", "31647"
        ];

        $columbus_franklin_zipCodes = [
            "1054", "1072", "1093", "1301", "1302", "1330", "1337", "1338", "1339", "1340", "1341", "1342", "1344", "1346", "1347", "1350", "1351",
            "1354", "1355", "1360", "1364", "1367", "1370", "1373", "1375", "1376", "1378", "1379", "1380", "17201", "17202", "17210", "17214", "17217",
            "17219", "17220", "17221", "17222", "17224", "17225", "17231", "17232", "17235", "17236", "17237", "17244", "17246", "17247", "17250", "17251",
            "17252", "17254", "17256", "17261", "17262", "17263", "17265", "17268", "17271", "17272", "24065", "24067", "24088", "24092", "24102", "24137",
            "24146", "24151", "24176", "24184", "27508", "27525", "27549", "27596", "30520", "30521", "30553", "30639", "30662", "32318", "32320", "32322",
            "32323", "32328", "32329", "35571", "35581", "35582", "35585", "35593", "35653", "35654", "37306", "37318", "37324", "37330", "37345", "37375",
            "37376", "37383", "37398", "39630", "39647", "39653", "39661", "40601", "40602", "40603", "40604", "40618", "40619", "40620", "40621", "40622",
            "4225", "4227", "4234", "4239", "4262", "4285", "4294", "43201", "43202", "43203", "43204", "43205", "43206", "43207", "43209", "43210", "43211",
            "43212", "43213", "43214", "43215", "43216", "43217", "43218", "43219", "43220", "43221", "43222", "43223", "43224", "43226", "43227", "43228",
            "43229", "43230", "43231", "43232", "43234", "43235", "43236", "43240", "43251", "43260", "43266", "43268", "43270", "43271", "43272", "43279",
            "43287", "43291", "47003", "47010", "47012", "47016", "47024", "47030", "47035", "47036", "4936", "4938", "4940", "4947", "4955", "4956", "4964",
            "4966", "4970", "4982", "4983", "4984", "4992", "50041", "50227", "50420", "50427", "50431", "50441", "50452", "50475", "50633", "5441", "5447",
            "5448", "5450", "5454", "5455", "5457", "5459", "5460", "5470", "5471", "5476", "5478", "5479", "5481", "5483", "5485", "5488", "62812", "62819",
            "62822", "62825", "62836", "62840", "62856", "62865", "62874", "62884", "62890", "62891", "62896", "62897", "62983", "62999", "63013", "63014",
            "63015", "63037", "63039", "63055", "63056", "63060", "63061", "63068", "63069", "63072", "63073", "63077", "63079", "63080", "63084", "63089",
            "63090", "66042", "66067", "66076", "66078", "66079", "66080", "66092", "66095", "68929", "68932", "68939", "68947", "68960", "68972", "68981",
            "71219", "71230", "71243", "71249", "71295", "71324", "71336", "71378", "72820", "72821", "72928", "72930", "72933", "72949", "75457", "75480",
            "75487", "83228", "83232", "83237", "83263", "83283", "83286", "99301", "99302", "99326", "99330", "99335", "99343"
        ];

        $dallas_zipCodes = [
            "36701", "36702", "36703", "36758", "36759", "36761", "36767", "36773", "36775", "36785",
            "50003", "50038", "50039", "50063", "50066", "50069", "50070", "50109", "50146", "50167",
            "50220", "50233", "50261", "50263", "50276", "65590", "65622", "65685", "65764", "65767",
            "65783", "71725", "71742", "71748", "71763", "75001", "75006", "75011", "75014", "75015",
            "75016", "75017", "75019", "75030", "75038", "75039", "75040", "75041", "75042", "75043",
            "75044", "75045", "75046", "75047", "75048", "75049", "75050", "75051", "75052", "75053",
            "75054", "75059", "75060", "75061", "75062", "75063", "75064", "75080", "75081", "75082",
            "75083", "75085", "75088", "75089", "75098", "75099", "75104", "75106", "75115", "75116",
            "75123", "75134", "75137", "75138", "75141", "75146", "75149", "75150", "75159", "75172",
            "75180", "75181", "75182", "75185", "75187", "75201", "75202", "75203", "75204", "75205",
            "75206", "75207", "75208", "75209", "75210", "75211", "75212", "75214", "75215", "75216",
            "75217", "75218", "75219", "75220", "75221", "75222", "75223", "75224", "75225", "75226",
            "75227", "75228", "75229", "75230", "75231", "75232", "75233", "75234", "75235", "75236",
            "75237", "75238", "75240", "75241", "75242", "75243", "75244", "75246", "75247", "75248",
            "75249", "75250", "75251", "75252", "75253", "75254", "75260", "75261", "75262", "75263",
            "75264", "75265", "75266", "75267", "75270", "75275", "75277", "75283", "75284", "75285",
            "75287", "75301", "75303", "75312", "75313", "75315", "75320", "75326", "75336", "75339",
            "75342", "75354", "75355", "75356", "75357", "75358", "75359", "75360", "75367", "75368",
            "75370", "75371", "75372", "75373", "75374", "75376", "75378", "75379", "75380", "75381",
            "75382", "75389", "75390", "75391", "75392", "75393", "75394", "75395", "75397", "75398"
        ];

        $denver_zipCodes = [
            "36701", "36702", "36703", "36758", "36759", "36761", "36767", "36773", "36775", "36785",
            "50003", "50038", "50039", "50063", "50066", "50069", "50070", "50109", "50146", "50167",
            "50220", "50233", "50261", "50263", "50276", "65590", "65622", "65685", "65764", "65767",
            "65783", "71725", "71742", "71748", "71763", "75001", "75006", "75011", "75014", "75015",
            "75016", "75017", "75019", "75030", "75038", "75039", "75040", "75041", "75042", "75043",
            "75044", "75045", "75046", "75047", "75048", "75049", "75050", "75051", "75052", "75053",
            "75054", "75059", "75060", "75061", "75062", "75063", "75064", "75080", "75081", "75082",
            "75083", "75085", "75088", "75089", "75098", "75099", "75104", "75106", "75115", "75116",
            "75123", "75134", "75137", "75138", "75141", "75146", "75149", "75150", "75159", "75172",
            "75180", "75181", "75182", "75185", "75187", "75201", "75202", "75203", "75204", "75205",
            "75206", "75207", "75208", "75209", "75210", "75211", "75212", "75214", "75215", "75216",
            "75217", "75218", "75219", "75220", "75221", "75222", "75223", "75224", "75225", "75226",
            "75227", "75228", "75229", "75230", "75231", "75232", "75233", "75234", "75235", "75236",
            "75237", "75238", "75240", "75241", "75242", "75243", "75244", "75246", "75247", "75248",
            "75249", "75250", "75251", "75252", "75253", "75254", "75260", "75261", "75262", "75263",
            "75264", "75265", "75266", "75267", "75270", "75275", "75277", "75283", "75284", "75285",
            "75287", "75301", "75303", "75312", "75313", "75315", "75320", "75326", "75336", "75339",
            "75342", "75354", "75355", "75356", "75357", "75358", "75359", "75360", "75367", "75368",
            "75370", "75371", "75372", "75373", "75374", "75376", "75378", "75379", "75380", "75381",
            "75382", "75389", "75390", "75391", "75392", "75393", "75394", "75395", "75397", "75398",
            "80201", "80202", "80203", "80204", "80205", "80206", "80207", "80208", "80209", "80210",
            "80211", "80212", "80216", "80217", "80218", "80219", "80220", "80222", "80223", "80224",
            "80227", "80230", "80231", "80235", "80236", "80237", "80238", "80239", "80243", "80244",
            "80246", "80247", "80248", "80249", "80250", "80251", "80252", "80256", "80257", "80259",
            "80261", "80262", "80263", "80264", "80265", "80266", "80271", "80273", "80274", "80281",
            "80290", "80291", "80293", "80294", "80299"
        ];

        $detroit_wayne_zipCodes = [
            "13143", "13146", "13154", "14413", "14433", "14449", "14489", "14502", "14505", "14513",
            "14516", "14519", "14520", "14522", "14538", "14542", "14551", "14555", "14563", "14568",
            "14589", "14590", "14593", "18405", "18417", "18424", "18427", "18428", "18431", "18436",
            "18437", "18438", "18439", "18443", "18445", "18449", "18453", "18454", "18455", "18456",
            "18459", "18460", "18461", "18462", "18463", "18469", "18472", "18473", "25507", "25511",
            "25512", "25514", "25517", "25530", "25534", "25535", "25555", "25562", "25570", "25669",
            "25699", "25709", "28460", "44214", "44217", "44230", "44270", "44276", "44287", "44606",
            "44618", "44627", "44636", "44645", "44659", "44667", "44676", "44677", "44691", "44694",
            "44696", "45316", "48201", "48202", "48203", "48204", "48205", "48206", "48207", "48208",
            "48209", "48210", "48211", "48212", "48213", "48214", "48215", "48216", "48217", "48218",
            "48219", "48221", "48222", "48223", "48224", "48225", "48226", "48227", "48228", "48229",
            "48230", "48231", "48232", "48233", "48234", "48235", "48236", "48238", "48239", "48240",
            "48242", "48243", "48244", "48260", "48264", "48266", "48267", "48268", "48275", "48277",
            "48278", "48279", "48288", "48168", "48193", "39322", "39324", "39367", "63632", "63934",
            "63944", "63950", "63951", "63952", "63956", "63957", "63964", "63966", "63967", "63763",
            "68787", "68723", "68740", "68790", "14502", "14505", "14522", "14563", "14538", "14551",
            "14542", "14555", "14516", "14568", "14590", "27530", "27532", "27533", "27534", "27830",
            "27863", "28333", "28365", "28578", "27531", "53515", "52590", "52583", "50008", "50052",
            "47324", "47327", "47330", "47335", "47339", "47341", "47345", "47346", "47357", "47374",
            "47375", "47392", "47393", "47370", "42633", "48101", "48111", "48112", "48120", "48121",
            "48123", "48124", "48125", "48126", "48127", "48128", "48134", "48135", "48136", "48138",
            "48141", "48146", "48150", "48151", "48152", "48153", "48154", "48164", "48167", "48170",
            "48173", "48174", "48180", "48183", "48184", "48185", "48186", "48187", "48188", "48192",
            "48195", "48201", "48202", "48203", "48204", "48205", "48206", "48207", "48208", "48209",
            "48210", "48211", "48212", "48213", "48214", "48215", "48216", "48217", "48218", "48219",
            "48220", "48224", "48225", "48233", "48240", "48246", "48255", "48260", "48265", "48270",
            "48271", "50008", "50052", "50060", "50068", "50123", "50147", "50165", "50349", "50440",
            "51551", "52144", "51335", "52301", "52360", "52999", "52967", "52947", "52980", "52963",
            "53515"
        ];

        $ell_passo_zip_code = [
            "79821", "79835", "79836", "79838", "79849", "79853", "79901", "79902", "79903", "79904",
            "79905", "79906", "79907", "79908", "79910", "79911", "79912", "79913", "79914", "79916",
            "79917", "79918", "79920", "79922", "79923", "79924", "79926", "79927", "79928", "79929",
            "79930", "79931", "79932", "79934", "79935", "79936", "79937", "79938", "79940", "79941",
            "79942", "79943", "79944", "79945", "79946", "79947", "79948", "79949", "79950", "79951",
            "79952", "79953", "79954", "79955", "79958", "79960", "79961", "79968", "79976", "79978",
            "79980", "79990", "79995", "79996", "79997", "79998", "79999", "80808", "80809", "80817",
            "80819", "80831", "80832", "80833", "80840", "80841", "80864", "80808", "80809", "80817",
            "80819", "80831", "80832", "80833", "80840", "80841", "80901", "80902", "80903", "80904",
            "80905", "80906", "80907", "80908", "80909", "80910", "80911", "80912", "80913", "80914",
            "80915", "80916", "80917", "80918", "80919", "80920", "80921", "80922", "80923", "80924",
            "80925", "80926", "80927", "80928", "80929", "80930", "80931", "80932", "80933", "80934",
            "80935", "80936", "80937", "80938", "80939", "80941", "80942", "80944", "80946", "80947",
            "80949", "80950", "80960", "80962", "80970", "80977", "80995", "80997", "88510", "88511",
            "88512", "88513", "88514", "88515", "88517", "88518", "88519", "88520", "88521", "88523",
            "88524", "88525", "88526", "88527", "88528", "88529", "88530", "88531", "88532", "88533",
            "88534", "88535", "88536", "88538", "88539", "88540", "88541", "88542", "88543", "88544",
            "88545", "88546", "88547", "88548", "88549", "88550", "88553", "88554", "88555", "88556",
            "88557", "88558", "88559", "88560", "88561", "88562", "88563", "88565", "88566", "88567",
            "88568", "88569", "88570", "88571", "88572", "88573", "88574", "88575", "88576", "88577",
            "88578", "88579", "88580", "88581", "88582", "88583", "88584", "88585", "88586", "88587",
            "88588", "88589", "88590", "88595"
        ];

        $fort_worth_tarrant_zip_code = [
            "76001", "76002", "76003", "76004", "76005", "76006", "76007", "76010", "76011", "76012",
            "76013", "76014", "76015", "76016", "76017", "76018", "76019", "76020", "76021", "76022",
            "76034", "76036", "76039", "76040", "76051", "76052", "76053", "76054", "76060", "76063",
            "76092", "76094", "76095", "76096", "76099", "76101", "76102", "76103", "76104", "76105",
            "76106", "76107", "76108", "76109", "76110", "76111", "76112", "76113", "76114", "76115",
            "76116", "76117", "76118", "76119", "76120", "76121", "76122", "76123", "76124", "76126",
            "76127", "76129", "76130", "76131", "76132", "76133", "76134", "76135", "76136", "76137",
            "76140", "76147", "76148", "76150", "76155", "76161", "76162", "76163", "76164", "76166",
            "76179", "76180", "76181", "76182", "76185", "76190", "76191", "76192", "76193", "76195",
            "76196", "76197", "76198", "76199", "76244", "76248"
        ];

        $houston_harris_zip_code = [
                "31804", "31807", "31811", "31822", "31823", "31826", "31831", "77001", "77002", "77003",
                "77004", "77005", "77006", "77007", "77008", "77009", "77010", "77011", "77012", "77013",
                "77014", "77015", "77016", "77017", "77018", "77019", "77020", "77021", "77022", "77023",
                "77024", "77025", "77026", "77027", "77028", "77029", "77030", "77031", "77032", "77033",
                "77034", "77035", "77036", "77037", "77038", "77039", "77040", "77041", "77042", "77043",
                "77044", "77045", "77046", "77047", "77048", "77049", "77050", "77051", "77052", "77053",
                "77054", "77055", "77056", "77057", "77058", "77059", "77060", "77061", "77062", "77063",
                "77064", "77065", "77066", "77067", "77068", "77069", "77070", "77071", "77072", "77073",
                "77074", "77075", "77076", "77077", "77078", "77079", "77080", "77081", "77082", "77083",
                "77084", "77085", "77086", "77087", "77088", "77089", "77090", "77091", "77093", "77094",
                "77095", "77096", "77098", "77201", "77202", "77203", "77204", "77205", "77206", "77207",
                "77208", "77209", "77210", "77212", "77213", "77215", "77216", "77217", "77218", "77219",
                "77220", "77221", "77222", "77223", "77224", "77225", "77226", "77227", "77228", "77229",
                "77230", "77231", "77233", "77234", "77235", "77236", "77237", "77238", "77240", "77241",
                "77242", "77243", "77244", "77245", "77248", "77249", "77251", "77252", "77253", "77254",
                "77256", "77257", "77258", "77259", "77261", "77262", "77263", "77265", "77266", "77267",
                "77268", "77269", "77270", "77271", "77272", "77273", "77274", "77275", "77277", "77279",
                "77336", "77338", "77339", "77346", "77373", "77375", "77373", "77388", "77389", "77396",
                "77401", "77429", "77433", "77447", "77449", "77450", "77491", "77493", "77494", "77502",
                "77503", "77504", "77505", "77506", "77507", "77520", "77521", "77530", "77532", "77536",
                "77547", "77562", "77571", "77587", "77598", "79901", "79925", "79902", "79903", "79904",
                "79905", "79907", "79912", "79915", "79922", "79924", "79930", "79932", "79935", "79936",
                "79906", "79908", "79910", "79911", "79913", "79914", "79917", "79920", "79923", "79926",
                "79927", "79929", "79931", "79934", "79937", "79938", "79940", "79941", "79942", "79943",
                "79944", "79945", "79946", "79947", "79948", "79949", "79950", "79951", "79952", "79953",
                "79954", "79955", "79958", "79960", "79961", "79968", "79976", "79978", "79980", "79990",
                "79995", "79996", "79997", "79998", "79999", "88510", "88511", "88512", "88513", "88514",
                "88515", "88517", "88518", "88519", "88520", "88521", "88523", "88524", "88525", "88526",
                "88527", "88528", "88529", "88530", "88531", "88532", "88533", "88534", "88535", "88536",
                "88538", "88539", "88540", "88541", "88542", "88543", "88544", "88545", "88546", "88547",
                "88548", "88549", "88550", "88553", "88554", "88555", "88556", "88557", "88558", "88559",
                "88560", "88561", "88562", "88563", "88565", "88566", "88567", "88568", "88569", "88570",
                "88571", "88572", "88573", "88574", "88575", "88576", "88577", "88578", "88579", "88580",
                "88581", "88582", "88583", "88584", "88585", "88586", "88587", "88588", "88589", "88590",
                "88595"
        ];

        $indianapolis_marion_zip_code = [
            "29519", "29546", "29571", "29574", "29589", "29592",
            "31803", "32111", "32113", "32133", "32134", "32179",
            "32182", "32183", "32192", "32195", "32617", "32634",
            "32663", "32664", "32681", "32686", "34420", "34421",
            "34430", "34431", "34432", "34470", "34471", "34472",
            "34473", "34474", "34475", "34476", "34477", "34478",
            "34479", "34480", "34481", "34482", "34483", "34488",
            "34489", "34491", "34492", "35543", "35548", "35563",
            "35564", "35570", "35594", "37340", "37347", "37374",
            "37380", "37396", "37397", "39429", "39478", "39483",
            "39643", "40009", "40033", "40037", "40049", "40060",
            "40062", "40063", "40328", "43301", "43302", "43314",
            "43322", "43332", "43335", "43337", "43341", "43342",
            "43356", "46107", "46113", "46183", "46201", "46202",
            "46203", "46204", "46205", "46206", "46207", "46208",
            "46209", "46211", "46213", "46214", "46216", "46217",
            "46218", "46219", "46220", "46221", "46222", "46224",
            "46225", "46226", "46227", "46228", "46229", "46230",
            "46231", "46234", "46235", "46236", "46237", "46239",
            "46240", "46241", "46242", "46244", "46247", "46249",
            "46250", "46251", "46253", "46254", "46255", "46256",
            "46259", "46260", "46266", "46268", "46274", "46275",
            "46277", "46278", "46282", "46283", "46285", "46291",
            "46295", "46296", "46298", "50044", "50057", "50062",
            "50116", "50119", "50138", "50163", "50214", "50219",
            "50225", "50252", "50256", "62801", "62807", "62849",
            "62853", "62854", "62870", "62875", "62881", "62882",
            "62892", "62893", "63401", "63454", "63461", "63463",
            "63471", "66840", "66851", "66858", "66859", "66861",
            "66866", "67053", "67063", "67073", "67438", "67475",
            "67483", "72619", "72634", "72661", "72668", "72672",
            "72677", "72687", "75564", "75657", "97002", "97020",
            "97026", "97032", "97071", "97137", "97301", "97302",
            "97303", "97305", "97306", "97307", "97308", "97309",
            "97310", "97311", "97312", "97313", "97314", "97317",
            "97325", "97342", "97346", "97350", "97352", "97362",
            "97373", "97375", "97381", "97383", "97384", "97385",
            "97392", "26554", "26555", "26559", "26560", "26563",
            "26566", "26570", "26571", "26572", "26574", "26576",
            "26578", "26582", "26585", "26586", "26587", "26588",
            "26591"
        ];

        $sanantonio_zip_code = ['78201', '78202', '78203', '78204', '78205', '78206', '78207', '78208', '78209', '78210',
            '78211', '78212', '78213', '78214', '78215', '78216', '78217', '78218', '78219', '78220',
            '78221', '78222', '78223', '78224', '78225', '78226', '78227', '78228', '78229', '78230',
            '78231', '78232', '78233', '78234', '78235', '78236', '78237', '78238', '78239', '78240',
            '78241', '78242', '78243', '78244', '78245', '78246', '78247', '78248', '78249', '78250',
            '78251', '78252', '78253', '78254', '78255', '78256', '78257', '78258', '78259', '78260',
            '78261', '78262', '78263', '78264', '78265', '78266', '78268', '78269', '78270', '78275',
            '78278', '78279', '78280', '78283', '78284', '78285', '78286', '78287', '78288', '78289',
            '78291', '78292', '78293', '78294', '78295', '78296', '78297', '78298', '78299'
        ];


        $philadephia_zipCodes = [
            "19019", "19092", "19093", "19099", "19101", "19102", "19103", "19104", "19105", "19106",
            "19107", "19108", "19109", "19110", "19111", "19112", "19114", "19115", "19116", "19118",
            "19119", "19120", "19121", "19122", "19123", "19124", "19125", "19127", "19128", "19129",
            "19130", "19131", "19132", "19133", "19134", "19135", "19136", "19137", "19138", "19139",
            "19140", "19141", "19142", "19143", "19144", "19145", "19146", "19147", "19148", "19149",
            "19150", "19151", "19152", "19153", "19154", "19155", "19160", "19161", "19162", "19170",
            "19171", "19172", "19173", "19175", "19176", "19177", "19178", "19179", "19181", "19182",
            "19183", "19184", "19185", "19187", "19188", "19190", "19191", "19192", "19193", "19194",
            "19195", "19196", "19197", "19244", "19255"
        ];

        $phoenix_maricopa_zipCodes = [
            "85001", "85002", "85003", "85004", "85005", "85006", "85007", "85008", "85009", "85010",
            "85011", "85012", "85013", "85014", "85015", "85016", "85017", "85018", "85019", "85020",
            "85021", "85022", "85023", "85024", "85025", "85026", "85027", "85028", "85029", "85030",
            "85031", "85032", "85033", "85034", "85035", "85036", "85037", "85038", "85039", "85040",
            "85041", "85042", "85043", "85044", "85045", "85046", "85048", "85050", "85051", "85053",
            "85054", "85060", "85061", "85062", "85063", "85064", "85065", "85066", "85067", "85068",
            "85069", "85070", "85071", "85072", "85073", "85074", "85075", "85076", "85078", "85079",
            "85080", "85082", "85083", "85085", "85086", "85087", "85097", "85098", "85127", "85142",
            "85190", "85201", "85202", "85203", "85204", "85205", "85206", "85207", "85208", "85209",
            "85210", "85211", "85212", "85213", "85214", "85215", "85216", "85224", "85225", "85226",
            "85233", "85234", "85236", "85244", "85246", "85248", "85249", "85250", "85251", "85252",
            "85253", "85254", "85255", "85256", "85257", "85258", "85259", "85260", "85261", "85262",
            "85263", "85264", "85266", "85267", "85268", "85269", "85271", "85274", "85275", "85277",
            "85280", "85281", "85282", "85283", "85284", "85285", "85286", "85287", "85288", "85295",
            "85296", "85297", "85298", "85299", "85301", "85302", "85303", "85304", "85305", "85306",
            "85307", "85308", "85309", "85310", "85311", "85312", "85318", "85320", "85322", "85323",
            "85326", "85327", "85329", "85331", "85335", "85337", "85338", "85339", "85340", "85342",
            "85343", "85345", "85351", "85353", "85354", "85355", "85358", "85361", "85363", "85372",
            "85373", "85374", "85375", "85376", "85377", "85378", "85379", "85380", "85381", "85382",
            "85383", "85385", "85387", "85388", "85390", "85392", "85395", "85396"
        ];

        $san_diago_zip_codes = [
            "91901", "91902", "91903", "91905", "91906", "91908", "91909", "91910", "91911", "91912",
            "91913", "91914", "91915", "91916", "91917", "91921", "91931", "91932", "91933", "91934",
            "91935", "91941", "91942", "91943", "91944", "91945", "91946", "91948", "91950", "91951",
            "91962", "91963", "91976", "91977", "91978", "91979", "91980", "91987", "92003", "92004",
            "92007", "92008", "92009", "92010", "92011", "92013", "92014", "92018", "92019", "92020",
            "92021", "92022", "92023", "92024", "92025", "92026", "92027", "92028", "92029", "92030",
            "92033", "92036", "92037", "92038", "92039", "92040", "92046", "92049", "92051", "92052",
            "92054", "92055", "92056", "92057", "92058", "92059", "92060", "92061", "92064", "92065",
            "92066", "92067", "92068", "92069", "92070", "92071", "92072", "92074", "92075", "92078",
            "92079", "92081", "92082", "92083", "92084", "92085", "92086", "92088", "92091", "92092",
            "92093", "92096", "92101", "92102", "92103", "92104", "92105", "92106", "92107", "92108",
            "92109", "92110", "92111", "92112", "92113", "92114", "92115", "92116", "92117", "92118",
            "92119", "92120", "92121", "92122", "92123", "92124", "92126", "92127", "92128", "92129",
            "92130", "92131", "92132", "92134", "92135", "92136", "92137", "92138", "92139", "92140",
            "92142", "92143", "92145", "92147", "92149", "92150", "92152", "92153", "92154", "92155",
            "92158", "92159", "92160", "92161", "92163", "92165", "92166", "92167", "92168", "92169",
            "92170", "92171", "92172", "92173", "92174", "92175", "92176", "92177", "92178", "92179",
            "92182", "92186", "92187", "92190", "92191", "92192", "92193", "92195", "92196", "92197",
            "92198", "92199"
        ];


        $jacksonville_duval_zip_codes = [
            "32207", "32216", "32218", "32256", "32099", "32201", "32202", "32203",
            "32204", "32205", "32206", "32208", "32209", "32210", "32211", "32212",
            "32214", "32217", "32219", "32220", "32221", "32222", "32223", "32224",
            "32225", "32226", "32227", "32228", "32229", "32231", "32232", "32233",
            "32234", "32235", "32236", "32237", "32238", "32239", "32240", "32241",
            "32244", "32245", "32246", "32247", "32250", "32254", "32255", "32257",
            "32258", "32266", "32277", "78341", "78349", "78357", "78376", "78384"
        ];


        $los_angeles_zip_codes = [
            "90210", "90002", "90003", "90004", "90006", "90012", "90017", "90018", "90019",
            "90020", "90024", "90025", "90026", "90027", "90029", "90031", "90034",
            "90035", "90036", "90038", "90039", "90041", "90045", "90046", "90048",
            "90049", "90057", "90064", "90067", "90071", "90212", "90230", "90277",
            "90292", "90401", "90403", "90404", "90405", "90503", "90802", "90804",
            "91006", "91105", "91107", "91505", "90001", "90005", "90007", "90008",
            "90009", "90010", "90011", "90013", "90014", "90015", "90016", "90019",
            "90021", "90022", "90023", "90028", "90030", "90032", "90033", "90037",
            "90040", "90042", "90043", "90044", "90047", "90050", "90051", "90052",
            "90053", "90054", "90055", "90056", "90058", "90059", "90060", "90061",
            "90062", "90063", "90065", "90066", "90068", "90069", "90070", "90072",
            "90073", "90074", "90075", "90076", "90077", "90079", "90080", "90081",
            "90082", "90083", "90084", "90086", "90087", "90088", "90089", "90091",
            "90093", "90095", "90096", "90134", "90209", "90211", "90213", "90220",
            "90221", "90223", "90224", "90231", "90232", "90233", "90239", "90240",
            "90241", "90242", "90245", "90247", "90248", "90249", "90250", "90251",
            "90254", "90255", "90260", "90261", "90262", "90263", "90264", "90265",
            "90266", "90267", "90270", "90272", "90274", "90275", "90278", "90280",
            "90290", "90291", "90293", "90294", "90295", "90296", "90301", "90302",
            "90303", "90304", "90305", "90306", "90307", "90308", "90309", "90310",
            "90311", "90312", "90402", "90406", "90407", "90408", "90409", "90410",
            "90411", "90501", "90502", "90504", "90505", "90506", "90507", "90508",
            "90509", "90510", "90601", "90602", "90603", "90604", "90605", "90606",
            "90607", "90608", "90609", "90610", "90637", "90638", "90639", "90640",
            "90650", "90651", "90652", "90660", "90661", "90662", "90670", "90702"
        ];

        $san_francisco_zip_codes = [
            "94102", "94103", "94105", "94107", "94108", "94109", "94111", "94104", "94110", "94115",
            "94117", "94118", "94123", "94133", "94112", "94114", "94116", "94120", "94121", "94122",
            "94124", "94127", "94129", "94131", "94132", "94134", "94143", "94177", "94119", "94125",
            "94126", "94130", "94137", "94139", "94140", "94141", "94142", "94144", "94145", "94146",
            "94147", "94151", "94159", "94160", "94161", "94163", "94164", "94172", "94188", "94158"
        ];

        $memphis_shelby_zip_codes = [
            "35007", "35040", "35043", "35051", "35078", "35080", "35114", "35115",
            "35124", "35137", "35143", "35144", "35147", "35176", "35178", "35185",
            "35186", "35187", "35242", "37501", "37544", "38002", "38014", "38016",
            "38017", "38018", "38027", "38028", "38029", "38053", "38054", "38055",
            "38083", "38088", "38101", "38103", "38104", "38105", "38106", "38107",
            "38108", "38109", "38111", "38112", "38113", "38114", "38115", "38116",
            "38117", "38118", "38119", "38120", "38122", "38124", "38125", "38126",
            "38127", "38128", "38130", "38131", "38132", "38133", "38134", "38135",
            "38136", "38137", "38138", "38139", "38141", "38145", "38148", "38150",
            "38151", "38152", "38157", "38159", "38161", "38163", "38166", "38167",
            "38168", "38173", "38174", "38175", "38177", "38181", "38182", "38183",
            "38184", "38186", "38187", "38188", "38190", "38193", "38194", "38197",
            "40003", "40022", "40065", "40066", "40067", "40076", "45302", "45306",
            "45333", "45334", "45336", "45340", "45353", "45360", "45363", "45365",
            "45367", "45845", "46110", "46126", "46130", "46144", "46161", "46176",
            "46182", "47234", "51446", "51447", "51527", "51530", "51531", "51537",
            "51562", "51565", "51578", "61957", "62422", "62431", "62438", "62444",
            "62462", "62463", "62465", "62534", "62550", "62553", "62565", "62571",
            "63434", "63437", "63439", "63443", "63450", "63451", "63468", "63469",
            "75935", "75954", "75973", "75974", "75975"
        ];


        $nashville_davidson_zipCodes = [
            "27239", "27292", "27293", "27294", "27295", "27299", "27351", "27360", "27361", "27373",
            "27374", "37011", "37013", "37070", "37072", "37076", "37080", "37115", "37116", "37138",
            "37189", "37201", "37202", "37203", "37204", "37205", "37206", "37207", "37208", "37209",
            "37210", "37211", "37212", "37213", "37214", "37215", "37216", "37217", "37218", "37219",
            "37220", "37221", "37222", "37224", "37227", "37228", "37229", "37230", "37232", "37234",
            "37235", "37236", "37238", "37240", "37241", "37242", "37243", "37244", "37246", "37250"
        ];

        $newyork_zip_codes = [
            "10001","10002","10003","10004","10005","10006","10007","10008","10009","10010",
            "10011","10012","10013","10014","10016","10017","10018","10019","10020","10021",
            "10022","10023","10024","10025","10026","10027","10028","10029","10030","10031",
            "10032","10033","10034","10035","10036","10037","10038","10039","10040","10041",
            "10043","10044","10045","10055","10060","10065","10069","10075","10080","10081",
            "10087","10090","10101","10102","10103","10104","10105","10106","10107","10108",
            "10109","10110","10111","10112","10113","10114","10115","10116","10117","10118",
            "10119","10120","10121","10122","10123","10124","10125","10126","10128","10129",
            "10130","10131","10132","10133","10138","10150","10151","10152","10153","10154",
            "10155","10156","10157","10158","10159","10160","10161","10162","10163","10164",
            "10165","10166","10167","10168","10169","10170","10171","10172","10173","10174",
            "10175","10176","10177","10178","10179","10185","10199","10203","10211","10212",
            "10213","10242","10249","10256","10258","10259","10260","10261","10265","10268",
            "10269","10270","10271","10272","10273","10274","10275","10276","10277","10278",
            "10279","10280","10281","10282","10285", "10286"
        ];


        $sanjose_santaclara_zip_codes = [
            "94022", "94040", "94043", "94086", "94089", "94301", "94305", "94306", "95008", "95014",
            "95020", "95035", "95037", "95050", "95051", "95054", "95070", "95110", "95111", "95112",
            "95113", "95126", "95131", "95134", "94024", "94041", "94087", "94088", "94304", "95002",
            "95030", "95032", "95033", "95052", "95053", "95101", "95116", "95117", "95118", "95119",
            "95120", "95121", "95122", "95123", "95124", "95125", "95127", "95128", "95129", "95132",
            "95133", "95135", "95136", "95138", "95139", "95141", "95148", "94023", "94035", "94039",
            "94042", "94302", "94309", "95009", "95011", "95013", "95015", "95021", "95031", "95036",
            "95038", "95042", "95044", "95046", "95055", "95056", "95071", "95103", "95106", "95108",
            "95109", "95115", "95130", "95140", "95150", "95151", "95152", "95153", "95154", "95155",
            "95156", "95157", "95158", "95159", "95160", "95161", "95164", "95170", "95172", "95173",
            "95190", "95191", "95192", "95193", "95194", "95196", "95026", "94085"
        ];

        $seatle_king_zip_codes = [
            "79236", "98104", "98101", "98001", "98002", "98003", "98004", "98005", "98006", "98007",
            "98008", "98011", "98027", "98031", "98032", "98033", "98039", "98040", "98047", "98052",
            "98055", "98056", "98072", "98102", "98103", "98105", "98106", "98107", "98108", "98109",
            "98112", "98115", "98116", "98117", "98119", "98121", "98122", "98125", "98126", "98133",
            "98134", "98144", "98177", "98188", "98195", "98199", "98009", "98010", "98013", "98014",
            "98015", "98019", "98022", "98023", "98024", "98025", "98028", "98029", "98034", "98035",
            "98038", "98041", "98042", "98045", "98050", "98051", "98053", "98057", "98058", "98059",
            "98062", "98063", "98064", "98065", "98068", "98070", "98071", "98073", "98083", "98092",
            "98093", "98111", "98114", "98118", "98124", "98129", "98131", "98136", "98138", "98145",
            "98146", "98148", "98154", "98155", "98158", "98161", "98164", "98166", "98168", "98174",
            "98178", "98181", "98185", "98191", "98198", "98224", "98288", "98160", "98190", "98030",
            "98074", "98075", "98089", "98113", "98127", "98139", "98141", "98165", "98170", "98175",
            "98194"
        ];

        $washington_dc_zip_codes = [
            "20001", "20002", "20003", "20004", "20005", "20006", "20007", "20008", "20009", "20010",
            "20011", "20012", "20013", "20015", "20016", "20017", "20018", "20019", "20020", "20024",
            "20026", "20029", "20030", "20032", "20033", "20035", "20036", "20037", "20038", "20039",
            "20040", "20041", "20042", "20043", "20044", "20045", "20047", "20049", "20050", "20052",
            "20053", "20055", "20056", "20057", "20058", "20059", "20060", "20061", "20062", "20064",
            "20065", "20066", "20067", "20068", "20069", "20070", "20071", "20073", "20074", "20075",
            "20076", "20080", "20081", "20090", "20091", "20201", "20202", "20203", "20204", "20206",
            "20207", "20208", "20210", "20211", "20212", "20213", "20215", "20216", "20217", "20218",
            "20219", "20220", "20221", "20222", "20223", "20224", "20226", "20227", "20228", "20229",
            "20230", "20233", "20235", "20239", "20240", "20241", "20242", "20244", "20245", "20250",
            "20251", "20254", "20260", "20261", "20265", "20266", "20268", "20289", "20301", "20306",
            "20310", "20314", "20319", "20330", "20340", "20350", "20372", "20375", "20380", "20389",
            "20392", "20393", "20394", "20395", "20401", "20402", "20403", "20404", "20405", "20406",
            "20407", "20408", "20409", "20410", "20411", "20412", "20414", "20415", "20416", "20418",
            "20419", "20420", "20421", "20422", "20423", "20424", "20425", "20426", "20427", "20428",
            "20429", "20431", "20433", "20435", "20436", "20439", "20440", "20441", "20442", "20444",
            "20447", "20451", "20453", "20456", "20460", "20463", "20472", "20502", "20503", "20505",
            "20506", "20507", "20510", "20515", "20520", "20521", "20522", "20523", "20524", "20525",
            "20526", "20527", "20530", "20531", "20533", "20534", "20535", "20536", "20538", "20539",
            "20540", "20541", "20542", "20543", "20544", "20546", "20547", "20548", "20549", "20551"
        ];
        if (in_array($zip_code_inform, $austin_travis_zip_code)) {
            return "austin_travis";
        } elseif (in_array($zip_code_inform, $detroit_wayne_zipCodes)) {
            return "wayne";
        } elseif (in_array($zip_code_inform, $chicago_cook_zipCodes)) {
            return "chicago_cook";
        } elseif (in_array($zip_code_inform, $charlotte_mecklenburg_zip_codes)) {
            return "charlotte_mecklenburg";
        } elseif (in_array($zip_code_inform, $sanantonio_zip_code)) {
            return "san_antonio";
        }
        // work it
        elseif (in_array($zip_code_inform, $washington_dc_zip_codes)) {
            return "washington_dc";
        } elseif (in_array($zip_code_inform, $seatle_king_zip_codes)) {
            return "seatle_king";
        } elseif (in_array($zip_code_inform, $sanjose_santaclara_zip_codes)) {
            return "sanjose_santaclara";
        } elseif (in_array($zip_code_inform, $san_francisco_zip_codes)) {
            return "san_francisco";
        } elseif (in_array($zip_code_inform, $san_diago_zip_codes)) {
            return "san_diago";
        } elseif (in_array($zip_code_inform, $phoenix_maricopa_zipCodes)) {
            return "phoenix_maricopa";
        } elseif (in_array($zip_code_inform, $philadephia_zipCodes)) {
            return "philadephia";
        } elseif (in_array($zip_code_inform, $newyork_zip_codes)) {
            return "newyork";
        } elseif (in_array($zip_code_inform, $nashville_davidson_zipCodes)) {
            return "nashville_davidson";
        } elseif (in_array($zip_code_inform, $columbus_franklin_zipCodes)) {
            return "franklin";
        } elseif (in_array($zip_code_inform, $memphis_shelby_zip_codes)) {
            return "memphis_shelby";
        } elseif (in_array($zip_code_inform, $los_angeles_zip_codes)) {
            return "los_angeles";
        } elseif (in_array($zip_code_inform, $jacksonville_duval_zip_codes)) {
            return "jacksonville_duval";
        } elseif (in_array($zip_code_inform, $dallas_zipCodes)) {
            return "dallas";
        } elseif (in_array($zip_code_inform, $fort_worth_tarrant_zip_code)) {
            return "tarrant";
        } elseif (in_array($zip_code_inform, $ell_passo_zip_code)) {
            return "el_passo";
        } elseif (in_array($zip_code_inform, $denver_zipCodes)) {
            return "denver";
        } elseif (in_array($zip_code_inform, $houston_harris_zip_code)) {
            return "harris";
        } elseif (in_array($zip_code_inform, $indianapolis_marion_zip_code)) {
            return "marion";
        } else {
            return "Unknown";
        }
    }


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

    // fgj sdf jksdhfjk jkghsja asjkdhfjk asfjkashfj asfasjkhfjk asfjkasdhf jkasdhfjkash fjkashdfjk asjkhfjkas hfjkashfj asjkfhasjkdfh jkashfj sdfjha j jkhjh

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

    //     $vehicles = VehicleMake::orderBy('make_name')->where('status', 1)->pluck('id', 'make_name');

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
    //         $query = $this->inventoryService->getItemByFilterOnly($request, $dealer_id);

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


    //         $query->select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'payment_price')
    //             ->with([
    //                 'dealer' => function ($query) {
    //                     $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id')
    //                         ->addSelect('id'); // Add id explicitly to avoid conflict
    //                 },
    //                 'additionalInventory' => function ($query) {
    //                     $query->select('main_inventory_id', 'local_img_url')  // Only necessary columns
    //                         ->addSelect('id'); // Add id explicitly to avoid conflict
    //                 },
    //                 'mainPriceHistory' => function ($query) {
    //                     $query->select('main_inventory_id', 'change_amount') // Only necessary columns
    //                         ->addSelect('id'); // Add id explicitly to avoid conflict
    //                 }
    //             ]);


    //         // if (!empty($zipCode)) {
    //         //     try {
    //         //         $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
    //         //         $response = @file_get_contents($url);

    //         //         if ($response === FALSE) {
    //         //             throw new Exception("Failed to fetch location data.");
    //         //         }

    //         //         $zip_location_data = json_decode($response, true);

    //         //         if (!isset($zip_location_data['results'][0]['geometry'])) {
    //         //             throw new Exception("Invalid response format.");
    //         //         }
    //         //     } catch (Exception $e) {
    //         //         // Log the error and fallback to fetching all results
    //         //         error_log("Geolocation API Error: " . $e->getMessage());

    //         //     }
    //         // }

    //         // // **Fallback: If API fails, fetch all cars**


    //         // marif code backup for exceedd limit start


    //         // marif code backup for exceedd limit end

    //         // *********************
    //         // $query->where('price', '>', 1);

    //         $query->where('price', '>', '1');
    //         $zipCodeData = [
    //             'zip_code_data' => $zipCode,
    //             'zip_radios_data' => $zip_radios,
    //             'query_data' => $query,
    //         ];

    //         if ($request->webRadios == 'Nationwide' || $request->mobileRadios == 'Nationwide') {
    //             $message = null;
    //             $inventories = $query->paginate(20);
    //         } else {
    //             $result = $this->getItemByDistance($zipCodeData['zip_code_data'], $zipCodeData['zip_radios_data'], $zipCodeData['query_data']);
    //             $inventories = $result['inventories'];
    //             $message = $result['message'];
    //         }

    //         // // *********************
    //         // // $query->where('type', '!=', 'New')->with('dealer');
    //         // $message = ''; // Initialize the $message variable

    //         // $countryCode = 'us';
    //         // $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
    //         // $response = file_get_contents($url);
    //         // $zip_location_data = json_decode($response, true);

    //         // if ($zipCode != null) {
    //         //     try {

    //         //         if (is_string($zip_radios) && !empty($zip_radios)) {
    //         //             if (isset($zip_location_data['results'][0]['geometry'])) {
    //         //                 $latitude = $zip_location_data['results'][0]['geometry']['lat'];
    //         //                 $longitude = $zip_location_data['results'][0]['geometry']['lng'];
    //         //                 $cityName = $zip_location_data['results'][0]['components']['city'] ?? '';

    //         //                 // Only check within the specified radius
    //         //                 $zipCodeQuery = clone $query;


    //         //                 // $zipCodeQuery->selectRaw(
    //         //                 //     "*, (3959 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"
    //         //                 // );

    //         //                 // $zipCodeQuery->selectRaw(
    //         //                 //     "*, (3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
    //         //                 //     [$latitude, $longitude, $latitude]
    //         //                 // );

    //         //                 $zipCodeQuery->select([
    //         //                     'main_inventories.id',
    //         //                     'main_inventories.deal_id',
    //         //                     'main_inventories.vin',
    //         //                     'main_inventories.year',
    //         //                     'main_inventories.make',
    //         //                     'main_inventories.model',
    //         //                     'main_inventories.price',
    //         //                     'main_inventories.title',
    //         //                     'main_inventories.miles',
    //         //                     'main_inventories.price_rating',
    //         //                     'main_inventories.zip_code'
    //         //                 ])->selectRaw(
    //         //                     "(3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
    //         //                     sin(radians(?)) * sin(radians(latitude)))) AS distance",
    //         //                     [$latitude, $longitude, $latitude]
    //         //                 );

    //         //                 $zipCodeQuery->having('distance', '<=', $zip_radios);
    //         //                 $zipCodeQuery->orderBy('distance', 'asc');

    //         //                 $zipCodeInventories = $zipCodeQuery->get();

    //         //                 if (!$zipCodeInventories->isEmpty()) {
    //         //                     // Results found within the input radius
    //         //                     $inventories = $zipCodeQuery->paginate(20);

    //         //                     // Check if any result is exactly within the radius specified by user
    //         //                     $maxDistance = $zipCodeInventories->max('distance');
    //         //                     if ($maxDistance > $zip_radios) {
    //         //                         $message = "
    //         //                             <section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:3px\" class=\"sptb2\">
    //         //                                 <div style=\"border-radius:5px\" class=\"container bg-white p-5\">
    //         //                                     <div class=\"text-center\">
    //         //                                         <h3 class=\"mb-2\">You searched {$zip_radios} miles. .....</h3>
    //         //                                         <p class=\"mb-2\">Showing results within {$maxDistance} miles.</p>
    //         //                                     </div>
    //         //                                 </div>
    //         //                             </section>";
    //         //                     }
    //         //                 } else {
    //         //                     // No results found within the specified radius
    //         //                     $inventories = $query->paginate(20);
    //         //                     $message = "
    //         //                         <section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px\" class=\"sptb2\">
    //         //                             <div style=\"border-radius:5px\" class=\"container bg-white p-5 match\">
    //         //                                 <div class=\"text-center\">
    //         //                                     <h3 class=\"mb-2\">No exact matches within {$zip_radios} miles. ....</h3>
    //         //                                     <p class=\"mb-2\">Modify your filters or click \"Save Search\" to be notified when more matches are available.</p>
    //         //                                     <a href=\"#\" class=\"mb-2 clearfilterAjax\" style=\"text-decoration:underline;font-weight:bold;font-size:15px\" id=\"clearfilterAjax\">Clear all filters.</a>
    //         //                                 </div>
    //         //                             </div>
    //         //                         </section>";
    //         //                 }
    //         //             } else {
    //         //                 // Fallback if no location data is found
    //         //                 $inventories = $query->orderByDesc('id')->paginate(20);
    //         //             }
    //         //         } else {



    //         //             $zipCodeQuery = clone $query;
    //         //             $zipCodeQuery->where('zip_code', $zipCode);
    //         //             // dd($zipCode, $zipCodeQuery);
    //         //             $zipCodeInventories = $zipCodeQuery->get();

    //         //             if ($zipCodeInventories->isEmpty()) {
    //         //                 // No exact matches found for ZIP code alone, so try increasing radii
    //         //                 $radiusOptions = [10, 25, 50, 100];
    //         //                 $foundInventories = false;
    //         //                 $lastRadiusChecked = '';

    //         //                 // Call external geolocation API to get latitude and longitude of the ZIP code
    //         //                 $countryCode = 'us';
    //         //                 $url = "https://api.opencagedata.com/geocode/v1/json?q={$zipCode},{$countryCode}&key=4b84ff4ad9a74c79ad4a1a945a4e5be1";
    //         //                 $response = file_get_contents($url);
    //         //                 $zip_location_data = json_decode($response, true);

    //         //                 if (isset($zip_location_data['results'][0]['geometry'])) {
    //         //                     $latitude = $zip_location_data['results'][0]['geometry']['lat'];
    //         //                     $longitude = $zip_location_data['results'][0]['geometry']['lng'];

    //         //                     // Iterate through each radius option
    //         //                     foreach ($radiusOptions as $radius) {
    //         //                         $zipCodeQuery = clone $query;
    //         //                         // $zipCodeQuery->selectRaw(
    //         //                         //     "*, (3959 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance"
    //         //                         // );

    //         //                         $zipCodeQuery->select([
    //         //                             'main_inventories.id',
    //         //                             'main_inventories.deal_id',
    //         //                             'main_inventories.vin',
    //         //                             'main_inventories.year',
    //         //                             'main_inventories.make',
    //         //                             'main_inventories.model',
    //         //                             'main_inventories.price',
    //         //                             'main_inventories.title',
    //         //                             'main_inventories.miles',
    //         //                             'main_inventories.price_rating',
    //         //                             'main_inventories.zip_code'
    //         //                         ])->selectRaw(
    //         //                             "(3959 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) +
    //         //                             sin(radians(?)) * sin(radians(latitude)))) AS distance",
    //         //                             [$latitude, $longitude, $latitude]
    //         //                         );

    //         //                         $zipCodeQuery->having('distance', '<=', $radius);
    //         //                         $zipCodeQuery->orderBy('distance', 'asc');

    //         //                         $zipCodeInventories = $zipCodeQuery->count();
    //         //                         if (is_string($zipCodeInventories) && !empty($zipCodeInventories)) {

    //         //                             $inventories = $zipCodeQuery->orderByDesc('id')->paginate(20);
    //         //                             $foundInventories = true;
    //         //                             break; // Exit loop if inventories are found within the current radius
    //         //                         } else {
    //         //                             // No results for this radius, so update lastRadiusChecked
    //         //                             $lastRadiusChecked = $radius;
    //         //                         }
    //         //                     }
    //         //                 }

    //         //                 // If no inventories found, show a message with the last radius checked
    //         //                 if (!$foundInventories) {
    //         //                     $inventories = $query->orderByDesc('id')->paginate(20);
    //         //                     $message = "
    //         //                         <section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px\" class=\"sptb2\">
    //         //                             <div style=\"border-radius:5px\" class=\"container bg-white p-5 match\">
    //         //                                 <div class=\"text-center\">
    //         //                                     <h3 class=\"mb-2\">No exact matches within {$lastRadiusChecked} miles. ......</h3>
    //         //                                     <p class=\"mb-2\">Modify your filters or click \"Save Search\" to be notified when more matches are available.</p>
    //         //                                     <a href=\"#\" class=\"mb-2 clearfilterAjax\" style=\"text-decoration:underline;font-weight:bold;font-size:15px\" id=\"clearfilterAjax\">Clear all filters.</a>
    //         //                                 </div>
    //         //                             </div>
    //         //                         </section>";
    //         //                 }
    //         //             } else {
    //         //                 // Exact matches found for the ZIP code alone
    //         //                 $inventories = $zipCodeQuery->orderByDesc('id')->paginate(20);
    //         //             }
    //         //         }
    //         //     } catch (\Exception $e) {
    //         //         // Handle any exceptions that occur during the API call or query building
    //         //         $message = 'An error occurred while processing your request. Please try again.';
    //         //         Log::error("Error processing zip code {$zipCode}: " . $e->getMessage());
    //         //         $inventories = $query->orderByDesc('id')->paginate(20); // Fallback to default pagination
    //         //     }
    //         // } else {
    //         //     // No zip code provided, paginate default query
    //         //     $inventories = $query->orderByDesc('id')->paginate(20);
    //         // }
    //         // // *********************

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

}
