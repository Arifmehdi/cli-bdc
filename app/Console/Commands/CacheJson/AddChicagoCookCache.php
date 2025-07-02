<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddChicagoCookCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-chicago-cook-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Chicago - Cook Zip Code data';

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
            "55604", "55605", "55606", "55612", "55613", "55615", "60004", "60005",
            "60006", "60007", "60008", "60009", "60016", "60017", "60018", "60019",
            "60022", "60025", "60029", "60038", "60043", "60053", "60055", "60056",
            "60062", "60065", "60067", "60068", "60070", "60074", "60076", "60077",
            "60078", "60082", "60090", "60091", "60093", "60094", "60095", "60103",
            "60104", "60107", "60130", "60131", "60141", "60153", "60154", "60155",
            "60159", "60160", "60161", "60162", "60163", "60164", "60165", "60168",
            "60171", "60173", "60176", "60179", "60193", "60194", "60195", "60196",
            "60201", "60202", "60203", "60204", "60208", "60209", "60301", "60302",
            "60303", "60304", "60305", "60402", "60406", "60409", "60411", "60412",
            "60415", "60418", "60419", "60422", "60425", "60426", "60429", "60430",
            "60438", "60443", "60445", "60452", "60453", "60454", "60455", "60456",
            "60457", "60458", "60459", "60461", "60462", "60463", "60464", "60465",
            "60466", "60467", "60469", "60471", "60472", "60473", "60475", "60476",
            "60477", "60478", "60480", "60482", "60499", "60501", "60513", "60525",
            "60526", "60534", "60546", "60558", "60601", "60602", "60603", "60604",
            "60605", "60606", "60607", "60608", "60609", "60610", "60611", "60612",
            "60613", "60614", "60615", "60616", "60617", "60618", "60619", "60620",
            "60621", "60622", "60623", "60624", "60625", "60626", "60628", "60629",
            "60630", "60631", "60632", "60633", "60634", "60636", "60637", "60638",
            "60639", "60640", "60641", "60643", "60644", "60645", "60646", "60647",
            "60649", "60651", "60652", "60653", "60654", "60655", "60656", "60657",
            "60659", "60660", "60661", "60664", "60668", "60669", "60670", "60673",
            "60674", "60675", "60678", "60680", "60681", "60684", "60685", "60687",
            "60690", "60691", "60693", "60694", "60697", "60699", "60701", "60707",
            "60712", "60714", "60803", "60804", "60805", "60827", "31620", "31627",
            "31637", "31647"

        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/cook_county.json'); // File path for caching


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

        $this->info('Chicago - Cook data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
