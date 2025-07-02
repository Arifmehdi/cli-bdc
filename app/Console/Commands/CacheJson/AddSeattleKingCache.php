<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddSeattleKingCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-seattle-king-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Seattle King Zip Code data';

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

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/king_county.json'); // File path for caching


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

        $this->info('Seattle King data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
