<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddAustinTravisCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-austin-travis-cache';

    // php artisan add-newyork-cache
    // php artisan add-philadelphia-cache
    // php artisan add-phoenix-maricopa-cache
    // php artisan add-san-diago-cache
    // php artisan add-san-fransisco-cache
    // php artisan add-sanjose-santaclara-cache
    // php artisan add-seatle-king-cache
    // php artisan add-washington-dc-cache

    // php artisan add-austin-travis-cache
    // php artisan add-charlotte-mecklenburg-cache
    // php artisan add-chicago-cook-cache

    // php artisan add-jacksonville-duval-cache
    // php artisan add-los-angeles-cache
    // php artisan add-memphis-volusia-cache
    // php artisan add-nashville-davidson-cache
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Jacksonville - Duval Zip Code data';

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

        $zip_code = [
            "73301", "73344", "78617", "78645", "78651", "78652", "78653", "78660", "78669", "78691",
            "78701", "78702", "78703", "78704", "78705", "78708", "78709", "78710", "78711", "78712",
            "78713", "78714", "78715", "78716", "78718", "78719", "78720", "78721", "78722", "78723",
            "78724", "78725", "78726", "78727", "78728", "78730", "78731", "78732", "78733", "78734",
            "78735", "78736", "78737", "78738", "78739", "78741", "78742", "78744", "78745", "78746",
            "78747", "78748", "78749", "78750", "78751", "78752", "78753", "78754", "78755", "78756",
            "78757", "78758", "78759", "78760", "78761", "78762", "78763", "78764", "78765", "78766",
            "78767", "78768", "78769", "78772", "78773", "78774", "78778", "78779", "78783", "78799"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/travis_county.json'); // File path for caching


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

        $this->info('Jacksonville - Duval data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
