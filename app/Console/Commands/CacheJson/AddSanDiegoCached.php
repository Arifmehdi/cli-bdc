<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddSanDiegoCached extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-san-diago-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the San DIago Zip Code data';

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
            "91901", "91902", "91903", "91905", "91906", "91908", "91909", "91910", "91911", "91912",
            "91913", "91914", "91915", "91916", "91917", "91921", "91931", "91932", "91933", "91934",
            "91935", "91941", "91942", "91943", "91944", "91945", "91946", "91948", "91950", "91951",
            "91962", "91963", "91976", "91977", "91978", "91979", "91980", "91987", "92003", "92004",
            "92007", "92008", "92009", "92010", "92011", "92013", "92014", "92018", "92019", "92020",
            "92021", "92022", "92023", "92024", "92025", "92026", "92027", "92028", "92029", "92030",
            "92033", "92036", "92037", "92038", "92039", "92040", "92046", "92049", "92051", "92052",
            "92054", "92055", "92056", "92057", "92058", "92059", "92060", "92061", "92064", "92065",
            "92066", "92067", "92068", "92069", "92070", "92071", "92072", "92074", "92075", "92078",
            "92079", "92081", "92082", "92083", "92084", "92085", "92086", "92088", "92091", "92092",
            "92093", "92096", "92101", "92102", "92103", "92104", "92105", "92106", "92107", "92108",
            "92109", "92110", "92111", "92112", "92113", "92114", "92115", "92116", "92117", "92118",
            "92119", "92120", "92121", "92122", "92123", "92124", "92126", "92127", "92128", "92129",
            "92130", "92131", "92132", "92134", "92135", "92136", "92137", "92138", "92139", "92140",
            "92142", "92143", "92145", "92147", "92149", "92150", "92152", "92153", "92154", "92155",
            "92158", "92159", "92160", "92161", "92163", "92165", "92166", "92167", "92168", "92169",
            "92170", "92171", "92172", "92173", "92174", "92175", "92176", "92177", "92178", "92179",
            "92182", "92186", "92187", "92190", "92191", "92192", "92193", "92195", "92196", "92197",
            "92198", "92199"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/san_diego_county.json'); // File path for caching


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

        $this->info('San Diago data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
