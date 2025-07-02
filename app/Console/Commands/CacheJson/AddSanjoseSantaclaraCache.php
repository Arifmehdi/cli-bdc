<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddSanjoseSantaclaraCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-sanjose-santaclara-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Sanjose Santa Clara Zip Code data';

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

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/santa_clara_county.json'); // File path for caching


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

        $this->info('Sanjos Santa Clara data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
