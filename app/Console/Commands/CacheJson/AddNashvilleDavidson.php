<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddNashvilleDavidson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-nashville-davidson-cache';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Nashville-Davidson Zip Code data';

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
            "27239", "27292", "27293", "27294", "27295", "27299", "27351", "27360", "27361", "27373", 
            "27374", "37011", "37013", "37070", "37072", "37076", "37080", "37115", "37116", "37138", 
            "37189", "37201", "37202", "37203", "37204", "37205", "37206", "37207", "37208", "37209", 
            "37210", "37211", "37212", "37213", "37214", "37215", "37216", "37217", "37218", "37219", 
            "37220", "37221", "37222", "37224", "37227", "37228", "37229", "37230", "37232", "37234", 
            "37235", "37236", "37238", "37240", "37241", "37242", "37243", "37244", "37246", "37250"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/davidson_county.json'); // File path for caching


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

        $this->info('OK Nashville-Davidson data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
