<?php

namespace App\Console\Commands;

use App\Models\Dealer;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AddDealerDataCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dealer-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the dealers data';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $chunkSize = 1000; // Adjust as needed
        $cacheFilePath = storage_path('app/cached_dealers.json'); // File path for caching

        // Clear old cache file if it exists
        if (file_exists($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        // Open the file in append mode
        $file = fopen($cacheFilePath, 'a');

        // Write an opening bracket for JSON array
        fwrite($file, '[');

        $firstChunk = true; // Flag to handle commas between records

        // User::with(['roles', 'mainInventories'])
        //     ->where('status', 1)
        //     ->whereHas('roles', function ($query) {
        //         $query->where('name', 'dealer');
        //     })
        //     ->select('id', 'dealer_id', 'name', 'phone', 'address', 'city', 'state', 'zip', 'brand_website')
        //     ->distinct()
        //     ->orderByRaw("
        //         CASE
        //             WHEN name REGEXP '^[0-9]' THEN 1
        //             ELSE 0
        //         END,
        //         name ASC
        //     ")

        Dealer::with([
            // 'roles' => function ($query) {
            //     $query->select('id', 'name'); // Select specific columns from the roles table
            // },
            'mainInventories' => function ($query) {
                $query->select('id', 'deal_id', 'inventory_status', 'vin')
                    ->where('inventory_status', '!=', "Sold")
                    ->orWhereNull('inventory_status');; // Select specific columns from the mainInventories table
            }
        ])
            ->where('status', 1)
            ->where('import_type', '!=', 0)
            // ->whereHas('roles', function ($query) {
            //     $query->where('name', 'dealer');
            // })
            ->whereHas('mainInventories', function ($query) {
                $query->where('inventory_status', '!=', "Sold")
                    ->orWhereNull('inventory_status');
            })
            ->select('id', 'dealer_id', 'name', 'phone', 'address', 'city', 'state', 'zip', 'brand_website')
            ->distinct()
            ->orderByRaw("
                CASE
                    WHEN name REGEXP '^[0-9]' THEN 1
                    ELSE 0
                END,
                name ASC
            ")
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

        // Write a closing bracket for JSON array
        fwrite($file, ']');

        // Close the file
        fclose($file);

        $this->info('Dealers data cached successfully in file: ' . $cacheFilePath);
    }


    // public function handle()
    // {
    //     $query = User::with(['roles', 'mainInventories'])
    //         ->where('status', 1)
    //         ->whereHas('roles', function ($query) {
    //             $query->where('name', 'dealer');
    //         })
    //         ->select('id', 'dealer_id', 'name', 'phone', 'address', 'city', 'state', 'zip', 'brand_website')
    //         ->distinct()
    //         ->orderByRaw("
    //         CASE
    //             WHEN name REGEXP '^[0-9]' THEN 1
    //             ELSE 0
    //         END,
    //         name ASC
    //     ");

    //     // Get all dealers (without pagination)
    //     $dealers = $query->get();

    //     // Cache the entire dataset for 60 minutes (adjust as needed)
    //     Cache::put('cached_dealers', $dealers, now()->addMinutes(60));

    //     $this->info('Dealers data cached successfully!');
    // }

}
