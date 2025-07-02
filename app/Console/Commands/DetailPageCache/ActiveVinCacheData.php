<?php

namespace App\Console\Commands\DetailpageCache;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ActiveVinCacheData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'active-vin-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $chunkSize = 1000;
        $baseFilePath = storage_path('app/detail/');

        if (!file_exists($baseFilePath)) {
            mkdir($baseFilePath, 0777, true);
        }
        dd($baseFilePath);
        // $cacheFilePath = storage_path('app/zip_county_data.json');

        // Clear old cache file if it exists
        if (file_exists($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        // Load existing cache to check if a county is already cached
        $existingCache = [];
        if (file_exists($cacheFilePath)) {
            $existingCache = json_decode(file_get_contents($cacheFilePath), true) ?? [];
        }
        $cachedCounties = collect($existingCache)->pluck('county')->unique()->toArray();

        // Open the file in append mode
        $file = fopen($cacheFilePath, 'a');

        // Write an opening bracket for JSON array
        fwrite($file, '[');

        $firstChunk = true; // Flag to handle commas between records

        // Retrieve zip codes in chunks
        LocationZip::select('zip_code', 'county', 'latitude', 'longitude')
            ->chunk($chunkSize, function ($chunk) use ($file, &$firstChunk, $cachedCounties) {
                foreach ($chunk as $row) {

                    $county_json  = str_replace(' ', '_', strtolower($row['county'])) . '_county.json';
                    $cacheFilePath = storage_path('app/' . $county_json);
                    file_exists($cacheFilePath) ? $isCached = 'Yes' : $isCached = 'No';

                    // Check if county exists in the cache
                    // $isCached = in_array($row->county, $cachedCounties) ? 'yes' : 'no';

                    // Serialize each row as JSON with 'cached' field

                    $rowJson = json_encode([
                        'zip_code' => $row->zip_code,
                        'latitude'   => $row->latitude,
                        'longitude'   => $row->longitude,
                        'county'   => $row->county,
                        'cached'   => $isCached,
                    ]);

                    // Add a comma between records (except for the first one)
                    if (!$firstChunk) {
                        fwrite($file, ',');
                    } else {
                        $firstChunk = false;
                    }

                    // Write the row JSON to the file
                    fwrite($file, $rowJson);
                }
            });

        // Write a closing bracket for JSON array
        fwrite($file, ']');

        // Close the file
        fclose($file);

        $this->info('Zip code and county data cached successfully in file: ' . $cacheFilePath);



        $activeVinCache =  DB::table('main_inventories')->pluck('id', 'vin');
        dd($activeVinCache);
        //
    }
}
