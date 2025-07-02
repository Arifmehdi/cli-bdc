<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddCharlotteMecklenburgCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-charlotte-mecklenburg-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Charlotte - Mecklenburg Zip Code data';

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

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/mecklenburg_county.json'); // File path for caching


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

        $this->info('Charlotte - Mecklenburg data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
