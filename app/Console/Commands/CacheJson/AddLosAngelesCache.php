<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddLosAngelesCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-los-angeles-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Los Angeles Zip Code data';

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
            "90210", "90002", "90003", "90004", "90006", "90012", "90017", "90018", "90019",
            "90020", "90024", "90025", "90026", "90027", "90029", "90031", "90034", 
            "90035", "90036", "90038", "90039", "90041", "90045", "90046", "90048", 
            "90049", "90057", "90064", "90067", "90071", "90212", "90230", "90277", 
            "90292", "90401", "90403", "90404", "90405", "90503", "90802", "90804", 
            "91006", "91105", "91107", "91505", "90001", "90005", "90007", "90008", 
            "90009", "90010", "90011", "90013", "90014", "90015", "90016", "90019", 
            "90021", "90022", "90023", "90028", "90030", "90032", "90033", "90037", 
            "90040", "90042", "90043", "90044", "90047", "90050", "90051", "90052", 
            "90053", "90054", "90055", "90056", "90058", "90059", "90060", "90061", 
            "90062", "90063", "90065", "90066", "90068", "90069", "90070", "90072", 
            "90073", "90074", "90075", "90076", "90077", "90079", "90080", "90081", 
            "90082", "90083", "90084", "90086", "90087", "90088", "90089", "90091", 
            "90093", "90095", "90096", "90134", "90209", "90211", "90213", "90220", 
            "90221", "90223", "90224", "90231", "90232", "90233", "90239", "90240", 
            "90241", "90242", "90245", "90247", "90248", "90249", "90250", "90251", 
            "90254", "90255", "90260", "90261", "90262", "90263", "90264", "90265", 
            "90266", "90267", "90270", "90272", "90274", "90275", "90278", "90280", 
            "90290", "90291", "90293", "90294", "90295", "90296", "90301", "90302", 
            "90303", "90304", "90305", "90306", "90307", "90308", "90309", "90310", 
            "90311", "90312", "90402", "90406", "90407", "90408", "90409", "90410", 
            "90411", "90501", "90502", "90504", "90505", "90506", "90507", "90508", 
            "90509", "90510", "90601", "90602", "90603", "90604", "90605", "90606", 
            "90607", "90608", "90609", "90610", "90637", "90638", "90639", "90640", 
            "90650", "90651", "90652", "90660", "90661", "90662", "90670", "90702"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/los_angeles_county.json'); // File path for caching


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

        $this->info('Los Angeles data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
