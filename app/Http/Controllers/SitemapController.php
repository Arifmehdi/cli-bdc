<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Inventory;
use App\Models\MainInventory;
use App\Models\News;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Sitemap;

class SitemapController extends Controller
{

    const SITEMAP_DIR = 'sitemaps/used';
    const SITEMAP_PREFIX = 'sitemap-listing_';

    public function generateAllSitemaps()
    {
        // Generate city-based sitemaps
        $this->generateCitySitemaps();

        return response()->json(['success' => true]);
    }

    protected function generateCitySitemaps()
    {
        $cities = DB::table('dealers')
            ->whereNotNull('city')
            ->distinct()
            ->pluck('city')
            ->toArray();

        sort($cities);

        $indexUrls = [];
        $directory = public_path(self::SITEMAP_DIR);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        foreach ($cities as $index => $city) {
            $filename = self::SITEMAP_PREFIX . ($index + 1) . '.xml';
            $filepath = "$directory/$filename";

            $inventory = DB::table('main_inventories')
                ->join('dealers', 'dealers.id', '=', 'main_inventories.deal_id')
                ->where('dealers.city', $city)
                ->get([
                    'main_inventories.*',
                    'dealers.city as dealer_city',
                    'dealers.state as dealer_state'
                ]);

            $urls = [];
            foreach ($inventory as $item) {
                $model = str_replace(' ', '+', $item->model);
                $urls[] = [
                    'url' => url("/best-used-cars-for-sale/listing/{$item->vin}/{$item->year}-{$item->make}-{$model}-in-{$item->dealer_city}-{$item->dealer_state}"),
                    'lastmod' => now()->toAtomString(),
                    'changefreq' => 'daily',
                    'priority' => 0.8
                ];
            }

            File::put($filepath, view('sitemap', ['urls' => $urls])->render());
            $indexUrls[] = [
                'url' => url("/" . self::SITEMAP_DIR . "/$filename"),
                'lastmod' => now()->toAtomString()
            ];
        }

        File::put(
            "$directory/sitemap-index.xml",
            view('sitemap_index', ['urls' => $indexUrls])->render()
        );
    }

    public function usedSitemapIndex()
    {
        $filepath = public_path(self::SITEMAP_DIR . '/sitemap-index.xml');

        if (!file_exists($filepath)) {
            $this->generateCitySitemaps();
        }

        return response()->file($filepath);
    }

    public function showListingSitemap($id)
    {
        $filepath = public_path(self::SITEMAP_DIR . '/' . self::SITEMAP_PREFIX . $id . '.xml');

        if (!file_exists($filepath)) {
            abort(404, 'Sitemap not found');
        }

        return response()->file($filepath);
    }

    // public function generate()
    // {
    //     $inventory_urls = DB::table('main_inventories')
    //         ->select(
    //             'main_inventories.id',
    //             'main_inventories.make',
    //             'main_inventories.model',
    //             'main_inventories.body_formated',
    //             'main_inventories.year',
    //             'main_inventories.vin',
    //             'main_inventories.stock',
    //             'main_inventories.deal_id',
    //             'dealers.id as dealer_id',
    //             'dealers.city as dealer_city',
    //             'dealers.state as dealer_state'
    //         )
    //         ->join('dealers', 'dealers.id', '=', 'main_inventories.deal_id')
    //         ->whereNotNull('dealers.city')
    //         ->get();

    //     $sitemap_urls = [];
    //     foreach ($inventory_urls as $url) {
    //         $modifiedBodyString = str_replace(' ', '+', $url->body_formated);
    //         $modifiedModelString = str_replace(' ', '+', $url->model);
    //         $dynamic_url = route('home') . '/best-used-cars-for-sale/listing/' . $url->vin . '/' . $url->year . '-' . $url->make . '-' . $modifiedModelString . '-in-' . $url->dealer_city . '-' . $url->dealer_state;

    //         $sitemap_urls[] = [
    //             'url'       => $dynamic_url,
    //             'lastmod'   => now()->toAtomString(),
    //             'changefreq' => 'daily',
    //             'priority'  => $dynamic_url === route('home') ? '1.0' : '0.8000',
    //         ];
    //     }

    //     // Generate XML content
    //     $xml = view('sitemap', ['urls' => $sitemap_urls])->render();

    //     // Save to public/sitemap.xml
    //     $filePath = public_path('sitemap.xml');
    //     file_put_contents($filePath, $xml);

    //     // Return the XML response (optional)
    //     return response($xml, 200)->header('Content-Type', 'text/xml');
    // }

    // public function generateAllSitemaps()
    // {
    //     // 1. Generate main sitemap.xml
    //     $this->generateMainSitemap();

    //     // 2. Generate city-based sitemaps and index
    //     $this->generateCitySitemaps();

    //     return response()->json(['message' => 'All sitemaps generated successfully']);
    // }
    // *************************************************************************************************************************************
    // protected function generateMainSitemap()
    // {
    //     $inventory_urls = DB::table('main_inventories')
    //         ->select(
    //             'main_inventories.*',
    //             'dealers.city as dealer_city',
    //             'dealers.state as dealer_state'
    //         )
    //         ->join('dealers', 'dealers.id', '=', 'main_inventories.deal_id')
    //         ->whereNotNull('dealers.city')
    //         ->get();

    //     $sitemap_urls = [];
    //     foreach ($inventory_urls as $url) {
    //         $model = str_replace(' ', '+', $url->model);
    //         $dynamic_url = url("/best-used-cars-for-sale/listing/{$url->vin}/{$url->year}-{$url->make}-{$model}-in-{$url->dealer_city}-{$url->dealer_state}");

    //         $sitemap_urls[] = [
    //             'url'       => $dynamic_url,
    //             'lastmod'   => now()->toAtomString(),
    //             'changefreq' => 'daily',
    //             'priority'  => $dynamic_url === route('home') ? '1.0' : '0.8',
    //         ];
    //     }

    //     $xml = view('sitemap', ['urls' => $sitemap_urls])->render();
    //     File::put(public_path('sitemap.xml'), $xml);
    // }
    // ****************************************************************************************************************************************************
    // protected function generateCitySitemaps()
    // {
    //     $city_data = DB::table('dealers')
    //         ->whereNotNull('city')
    //         ->distinct()
    //         ->pluck('city')
    //         ->toArray();

    //     sort($city_data);

    //     $sitemap_index_urls = [];
    //     $directory = public_path('sitemaps/used');

    //     // Ensure directory exists
    //     if (!File::isDirectory($directory)) {
    //         File::makeDirectory($directory, 0755, true);
    //     }

    //     foreach ($city_data as $index => $city) {
    //         $filename = 'sitemap-city-' . Str::slug($city) . '.xml';
    //         $filepath = $directory . '/' . $filename;

    //         // Generate city sitemap
    //         $inventory_urls = DB::table('main_inventories')
    //             ->join('dealers', 'dealers.id', '=', 'main_inventories.deal_id')
    //             ->where('dealers.city', $city)
    //             ->get([
    //                 'main_inventories.*',
    //                 'dealers.city as dealer_city',
    //                 'dealers.state as dealer_state'
    //             ]);

    //         $sitemap_urls = [];
    //         foreach ($inventory_urls as $item) {
    //             $model = str_replace(' ', '+', $item->model);
    //             $sitemap_urls[] = [
    //                 'url' => url("/best-used-cars-for-sale/listing/{$item->vin}/{$item->year}-{$item->make}-{$model}-in-{$item->dealer_city}-{$item->dealer_state}"),
    //                 'lastmod' => now()->toAtomString(),
    //                 'changefreq' => 'daily',
    //                 'priority' => 0.8
    //             ];
    //         }

    //         $xml = view('sitemap', ['urls' => $sitemap_urls])->render();
    //         File::put($filepath, $xml);

    //         // Add to sitemap index
    //         $sitemap_index_urls[] = [
    //             'url' => url("/sitemaps/used/sitemap-city-" . Str::slug($city) . '.xml'),
    //             'lastmod' => now()->toAtomString()
    //         ];
    //     }

    //     // Generate sitemap index
    //     // Generate sitemap index
    //     $index_xml = view('sitemap_index', ['urls' => $sitemap_index_urls])->render();
    //     File::put($directory . '/sitemap-index.xml', $index_xml);  // Removed extra parenthesis
    // }

    // public function usedSitemapIndex()
    // {
    //     // Fetch distinct cities from the User model
    //     $city_data = DB::table('dealers')->whereNotNull('city')->distinct()->pluck('city')->toArray();
    //     sort($city_data);  // Sort cities alphabetically

    //     // Initialize sitemap URLs array
    //     $sitemap_urls = [];
    //     foreach ($city_data as $index => $city) {
    //         $sitemap_urls[] = [
    //             'url'       => route('sitemap.based.city', ['param' => $index + 1]),
    //             'lastmod'   => now()->toAtomString(),
    //             'changefreq' => 'daily',
    //             'priority'  => '0.8000',
    //         ];
    //     }

    //     // Generate the XML content
    //     $xml = view('sitemap_index', ['urls' => $sitemap_urls])->render();

    //     // Ensure directory exists
    //     $directory = public_path('sitemaps/used');
    //     if (!File::exists($directory)) {
    //         File::makeDirectory($directory, 0755, true);
    //     }

    //     // Save to file
    //     $filePath = $directory . '/sitemap-index.xml';
    //     File::put($filePath, $xml);

    //     // Return the XML response
    //     return response($xml)->header('Content-Type', 'text/xml');
    // }


    // public function sitemapBasedOnCity($param)
    // {
    //     ob_clean();

    //     $city_data = DB::table('dealers')->whereNotNull('city')->distinct()->pluck('city')->toArray();
    //     sort($city_data);
    //     $index = $param - 1;

    //     if (!isset($city_data[$index])) {
    //         abort(404, 'City not found');
    //     }

    //     $city = $city_data[$index];
    //     $filename = 'sitemap-city-' . Str::slug($city) . '.xml';
    //     $directory = public_path('sitemaps/used');
    //     $filepath = $directory . '/' . $filename;

    //     // Check cache first
    //     if (file_exists($filepath) && time() - filemtime($filepath) < 86400) {
    //         return response()->file($filepath);
    //     }

    //     // Get inventory data
    //     $inventory_urls = DB::table('main_inventories')
    //         ->join('dealers', 'dealers.id', '=', 'main_inventories.deal_id')
    //         ->where('dealers.city', $city)
    //         ->get([
    //             'main_inventories.*',
    //             'dealers.city as dealer_city',
    //             'dealers.state as dealer_state'
    //         ]);

    //     // Prepare URLs
    //     $sitemap_urls = [];
    //     foreach ($inventory_urls as $item) {
    //         $model = str_replace(' ', '+', $item->model);
    //         $sitemap_urls[] = [
    //             'url' => url("/best-used-cars-for-sale/listing/{$item->vin}/{$item->year}-{$item->make}-{$model}-in-{$item->dealer_city}-{$item->dealer_state}"),
    //             'lastmod' => now()->toAtomString(),
    //             'changefreq' => 'daily',
    //             'priority' => 0.8
    //         ];
    //     }

    //     // Generate XML
    //     $xml = view('sitemap', ['urls' => $sitemap_urls])->render();

    //     // Ensure directory exists
    //     if (!File::isDirectory($directory)) {
    //         File::makeDirectory($directory, 0755, true, true);
    //     }

    //     // Save file
    //     if (File::put($filepath, $xml) === false) {
    //         Log::error("Failed to write sitemap file: " . $filepath);
    //         abort(500, "Could not generate sitemap");
    //     }

    //     return response($xml, 200, [
    //         'Content-Type' => 'text/xml',
    //         'Cache-Control' => 'public, max-age=86400'
    //     ]);
    // }
    // *************************************************************************************************************************************
    // public function generate()
    // {
    //     // Clear any output buffer
    //     ob_clean();
    //     // $inventory_urls = MainInventory::select('id', 'make', 'model', 'body_formated', 'year', 'vin', 'stock', 'deal_id')
    //     //     ->whereHas('dealer', function ($query) {
    //     //         $query->whereNotNull('city'); // Filter only inventories with a dealer that has a city
    //     //     })
    //     //     ->with(['dealer:id,city,state']) // Eager load the dealer relationship
    //     //     ->get();

    //     $inventory_urls = DB::table('main_inventories')
    //         ->select(
    //             'main_inventories.id',
    //             'main_inventories.make',
    //             'main_inventories.model',
    //             'main_inventories.body_formated',
    //             'main_inventories.year',
    //             'main_inventories.vin',
    //             'main_inventories.stock',
    //             'main_inventories.deal_id',
    //             'dealers.id as dealer_id',
    //             'dealers.city as dealer_city',
    //             'dealers.state as dealer_state'
    //         )
    //         ->join('dealers', 'dealers.id', '=', 'main_inventories.deal_id')
    //         ->whereNotNull('dealers.city')
    //         ->get();

    //     $sitemap_urls = [];
    //     foreach ($inventory_urls as $url) {
    //         $modifiedBodyString = str_replace(' ', '+', $url->body_formated);
    //         $modifiedModelString = str_replace(' ', '+', $url->model);
    //         $dynamic_url = route('home') . '/best-used-cars-for-sale/listing/' . $url->vin . '/' . $url->year . '-' . $url->make . '-' . $modifiedModelString . '-in-' . $url->dealer_city . '-' . $url->dealer_state;

    //         $sitemap_urls[] = [
    //             'url'       => $dynamic_url,
    //             'lastmod'   => now()->toAtomString(),
    //             'changefreq' => 'daily',
    //             'priority'  => $dynamic_url === route('home') ? '1.0' : '0.8000',
    //         ];
    //     }

    //     return response()->view('sitemap', ['urls' => $sitemap_urls])
    //         ->header('Content-Type', 'text/xml');
    // }


    // public function usedSitemapIndex()
    // {
    //     // Fetch distinct cities from the User model
    //     $city_data = DB::table('dealers')->whereNotNull('city')->distinct()->pluck('city')->toArray();
    //     sort($city_data);  // Sort cities alphabetically

    //     // Initialize sitemap URLs array
    //     $sitemap_urls = [];
    //     foreach ($city_data as $index => $city) {
    //         // Construct the URL for each city's sitemap using the index (1-based)
    //         $sitemap_urls[] = [
    //             'url'       => route('sitemap.based.city', ['param' => $index + 1]),  // Pass 1-based index
    //             'lastmod'   => now()->toAtomString(),
    //             'changefreq' => 'daily',
    //             'priority'  => '0.8000',  // Adjust the priority as needed
    //         ];
    //     }

    //     // dd($sitemap_urls);
    //     // Return the sitemap index as an XML response
    //     return response()->view('sitemap_index', ['urls' => $sitemap_urls])
    //         ->header('Content-Type', 'text/xml');
    // }




    // public function sitemapBasedOnCity($param)
    // {
    //     ob_clean();

    //     $city_data = User::whereNotNull('city')->distinct()->pluck('city')->toArray();
    //     sort($city_data);  // Sorts the cities alphabetically
    //     $city = '';
    //     $index = $param - 1;  // Subtract 1 to convert the 1-based index to 0-based
    //     if (isset($city_data[$index])) {
    //         $city = $city_data[$index];  // Get the city at that index
    //     } else {
    //         dd('City not found for the given parameter');
    //     }


    //     $inventory_urls = MainInventory::select('id', 'make', 'model', 'body_formated', 'year', 'vin', 'stock', 'deal_id')
    //         ->with(['dealer:id,city,state']) // Eager load the dealer relationship
    //         ->whereHas('dealer', function ($query) use ($city) {
    //             $query->where('city', $city); // Filter by city
    //         })
    //         ->get();

    //     $sitemap_urls = [];
    //     foreach ($inventory_urls as $url) {
    //         $modifiedBodyString = str_replace(' ', '+', $url->body_formated);
    //         $modifiedModelString = str_replace(' ', '+', $url->model);
    //         $dynamic_url = route('home') . '/best-used-cars-for-sale/listing/' . $url->vin . '/' . $url->year . '-' . $url->make . '-' . $modifiedModelString . '-in-' . $url->dealer->city . '-' . $url->dealer->state;

    //         $sitemap_urls[] = [
    //             'url'       => $dynamic_url,
    //             'lastmod'   => now()->toAtomString(),
    //             'changefreq' => 'daily',
    //             'priority'  => $dynamic_url === route('home') ? '1.0' : '0.8000',
    //         ];
    //     }
    //     // dd($sitemap_urls);
    //     // dd('dollin', $city_data, $sitemap_urls);
    //     return response()->view('sitemap', ['urls' => $sitemap_urls])
    //         ->header('Content-Type', 'text/xml');
    // }


    public function newsGenerate()
    {
        // Clear any output buffer
        ob_clean();

        $inventory_urls = News::pluck('slug');


        $sitemap_urls = [];
        foreach ($inventory_urls as $url) {
            $dynamic_url = route('home') . '/auto-news/' . $url;

            $sitemap_urls[] = [
                'url'       => $dynamic_url,
                'lastmod'   => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority'  => $dynamic_url === route('home') ? '1.0' : '0.8000',
            ];
        }

        return response()->view('sitemap', ['urls' => $sitemap_urls])
            ->header('Content-Type', 'text/xml');
    }

    public function researchGenerate()
    {
        // Clear any output buffer
        ob_clean();

        $inventory_urls = Blog::pluck('slug');


        $sitemap_urls = [];
        foreach ($inventory_urls as $url) {
            $dynamic_url = route('home') . '/articles/' . $url;

            $sitemap_urls[] = [
                'url'       => $dynamic_url,
                'lastmod'   => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority'  => $dynamic_url === route('home') ? '1.0' : '0.8000',
            ];
        }

        return response()->view('sitemap', ['urls' => $sitemap_urls])
            ->header('Content-Type', 'text/xml');
    }




    // public function generate()
    // {
    //     SitemapGenerator::create(config('app.url'))
    //         ->writeToFile(public_path('sitemap.xml'));

    //     return response()->view('sitemap');
    // }

    // public function generate()
    // {
    //     // Get inventory URLs from the database (assuming Inventory is an Eloquent model)
    //     $inventory_urls = Inventory::select('id', 'make', 'model', 'body_formated', 'year', 'vin', 'stock')
    //                         ->with(['dealer:id,city,state'])
    //                         ->get();
    //     // dd($inventory_urls);

    //     // $inventory_urls = Inventory::select('id','make','model','body_formated','year','vin','dealer_city','dealer_state','stock')->get();
    //     // dd(route('home'));

    //     // Define static URLs
    //     $static_urls = [
    //         route('home'),
    //         // route('news.show'),
    //         route('contact'),
    //         // 'https://dreambestcar.com/recently-added',
    //         // 'https://dreambestcar.com/favorite/listing',
    //     ];

    //     // Combine static URLs with inventory URLs
    //     $sitemap_urls = [];
    //     foreach ($inventory_urls as $url) {
    //         $modifiedBodyString = str_replace(' ', '+', $url->body_formated);
    //         $modifiedModelString = str_replace(' ', '+', $url->model);
    //         // ACURA-Integra-in-Grapevine-TX
    //         $dynamic_url = route('home').'/best-used-cars-for-sale/listing/' . $url->vin . '/' . $url->year . '-' . $url->make . '-' . $modifiedModelString . '-in-' . $url->dealer_city . '-' . $url->dealer_state;

    //         $sitemap_urls[] = [
    //             'url'       => $dynamic_url,
    //             'lastmod'   => now()->toAtomString(),
    //             'changefreq' => 'daily',
    //             'priority'  => $dynamic_url === route('home') ? '1.0' : '0.8000',
    //         ];
    //     }

    //     // Merge static URLs with dynamic URLs
    //     $all_urls = array_merge($static_urls, array_column($sitemap_urls, 'url'));

    //     // Generate the sitemap URLs array
    //     $sitemap_urls = [];
    //     foreach ($all_urls as $url) {
    //         $sitemap_urls[] = [
    //             'url'       => $url,
    //             'lastmod'   => now()->toAtomString(),
    //             'changefreq' => 'daily',
    //             'priority'  => $url === route('home') ? '1.0' : '0.8000',
    //         ];
    //     }

    //     // Return the sitemap as an XML response
    //     return response()->view('sitemap', ['urls' => $sitemap_urls])->header('Content-Type', 'text/xml');
    // }



    // {
    //     $inventory_url = Inventory::get();
    //     // dd($inventory_url);


    //     $urls = [
    //         ['url' => 'https://localcarz.com/', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '1.0'],
    //         ['url' => 'https://localcarz.com/car-news', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '1.0'],
    //         ['url' => 'https://localcarz.com/recently-added', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '1.0'],
    //         ['url' => 'https://localcarz.com/favorite/listing', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '1.0'],
    //         ['url' => 'https://localcarz.com/contact-us', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '1.0'],


    //         foreach ($inventory_url as $item) {
    //             echo $item . '<br>';
    //         }


    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCBKC8FR525421/listing/2015-Chevrolet-Tahoe-Sport+Utility-D254211', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1HGCV1F32JA233817/listing/2018-Honda-Accord%20Sedan-4dr+Car-T33817', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPE34AF0JH653102/listing/2018-Hyundai-Sonata-4dr+Car-T53102', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/2C3CCAAG2MH609263/listing/2021-Chrysler-300-4dr+Car-609263', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1C4BJWDG3GL302262/listing/2016-Jeep-Wrangler%20Unlimited-Convertible-302262', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GCGSCEN3J1317250/listing/2018-Chevrolet-Colorado-Crew+Cab+Pickup-D17250', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/2G1125S34J9149742/listing/2018-Chevrolet-Impala-4dr+Car-149742', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPDH4AE5FH591305/listing/2015-Hyundai-Elantra-4dr+Car-T91305', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/3N1CE2CP4JL352203/listing/2018-Nissan-Versa%20Note-Hatchback-352203', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4BV0LC234079/listing/2020-Nissan-Altima-4dr+Car-234079', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4BV4LC251743/listing/2020-Nissan-Altima-4dr+Car-251743', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCAKC2GR445334/listing/2016-Chevrolet-Tahoe-Sport+Utility-445334', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/3KPFK4A79HE062751/listing/2017-Kia-Forte-4dr+Car-062751', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N6AD0ER0FN719497/listing/2015-Nissan-Frontier-Crew+Cab+Pickup-T19497', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/55SWF4JB4FU071453/listing/2015-Mercedes-Benz-C-Class-4dr+Car-071453', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPE34AF4JH677709/listing/2018-Hyundai-Sonata-4dr+Car-677709', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/3N1CN8EV9ML924823/listing/2021-Nissan-Versa-4dr+Car-924823', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4CVXMN363757/listing/2021-Nissan-Altima-4dr+Car-363757', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GKKNPLS7MZ151651/listing/2021-GMC-Acadia-Sport+Utility-151651', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5N1DR3AC5NC252832/listing/2022-Nissan-Pathfinder-Sport+Utility-252832', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/4T1BF1FK1HU672638/listing/2017-Toyota-Camry-4dr+Car-672638', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/3GKALMEV2JL211135/listing/2018-GMC-Terrain-Sport+Utility-211135', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/2G61M5S30E9170369/listing/2014-Cadillac-XTS-4dr+Car-170369', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/3GNAXKEV8NL226823/listing/2022-Chevrolet-Equinox-Sport+Utility-226823', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4AA6AP8HC429886/listing/2017-Nissan-Maxima-4dr+Car-429886', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N6BD0CT4HN767011/listing/2017-Nissan-Frontier-Extended+Cab+Pickup-767011', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/JTHBK1GG9G2222593/listing/2016-Lexus-ES%20350-4dr+Car-222593', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCBKC8FR525421/listing/2016-Chevrolet-Tahoe-Sport+Utility-445334', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCBKC8GR459793/listing/2016-Chevrolet-Tahoe-Sport+Utility-459793', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCBKC8FR525421/listing/2016-Chevrolet-Tahoe-Sport+Utility-459793', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCBKCXFR512587/listing/2015-Chevrolet-Tahoe-Sport+Utility-512587', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCBKC8FR525421/listing/2015-Chevrolet-Tahoe-Sport+Utility-512587', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/3GCPWDEK6MG196879/listing/2021-Chevrolet-Silverado%201500-Crew+Cab+Pickup-196879', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/3GCUKSEC8HG464395/listing/2017-Chevrolet-Silverado%201500-Crew+Cab+Pickup-464395', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1C6RR6TT1KS653781/listing/2019-Ram-1500%20Classic-Crew+Cab+Pickup-653781', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1HGCV1F38JA203320/listing/2018-Honda-Accord%20Sedan-4dr+Car-203320', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1HGCV1F32JA233817/listing/2018-Honda-Accord%20Sedan-4dr+Car-203320', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1HGCR2F56HA217190/listing/2017-Honda-Accord%20Sedan-4dr+Car-217190', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1HGCV1F32JA233817/listing/2017-Honda-Accord%20Sedan-4dr+Car-217190', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPE34ABXFH109937/listing/2015-Hyundai-Sonata-4dr+Car-D09937', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPE34AF0JH653102/listing/2015-Hyundai-Sonata-4dr+Car-D09937', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPE34AF3KH806332/listing/2019-Hyundai-Sonata-4dr+Car-806332', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPE34AF0JH653102/listing/2019-Hyundai-Sonata-4dr+Car-806332', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPE34AF0JH653102/listing/2018-Hyundai-Sonata-4dr+Car-6777092', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1C4HJXDG4JW112430/listing/2018-Jeep-Wrangler%20Unlimited-Convertible-D12430', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1C4BJWDG3GL302262/listing/2018-Jeep-Wrangler%20Unlimited-Convertible-D12430', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GCHSBEA0H1276596/listing/2017-Chevrolet-Colorado-Extended+Cab+Pickup-276596', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GCGTCEN3J1274848/listing/2018-Chevrolet-Colorado-Crew+Cab+Pickup-274848', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GCGSCEN3J1317250/listing/2018-Chevrolet-Colorado-Crew+Cab+Pickup-274848', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],

    //         ['url' => 'https://localcarz.com/used-cars-for-sale/2G1125S34J9149742/listing/2018-Chevrolet-Impala-4dr+Car-137866', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],

    //         ['url' => 'https://localcarz.com/used-cars-for-sale/2G1105S39J9137866/listing/2018-Chevrolet-Impala-4dr+Car-137866', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPDH4AE0GH735540/listing/2016-Hyundai-Elantra-4dr+Car-735540', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/3N1CE2CP8JL353242/listing/2018-Nissan-Versa%20Note-Hatchback-353242', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GCGSCEN3J1317250/listing/2018-Chevrolet-Colorado-Crew+Cab+Pickup-274848', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/5NPDH4AE5FH591305/listing/2016-Hyundai-Elantra-4dr+Car-735540', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/3N1CE2CP4JL352203/listing/2018-Nissan-Versa%20Note-Hatchback-353242', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4BV6MN356578/listing/2021-Nissan-Altima-4dr+Car-356578', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4BV0LC234079/listing/2021-Nissan-Altima-4dr+Car-356578', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4DV5MN365818/listing/2021-Nissan-Altima-4dr+Car-365818', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4BV0LC234079/listing/2020-Nissan-Altima-4dr+Car-251743', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4BV0LC234079/listing/2021-Nissan-Altima-4dr+Car-365818', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4BV4LC251743/listing/2020-Nissan-Altima-4dr+Car-234079', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1N4BL4BV4LC251743/listing/2021-Nissan-Altima-4dr+Car-356578', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],

    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCAKC2GR445334/listing/2016-Chevrolet-Tahoe-Sport+Utility-459793', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCAKC2GR445334/listing/2015-Chevrolet-Tahoe-Sport+Utility-512587', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         ['url' => 'https://localcarz.com/used-cars-for-sale/1GNSCAKC2GR445334/listing/2015-Chevrolet-Tahoe-Sport+Utility-D25421', 'lastmod' => now(), 'changefreq' => 'daily', 'priority' => '0.8000'],
    //         // Add more URLs as needed

    //     ];

    //     return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'text/xml');
    // }
}
