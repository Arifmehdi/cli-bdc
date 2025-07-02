<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddColumbusFranklinCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-columbus-franklin-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Columbus - Franklin Zip Code data';

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
            "01054", "01072", "01093", "01301", "01302", "01330", "01337", "01338", "01339", "01340", "01341", "01342", "01344",
            "01346", "01347", "01349", "01350", "01351", "01354", "01355", "01360", "01364", "01367", "01370", "01373", "01375",
            "01376", "01378", "01379", "01380", "04225", "04227", "04234", "04239", "04262", "04285", "04294", "04936", "04938",
            "04940", "04947", "04955", "04956", "04964", "04966", "04970", "04982", "04983", "04984", "04992", "05441", "05447",
            "05448", "05450", "05454", "05455", "05457", "05459", "05460", "05470", "05471", "05476", "05478", "05479", "05481",
            "05483", "05485", "05488", "12914", "12915", "12916", "12917", "12920", "12926", "12930", "12937", "12939", "12945",
            "12953", "12957", "12966", "12969", "12970", "12976", "12980", "12983", "12986", "12989", "12995", "13655", "17201",
            "17202", "17210", "17214", "17217", "17219", "17220", "17221", "17222", "17224", "17225", "17231", "17232", "17235",
            "17236", "17237", "17244", "17246", "17247", "17250", "17251", "17252", "17254", "17256", "17261", "17262", "17263",
            "17265", "17268", "17271", "17272", "24065", "24067", "24088", "24092", "24102", "24137", "24146", "24151", "24176",
            "24184", "27508", "27525", "27549", "27596", "30520", "30521", "30553", "30639", "30662", "32318", "32320", "32322",
            "32323", "32328", "32329", "35571", "35581", "35582", "35585", "35593", "35653", "35654", "37306", "37318", "37324",
            "37330", "37345", "37375", "37376", "37383", "37398", "39630", "39647", "39653", "39661", "40601", "40602", "40603",
            "40604", "40618", "40619", "40620", "40621", "40622", "43002", "43004", "43016", "43017", "43026", "43054", "43068",
            "43069", "43081", "43085", "43086", "43109", "43119", "43123", "43125", "43126", "43194", "43195", "43199", "43201",
            "43202", "43203", "43204", "43205", "43206", "43207", "43209", "43210", "43211", "43212", "43213", "43214", "43215",
            "43216", "43217", "43218", "43219", "43220", "43221", "43222", "43223", "43224", "43226", "43227", "43228", "43229",
            "43230", "43231", "43232", "43234", "43235", "43236", "43240", "43251", "43260", "43266", "43268", "43270", "43271",
            "43272", "43279", "43287", "43291", "47003", "47010", "47012", "47016", "47024", "47030", "47035", "47036", "50041",
            "50227", "50420", "50427", "50431", "50441", "50452", "50475", "50633", "62812", "62819", "62822", "62825", "62836",
            "62840", "62856", "62865", "62874", "62884", "62890", "62891", "62896", "62897", "62983", "62999", "63013", "63014",
            "63015", "63037", "63039", "63055", "63056", "63060", "63061", "63068", "63069", "63072", "63073", "63077", "63079",
            "63080", "63084", "63089", "63090", "66042", "66067", "66076", "66078", "66079", "66080", "66092", "66095", "68929",
            "68932", "68939", "68947", "68960", "68972", "68981", "71219", "71230", "71243", "71249", "71295", "71324", "71336",
            "71378", "72820", "72821", "72928", "72930", "72933", "72949", "75457", "75480", "75487", "83228", "83232", "83237",
            "83263", "83283", "83286", "99301", "99302", "99326", "99330", "99335", "99343"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/franklin_county.json'); // File path for caching


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

        $this->info('Columbus - Franklin data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
