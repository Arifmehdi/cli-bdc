<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddElPassoCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-el-paso-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the El Passo Zip Code data';

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
            "79821", "79835", "79836", "79838", "79849", "79853", "79901", "79902", "79903", "79904",
            "79905", "79906", "79907", "79908", "79910", "79911", "79912", "79913", "79914", "79916",
            "79917", "79918", "79920", "79922", "79923", "79924", "79926", "79927", "79928", "79929",
            "79930", "79931", "79932", "79934", "79935", "79936", "79937", "79938", "79940", "79941",
            "79942", "79943", "79944", "79945", "79946", "79947", "79948", "79949", "79950", "79951",
            "79952", "79953", "79954", "79955", "79958", "79960", "79961", "79968", "79976", "79978",
            "79980", "79990", "79995", "79996", "79997", "79998", "79999", "80808", "80809", "80817",
            "80819", "80831", "80832", "80833", "80840", "80841", "80864", "80808", "80809", "80817",
            "80819", "80831", "80832", "80833", "80840", "80841", "80901", "80902", "80903", "80904",
            "80905", "80906", "80907", "80908", "80909", "80910", "80911", "80912", "80913", "80914",
            "80915", "80916", "80917", "80918", "80919", "80920", "80921", "80922", "80923", "80924",
            "80925", "80926", "80927", "80928", "80929", "80930", "80931", "80932", "80933", "80934",
            "80935", "80936", "80937", "80938", "80939", "80941", "80942", "80944", "80946", "80947",
            "80949", "80950", "80960", "80962", "80970", "80977", "80995", "80997", "88510", "88511",
            "88512", "88513", "88514", "88515", "88517", "88518", "88519", "88520", "88521", "88523",
            "88524", "88525", "88526", "88527", "88528", "88529", "88530", "88531", "88532", "88533",
            "88534", "88535", "88536", "88538", "88539", "88540", "88541", "88542", "88543", "88544",
            "88545", "88546", "88547", "88548", "88549", "88550", "88553", "88554", "88555", "88556",
            "88557", "88558", "88559", "88560", "88561", "88562", "88563", "88565", "88566", "88567",
            "88568", "88569", "88570", "88571", "88572", "88573", "88574", "88575", "88576", "88577",
            "88578", "88579", "88580", "88581", "88582", "88583", "88584", "88585", "88586", "88587",
            "88588", "88589", "88590", "88595"
        ];

        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/el_paso_county.json'); // File path for caching


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

        $this->info('El Passo data cached successfully in file: ' . $cacheFilePath);
        // 78228
    }
}
