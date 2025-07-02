<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddIndianapolisMarionCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-indianapolis-marion-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Indianapolis - Marion Zip Code data';

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
            "29519", "29546", "29571", "29574", "29589", "29592",
            "31803", "32111", "32113", "32133", "32134", "32179",
            "32182", "32183", "32192", "32195", "32617", "32634",
            "32663", "32664", "32681", "32686", "34420", "34421",
            "34430", "34431", "34432", "34470", "34471", "34472",
            "34473", "34474", "34475", "34476", "34477", "34478",
            "34479", "34480", "34481", "34482", "34483", "34488",
            "34489", "34491", "34492", "35543", "35548", "35563",
            "35564", "35570", "35594", "37340", "37347", "37374",
            "37380", "37396", "37397", "39429", "39478", "39483",
            "39643", "40009", "40033", "40037", "40049", "40060",
            "40062", "40063", "40328", "43301", "43302", "43314",
            "43322", "43332", "43335", "43337", "43341", "43342",
            "43356", "46107", "46113", "46183", "46201", "46202",
            "46203", "46204", "46205", "46206", "46207", "46208",
            "46209", "46211", "46213", "46214", "46216", "46217",
            "46218", "46219", "46220", "46221", "46222", "46224",
            "46225", "46226", "46227", "46228", "46229", "46230",
            "46231", "46234", "46235", "46236", "46237", "46239",
            "46240", "46241", "46242", "46244", "46247", "46249",
            "46250", "46251", "46253", "46254", "46255", "46256",
            "46259", "46260", "46266", "46268", "46274", "46275",
            "46277", "46278", "46282", "46283", "46285", "46291",
            "46295", "46296", "46298", "50044", "50057", "50062",
            "50116", "50119", "50138", "50163", "50214", "50219",
            "50225", "50252", "50256", "62801", "62807", "62849",
            "62853", "62854", "62870", "62875", "62881", "62882",
            "62892", "62893", "63401", "63454", "63461", "63463",
            "63471", "66840", "66851", "66858", "66859", "66861",
            "66866", "67053", "67063", "67073", "67438", "67475",
            "67483", "72619", "72634", "72661", "72668", "72672",
            "72677", "72687", "75564", "75657", "97002", "97020",
            "97026", "97032", "97071", "97137", "97301", "97302",
            "97303", "97305", "97306", "97307", "97308", "97309",
            "97310", "97311", "97312", "97313", "97314", "97317",
            "97325", "97342", "97346", "97350", "97352", "97362",
            "97373", "97375", "97381", "97383", "97384", "97385",
            "97392", "26554", "26555", "26559", "26560", "26563",
            "26566", "26570", "26571", "26572", "26574", "26576",
            "26578", "26582", "26585", "26586", "26587", "26588",
            "26591"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/marion_county.json'); // File path for caching


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

        $this->info('Indianapolis - Marion data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
