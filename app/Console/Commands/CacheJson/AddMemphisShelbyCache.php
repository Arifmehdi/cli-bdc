<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddMemphisShelbyCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-memphis-shelby-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Memphis Volusia Zip Code data';

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
            "35007", "35040", "35043", "35051", "35078", "35080", "35114", "35115", 
            "35124", "35137", "35143", "35144", "35147", "35176", "35178", "35185", 
            "35186", "35187", "35242", "37501", "37544", "38002", "38014", "38016", 
            "38017", "38018", "38027", "38028", "38029", "38053", "38054", "38055", 
            "38083", "38088", "38101", "38103", "38104", "38105", "38106", "38107", 
            "38108", "38109", "38111", "38112", "38113", "38114", "38115", "38116", 
            "38117", "38118", "38119", "38120", "38122", "38124", "38125", "38126", 
            "38127", "38128", "38130", "38131", "38132", "38133", "38134", "38135", 
            "38136", "38137", "38138", "38139", "38141", "38145", "38148", "38150", 
            "38151", "38152", "38157", "38159", "38161", "38163", "38166", "38167", 
            "38168", "38173", "38174", "38175", "38177", "38181", "38182", "38183", 
            "38184", "38186", "38187", "38188", "38190", "38193", "38194", "38197", 
            "40003", "40022", "40065", "40066", "40067", "40076", "45302", "45306", 
            "45333", "45334", "45336", "45340", "45353", "45360", "45363", "45365", 
            "45367", "45845", "46110", "46126", "46130", "46144", "46161", "46176", 
            "46182", "47234", "51446", "51447", "51527", "51530", "51531", "51537", 
            "51562", "51565", "51578", "61957", "62422", "62431", "62438", "62444", 
            "62462", "62463", "62465", "62534", "62550", "62553", "62565", "62571", 
            "63434", "63437", "63439", "63443", "63450", "63451", "63468", "63469", 
            "75935", "75954", "75973", "75974", "75975"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/shelby_county.json'); // File path for caching


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

        $this->info('Memphis-Volusia data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
