<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddDenverCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-denver-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Denver Zip Code data';

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
            "36701", "36702", "36703", "36758", "36759", "36761", "36767", "36773", "36775", "36785",
            "50003", "50038", "50039", "50063", "50066", "50069", "50070", "50109", "50146", "50167",
            "50220", "50233", "50261", "50263", "50276", "65590", "65622", "65685", "65764", "65767",
            "65783", "71725", "71742", "71748", "71763", "75001", "75006", "75011", "75014", "75015",
            "75016", "75017", "75019", "75030", "75038", "75039", "75040", "75041", "75042", "75043",
            "75044", "75045", "75046", "75047", "75048", "75049", "75050", "75051", "75052", "75053",
            "75054", "75059", "75060", "75061", "75062", "75063", "75064", "75080", "75081", "75082",
            "75083", "75085", "75088", "75089", "75098", "75099", "75104", "75106", "75115", "75116",
            "75123", "75134", "75137", "75138", "75141", "75146", "75149", "75150", "75159", "75172",
            "75180", "75181", "75182", "75185", "75187", "75201", "75202", "75203", "75204", "75205",
            "75206", "75207", "75208", "75209", "75210", "75211", "75212", "75214", "75215", "75216",
            "75217", "75218", "75219", "75220", "75221", "75222", "75223", "75224", "75225", "75226",
            "75227", "75228", "75229", "75230", "75231", "75232", "75233", "75234", "75235", "75236",
            "75237", "75238", "75240", "75241", "75242", "75243", "75244", "75246", "75247", "75248",
            "75249", "75250", "75251", "75252", "75253", "75254", "75260", "75261", "75262", "75263",
            "75264", "75265", "75266", "75267", "75270", "75275", "75277", "75283", "75284", "75285",
            "75287", "75301", "75303", "75312", "75313", "75315", "75320", "75326", "75336", "75339",
            "75342", "75354", "75355", "75356", "75357", "75358", "75359", "75360", "75367", "75368",
            "75370", "75371", "75372", "75373", "75374", "75376", "75378", "75379", "75380", "75381",
            "75382", "75389", "75390", "75391", "75392", "75393", "75394", "75395", "75397", "75398",
            "80201", "80202", "80203", "80204", "80205", "80206", "80207", "80208", "80209", "80210",
            "80211", "80212", "80216", "80217", "80218", "80219", "80220", "80222", "80223", "80224",
            "80227", "80230", "80231", "80235", "80236", "80237", "80238", "80239", "80243", "80244",
            "80246", "80247", "80248", "80249", "80250", "80251", "80252", "80256", "80257", "80259",
            "80261", "80262", "80263", "80264", "80265", "80266", "80271", "80273", "80274", "80281",
            "80290", "80291", "80293", "80294", "80299"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/denver_county.json'); // File path for caching


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

        $this->info('Denver data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
