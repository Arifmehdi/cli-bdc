<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddPhoenixMaricopaCached extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-phoenix-maricopa-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Phoenix Maricopa Zip Code data';

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
            "85001", "85002", "85003", "85004", "85005", "85006", "85007", "85008", "85009", "85010",
            "85011", "85012", "85013", "85014", "85015", "85016", "85017", "85018", "85019", "85020",
            "85021", "85022", "85023", "85024", "85025", "85026", "85027", "85028", "85029", "85030",
            "85031", "85032", "85033", "85034", "85035", "85036", "85037", "85038", "85039", "85040",
            "85041", "85042", "85043", "85044", "85045", "85046", "85048", "85050", "85051", "85053",
            "85054", "85060", "85061", "85062", "85063", "85064", "85065", "85066", "85067", "85068",
            "85069", "85070", "85071", "85072", "85073", "85074", "85075", "85076", "85078", "85079",
            "85080", "85082", "85083", "85085", "85086", "85087", "85097", "85098", "85127", "85142",
            "85190", "85201", "85202", "85203", "85204", "85205", "85206", "85207", "85208", "85209",
            "85210", "85211", "85212", "85213", "85214", "85215", "85216", "85224", "85225", "85226",
            "85233", "85234", "85236", "85244", "85246", "85248", "85249", "85250", "85251", "85252",
            "85253", "85254", "85255", "85256", "85257", "85258", "85259", "85260", "85261", "85262",
            "85263", "85264", "85266", "85267", "85268", "85269", "85271", "85274", "85275", "85277",
            "85280", "85281", "85282", "85283", "85284", "85285", "85286", "85287", "85288", "85295",
            "85296", "85297", "85298", "85299", "85301", "85302", "85303", "85304", "85305", "85306",
            "85307", "85308", "85309", "85310", "85311", "85312", "85318", "85320", "85322", "85323",
            "85326", "85327", "85329", "85331", "85335", "85337", "85338", "85339", "85340", "85342",
            "85343", "85345", "85351", "85353", "85354", "85355", "85358", "85361", "85363", "85372",
            "85373", "85374", "85375", "85376", "85377", "85378", "85379", "85380", "85381", "85382",
            "85383", "85385", "85387", "85388", "85390", "85392", "85395", "85396"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/maricopa_county.json'); // File path for caching


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

        $this->info('Phoenix Maricopa data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
