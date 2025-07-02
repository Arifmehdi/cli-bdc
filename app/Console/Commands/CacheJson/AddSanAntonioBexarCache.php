<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddSanAntonioBexarCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-sanantonio-bexar-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the San Antonio - Bexar Zip Code data';

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
            '78201', '78202', '78203', '78204', '78205', '78206', '78207', '78208', '78209', '78210',
            '78211', '78212', '78213', '78214', '78215', '78216', '78217', '78218', '78219', '78220',
            '78221', '78222', '78223', '78224', '78225', '78226', '78227', '78228', '78229', '78230',
            '78231', '78232', '78233', '78234', '78235', '78236', '78237', '78238', '78239', '78240',
            '78241', '78242', '78243', '78244', '78245', '78246', '78247', '78248', '78249', '78250',
            '78251', '78252', '78253', '78254', '78255', '78256', '78257', '78258', '78259', '78260',
            '78261', '78262', '78263', '78264', '78265', '78266', '78268', '78269', '78270', '78275',
            '78278', '78279', '78280', '78283', '78284', '78285', '78286', '78287', '78288', '78289',
            '78291', '78292', '78293', '78294', '78295', '78296', '78297', '78298', '78299'
        ];
        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/bexar_county.json'); // File path for caching


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

        $this->info('San Antonio - Bexar data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
