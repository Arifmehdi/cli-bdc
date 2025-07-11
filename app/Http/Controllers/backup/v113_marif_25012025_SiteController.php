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
use Jorenvh\Share\Share;
use PDO;
use Stevebauman\Location\Facades\Location;
use function PHPUnit\Framework\isEmpty;

class SiteController extends Controller
{

    use Notify;

    private $inventoryService;
    public function __construct(InventoryServiceInterface $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index()
    {
        $makes_obj = VehicleMake::orderBy('make_name');
        $makes = $makes_obj->where('status', 1)->get();
        $years = VehicleYear::orderByDesc('year')->where('status', 1)->get();
        $news = News::with('user')->where('status', '1')->get();

        $slider = GeneralSetting::first();

        $bodies = VehicleBody::orderBy('name')->get();

        //     $userHistories = UserTrack::where('type', 'Viewed')
        //     ->orWhere(function ($query) {
        //     $query->where('ip_address', request()->ip())
        //           ->where('type', 'Viewed');
        // })
        // ->orderBy('id', 'desc')
        // ->get();

        $totalCount = UserTrack::where('type', 'Viewed')
            ->orWhere(function ($query) {
                $query->where('ip_address', request()->ip())
                    ->where('type', 'Viewed');
            })
            ->count();

        // Fetch the top 12 records
        $userHistories = UserTrack::where('type', 'Viewed')
            ->orWhere(function ($query) {
                $query->where('ip_address', request()->ip())
                    ->where('type', 'Viewed');
            })->with('inventory')
            ->orderBy('count', 'desc')
            ->limit(12)
            ->get();
        // return $userHistories;
        $tendings = Trending::where('status', 1)->orderBy('id', 'desc')->get();

        $ad1 = Advertisement::where('status', '1')->orderBy('id', 'desc')->first();
        $ad2 = Advertisement::where('status', '1')->orderBy('id', 'desc')->skip(1)->first();
        // $header_menus = Menu::with('submenus')->where('parent', 0)->where('status', 1)->get();
        return view('frontend.home', compact('makes', 'years', 'news', 'slider', 'bodies', 'ad1', 'ad2', 'userHistories', 'tendings'));
    }

    public function auto(Request $request, $param = null)
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

        if (empty($request->zip)) {
            Cookie::queue(Cookie::forget('zipcode'));
        }


        $searchBody = $request->query('homeBodySearch');
        // $inventores = Inventory::all();

        $vehicles_obj = VehicleMake::query();


        $vehicles = $vehicles_obj->where('status', 1)->get();
        // $inventory_obj = Inventory::query();
        $inventory_obj = MainInventory::query();
        $vehicles_body = $inventory_obj->distinct()->pluck('body_formated')->toArray();
        $vehicles_fuel_other = $inventory_obj->distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();



        sort($vehicles_fuel_other);
        sort($vehicles_body);

        $price_max = (int)$inventory_obj->where('price', '!=', 'N/A')->max('price');
        $price_min = (int)$inventory_obj->where('price', '!=', 'N/A')->min('price');
        $miles_max = $inventory_obj->where('miles', '!=', 'N/A')->max('miles');
        $miles_min = $inventory_obj->where('miles', '!=', 'N/A')->min('miles');

        // dd($price_max,$price_min,$miles_max,$miles_min);
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
                    // $query = Inventory::with('dealer');
                    $query = MainInventory::with('dealer','additionalInventory');

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

            $query->where('price', '>', '1');
            // $query->where('type', '!=', 'New')->with('dealer');
            $message = ''; // Initialize the $message variable

            if ($zipCode != null) {

                try {

                    if (is_string($zip_radios) && !empty($zip_radios)) {

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
                                                    <h3 class=\"mb-2\">You searched {$zip_radios} miles. .....</h3>
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
                                                <h3 class=\"mb-2\">No exact matches within {$zip_radios} miles. ....</h3>
                                                <p class=\"mb-2\">Modify your filters or click \"Save Search\" to be notified when more matches are available.</p>
                                                <a href=\"#\" class=\"mb-2 clearfilterAjax\" style=\"text-decoration:underline;font-weight:bold;font-size:15px\" id=\"clearfilterAjax\">Clear all filters.</a>
                                            </div>
                                        </div>
                                    </section>";
                            }
                        } else {
                            // Fallback if no location data is found
                            $inventories = $query->orderByDesc('id')->paginate(20);
                        }
                    } else {

                        $zipCodeQuery = clone $query;
                        $zipCodeQuery->where('zip_code', $zipCode);
                        // dd($zipCode, $zipCodeQuery);
                        $zipCodeInventories = $zipCodeQuery->get();

                        if ($zipCodeInventories->isEmpty()) {
                            // No exact matches found for ZIP code alone, so try increasing radii
                            $radiusOptions = [10, 25, 50, 100];
                            $foundInventories = false;
                            $lastRadiusChecked = '';

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

                                    $zipCodeInventories = $zipCodeQuery->count();
                                    if (is_string($zipCodeInventories) && !empty($zipCodeInventories)) {

                                        $inventories = $zipCodeQuery->orderByDesc('id')->paginate(20);
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
                                $inventories = $query->orderByDesc('id')->paginate(20);
                                $message = "
                                    <section style=\"padding-top: 5px !important; padding-bottom:3px !important; margin-bottom:5px\" class=\"sptb2\">
                                        <div style=\"border-radius:5px\" class=\"container bg-white p-5 match\">
                                            <div class=\"text-center\">
                                                <h3 class=\"mb-2\">No exact matches within {$lastRadiusChecked} miles. ......</h3>
                                                <p class=\"mb-2\">Modify your filters or click \"Save Search\" to be notified when more matches are available.</p>
                                                <a href=\"#\" class=\"mb-2 clearfilterAjax\" style=\"text-decoration:underline;font-weight:bold;font-size:15px\" id=\"clearfilterAjax\">Clear all filters.</a>
                                            </div>
                                        </div>
                                    </section>";
                            }
                        } else {
                            // Exact matches found for the ZIP code alone
                            $inventories = $zipCodeQuery->orderByDesc('id')->paginate(20);
                        }
                    }
                } catch (\Exception $e) {
                    // Handle any exceptions that occur during the API call or query building
                    $message = 'An error occurred while processing your request. Please try again.';
                    $inventories = $query->orderByDesc('id')->paginate(20); // Fallback to default pagination
                }
            } else {
                // No zip code provided, paginate default query
                $inventories = $query->orderByDesc('id')->paginate(20);
            }

            $current_page_count = $inventories->count();

            $total_count = number_format($inventories->total());
            $single_inventories_count = ($inventories->perPage() * ($inventories->currentPage() - 1)) + $current_page_count;
            // inventory numberf calculation end  here
            $view = view('frontend.auto_ajax', compact('inventories', 'total_count', 'single_inventories_count', 'message'))->render();
            return response()->json(['view' => $view, 'pagination' => $inventories->links()->toHtml(), 'total_count' => $total_count, 'message' => $message]);
        }



        $make_data = $request->input('make');
        $states = LocationState::orderBy('state_name')->pluck('state_name', 'id');
        // $cachefuel = MainInventory::distinct()->pluck('fuel')->toArray();

        $cachefuel = MainInventory::distinct()->pluck('fuel')->toArray();


        return view('frontend.auto', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'states','cachefuel'));
        // return view('frontend.auto', compact('vehicles', 'vehicles_body', 'inventores', 'searchBody', 'vehicles_fuel_other', 'make_data', 'states','cachefuel'));
    }

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
                    $query = MainInventory::with('dealer','additionalInventory');

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
        return view('frontend.dealer', compact('vehicles', 'vehicles_body', 'searchBody', 'vehicles_fuel_other', 'make_data', 'stockId', 'dealer_name', 'id','cachefuel'));
    }



    public function autoDetails(Request $request, $vin, $slug)
    {
        // $inventory = Inventory::where('vin', $vin)->first();
        $inventory = MainInventory::with('additionalInventory','mainPriceHistory')->where('vin', $vin)->first();

        // if ($inventory->priceHistory->isNotEmpty()) {
        //     foreach ($inventory->priceHistory as $priceHistory) {
        //         echo $priceHistory->amount;
        //     }
        // } else {
        //     echo 'No price history available.';
        // }



        if (!$inventory) {
            return redirect()->route('not-found');
        }
        $lowerPrice = (float)$inventory->price - 5000;
        $higherPrice = (float)$inventory->price + 5000;
        $relateds = MainInventory::where('body_formated', $inventory->body_formated)
            ->where('id', '!=', $inventory->id)
            ->whereBetween('price',[$lowerPrice, $higherPrice])
            ->take(4)
            ->get();


        $url_id = $inventory->year . '-' . $inventory->make . '-' . $inventory->model . '-in-' . $inventory->dealer->city . '-' . strtoupper($inventory->dealer->state);
        $shareButtons = \Share::page(url('/best-used-cars-for-sale' . '/' . 'listing' . '/' . $vin . '/' . $url_id), $inventory->title)
            ->facebook()
            ->twitter()
            ->linkedin()
            ->whatsapp()
            ->pinterest()
            ->telegram();


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
        $locationStateData = LocationState::where('short_name',$stateData)->first();
        $locationCityData = LocationCity::where('city_name',$stateCity)->first();

        ($locationStateData) ? $stateRate = (float) $locationStateData->sales_tax : $stateRate = 0;
        ($locationCityData) ? $cityrate = (float) $locationCityData->sales_tax : $cityrate = 0;

        return response()->view('frontend.auto_details', compact('inventory', 'relateds', 'shareButtons','stateRate','cityrate'));
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
        $termsconditions = TermsCondition::orderBy('id', 'desc')->get();
        return view('frontend.privacy.index', compact('termsconditions'));
    }

    public function details($slug)
    {
        try {
            $data = News::where('slug', $slug)->firstOrFail();

            $news = News::orderBy('created_at', 'desc')->get();
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
            ->select('id', 'dealer_id', 'name', 'phone', 'address','city','state','zip')
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

            $dealers = $query->paginate(20);
            $cities = LocationCity::where('status', 1)
                ->orderBy('city_name', 'asc')
                ->select('id', 'city_name')
                ->distinct()
                ->get();

            // Pagination values
            $current_page_count = $dealers->count();
            $total_count = number_format($dealers->total());
            $single_dealer_count = ($dealers->perPage() * ($dealers->currentPage() - 1)) + $current_page_count;

            $view = view('frontend.dealer.dealer_ajax', compact('dealers', 'total_count', 'single_dealer_count', 'cities', 'target_city', 'target_name'))->render();

            return response()->json([
                'view' => $view,
                'pagination' => $dealers->links()->toHtml(),
                'total_count' => $total_count,
            ]);
        }

        // Non-AJAX requests
        $dealers = $query->select('id','dealer_id','address','name','city','state','zip','phone','website', 'price');
        // $dealers = $query->get();

        $cities = LocationCity::where('status', 1)
            ->orderBy('city_name', 'asc')
            ->select('id', 'city_name')
            ->distinct()
            ->get();

        return view('frontend.dealer.index', compact('dealers', 'cities'));
    }


    public function news()
    {
        $all = News::where('status', '1')->get();
        $firstNews = News::where('status', '1')->orderBy('created_at', 'desc')->first();
        $lastNews = News::where('status', '1')->orderBy('created_at', 'asc')->first();
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
            $reviews = Blog::where('status', '1')
                ->whereNotIn('id', [$firstreviews->id]) // Use whereNotIn as a fallback
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
        // dd($videos);
        return view('frontend.news.news_page', compact('news', 'firstNews', 'lastNews', 'reviews', 'firstreviews', 'lastreviews', 'advices', 'videos', 'all'));
    }

    public function articles($slug)
    {
        $videos = LatestVideo::where('status', '1')->orderBy('created_at', 'desc')->get();
        $review = Review::where('status', '1')->orderBy('created_at', 'desc')->get();
        $news = News::where('status', '1')->orderBy('created_at', 'desc')->get();

        $slug_info = $slug;
        if($slug = 'vedio'){
            $datas = LatestVideo::where('status', '1')->orderBy('created_at', 'desc')->get();
        }

        if($slug = 'review')
        {
            $datas = Review::where('status', '1')->orderBy('created_at', 'desc')->get();
        }
        
        if($slug = 'article')
        {
            $datas = News::where('status', '1')->orderBy('created_at', 'desc')->get();
        }
        return view('frontend.article', compact('videos', 'review', 'news', 'datas','slug_info'));
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

        return view('frontend.news.vedio_details', compact('news', 'firstNews', 'lastNews', 'reviews', 'firstreviews', 'lastreviews', 'advices', 'videos', 'all','vedio'));
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
            $query = MainInventory::with('additionalInventory');
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
        $inventory = MainInventory::with('additionalInventory','mainPriceHistory')->findOrFail($id);

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
        $news = News::with('user')->where('status', '1')->get();
        // $sels = Blog::where('type', 'Selling your car')->where('status', '1')->get();
        // $shops = Blog::where('type', 'Shopping & negotiating')->where('status', '1')->get();
        // $owners = Blog::where('type', 'Ownership & maintenance')->where('status', '1')->get();
        $sels = Blog::where('type', 1)->where('status', '1')->get();
        $shops = Blog::where('type', 2)->where('status', '1')->get();
        $owners = Blog::where('type', 3)->where('status', '1')->get();
        $faqs = Faq::where('status', '1')->where('type', 'research')->get();

        return view('frontend.research', compact('news', 'sels', 'shops', 'owners', 'faqs'));
    }

    public function article_details($slug = null)
    {
        $encodedSlug = urlencode($slug);
        $art = Blog::where('slug', 'LIKE', "%{$encodedSlug}%")->first();

        // $art = Blog::where('slug',$slug)->first();
        // dd($art, $slug, $encodedSlug);
        $artId = $art->id;

        $rels = Blog::whereNot('id',$artId)->limit(6)->get();
        return view('frontend.article-details', compact('art', 'rels'));
    }

    public function tipsdetails($id = null)
    {
        $art = Tips::find($id);
        $rels = Tips::limit(6)->get();
        return view('frontend.tips-details', compact('art', 'rels'));
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
        if($request->action == 'search-rate'){
            $arpRateValue = Setting::where('id',$request->arpRate)->where('data_type',1)->select('id','key','value')->first();
            return $arpRateValue->value;
        }
    }
}
