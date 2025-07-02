<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddDetroitWayneCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-detroit-wayne-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Detroit - Wayne Zip Code data';

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
            "13143", "13146", "13154", "14413", "14433", "14449", "14489", "14502", "14505", "14513", 
            "14516", "14519", "14520", "14522", "14538", "14542", "14551", "14555", "14563", "14568", 
            "14589", "14590", "14593", "18405", "18417", "18424", "18427", "18428", "18431", "18436", 
            "18437", "18438", "18439", "18443", "18445", "18449", "18453", "18454", "18455", "18456", 
            "18459", "18460", "18461", "18462", "18463", "18469", "18472", "18473", "25507", "25511", 
            "25512", "25514", "25517", "25530", "25534", "25535", "25555", "25562", "25570", "25669", 
            "25699", "25709", "28460", "44214", "44217", "44230", "44270", "44276", "44287", "44606", 
            "44618", "44627", "44636", "44645", "44659", "44667", "44676", "44677", "44691", "44694", 
            "44696", "45316", "48201", "48202", "48203", "48204", "48205", "48206", "48207", "48208", 
            "48209", "48210", "48211", "48212", "48213", "48214", "48215", "48216", "48217", "48218", 
            "48219", "48221", "48222", "48223", "48224", "48225", "48226", "48227", "48228", "48229", 
            "48230", "48231", "48232", "48233", "48234", "48235", "48236", "48238", "48239", "48240", 
            "48242", "48243", "48244", "48260", "48264", "48266", "48267", "48268", "48275", "48277", 
            "48278", "48279", "48288", "48168", "48193", "39322", "39324", "39367", "63632", "63934", 
            "63944", "63950", "63951", "63952", "63956", "63957", "63964", "63966", "63967", "63763", 
            "68787", "68723", "68740", "68790", "14502", "14505", "14522", "14563", "14538", "14551", 
            "14542", "14555", "14516", "14568", "14590", "27530", "27532", "27533", "27534", "27830", 
            "27863", "28333", "28365", "28578", "27531", "53515", "52590", "52583", "50008", "50052", 
            "47324", "47327", "47330", "47335", "47339", "47341", "47345", "47346", "47357", "47374", 
            "47375", "47392", "47393", "47370", "42633", "48101", "48111", "48112", "48120", "48121", 
            "48123", "48124", "48125", "48126", "48127", "48128", "48134", "48135", "48136", "48138", 
            "48141", "48146", "48150", "48151", "48152", "48153", "48154", "48164", "48167", "48170", 
            "48173", "48174", "48180", "48183", "48184", "48185", "48186", "48187", "48188", "48192", 
            "48195", "48201", "48202", "48203", "48204", "48205", "48206", "48207", "48208", "48209", 
            "48210", "48211", "48212", "48213", "48214", "48215", "48216", "48217", "48218", "48219", 
            "48220", "48224", "48225", "48233", "48240", "48246", "48255", "48260", "48265", "48270", 
            "48271", "50008", "50052", "50060", "50068", "50123", "50147", "50165", "50349", "50440", 
            "51551", "52144", "51335", "52301", "52360", "52999", "52967", "52947", "52980", "52963", 
            "53515"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/wayne_county.json'); // File path for caching


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

        $this->info('Detroit - Waine data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
