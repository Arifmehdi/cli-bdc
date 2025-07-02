<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddNewYorkCached extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-newyork-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the New York Zip Code data';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // $cacheFilePath = storage_path('app/san_antonio_county.json');

        // // Check if the file exists
        // if (!file_exists($cacheFilePath)) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'Cache file not found'
        //     ], 404);
        // }

        // // Read the JSON file
        // $jsonData = file_get_contents($cacheFilePath);
        // dump(json_decode($jsonData));
        // dd('53 bilion');
        // // Convert to JSON response
        // return response()->json(json_decode($jsonData), 200);

        $zip_codes = [
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

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/new_york_county.json'); // File path for caching


        // Clear old cache file if it exists
        if (file_exists($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        // Open the file in append mode
        $file = fopen($cacheFilePath, 'a');

        // Write an opening bracket for JSON array
        fwrite($file, '[');

        $firstChunk = true; // Flag to handle commas between records
        // $mainInventory = MainInventory::whereIn('zip_code',$zip_code)->get();

        $mainInventory = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'payment_price', 'exterior_color', 'interior_color', 'fuel', 'body_formated','drive_info', 'transmission','stock_date_formated')
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
            ])->whereIn('zip_code', $zip_codes)
            // ->get();;
            ->chunk($chunkSize, function ($chunk, $index) use ($file, &$firstChunk) {
                foreach ($chunk as $dealer) {
                    // Serialize each dealer as JSON
                    $dealerJson = json_encode($dealer);

                    // Add a comma between records (except for the first one)
                    if (!$firstChunk) {
                        fwrite($file, ',');
                    } else {
                        $firstChunk = false;
                    }

                    // Write the dealer JSON to the file
                    fwrite($file, $dealerJson);
                }
            });

        fwrite($file, ']');

        // Close the file
        fclose($file);

        $this->info('New York data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
