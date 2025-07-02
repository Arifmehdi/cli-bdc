<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddFortWorthTarrantCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-fortworth-tarrant-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Fort Worth - Tarrant Zip Code data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // here have 4 digit zip code ...

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

        $zip_code = [
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

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/tarrant_county.json'); // File path for caching


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
            ])->whereIn('zip_code', $zip_code)
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

        $this->info('Fort Worth - Tarrant data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
