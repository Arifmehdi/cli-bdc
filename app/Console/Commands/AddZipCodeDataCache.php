<?php

namespace App\Console\Commands;

use App\Models\LocationZip;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AddZipCodeDataCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-zip-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Zip Code data';

    /**
     * Execute the console command.
     */


     public function handle()
    {
        $chunkSize = 1000; // Adjust based on system capacity
        $cacheFilePath = storage_path('app/zip_county_data.json'); // File path for caching

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
        LocationZip::select('zip_code', 'county','latitude','longitude')
            ->chunk($chunkSize, function ($chunk) use ($file, &$firstChunk, $cachedCounties) {
                foreach ($chunk as $row) {

                    $county_json  = str_replace(' ','_',strtolower($row['county'])).'_county.json';
                    $cacheFilePath = storage_path('app/'.$county_json);
                    file_exists($cacheFilePath) ? $isCached = 'Yes': $isCached = 'No' ;

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
    }



    //  this use chunk and work also but need one more column cached yes or no

    //  public function handle()
    //  {
    //      $chunkSize = 5000; // Adjust based on system capacity
    //      $cacheFilePath = storage_path('app/zip_county_data.json'); // File path for caching

    //      // Clear old cache file if it exists
    //      if (file_exists($cacheFilePath)) {
    //          unlink($cacheFilePath);
    //      }

    //      // Open the file in append mode
    //      $file = fopen($cacheFilePath, 'a');

    //      // Write an opening bracket for JSON array
    //      fwrite($file, '[');

    //      $firstChunk = true; // Flag to handle commas between records

    //      // Retrieve zip codes in chunks
    //      LocationZip::select('zip_code', 'county')
    //          ->chunk($chunkSize, function ($chunk) use ($file, &$firstChunk) {
    //              foreach ($chunk as $row) {
    //                  // Serialize each row as JSON
    //                  $rowJson = json_encode(['zip_code' => $row->zip_code, 'county' => $row->county]);

    //                  // Add a comma between records (except for the first one)
    //                  if (!$firstChunk) {
    //                      fwrite($file, ',');
    //                  } else {
    //                      $firstChunk = false;
    //                  }

    //                  // Write the row JSON to the file
    //                  fwrite($file, $rowJson);
    //              }
    //          });

    //      // Write a closing bracket for JSON array
    //      fwrite($file, ']');

    //      // Close the file
    //      fclose($file);

    //      $this->info('Zip code and county data cached successfully in file: ' . $cacheFilePath);
    //  }

    //  first make it and work but not use here chunk
    // public function handle()
    // {
    //     // Clear old cache (optional)
    //     Cache::forget('cached_zipcode');

    //     // Chunk size to avoid memory overload
    //     $chunkSize = 5000; // Adjust based on system capacity

    //     // Initialize an empty array to store all ZIP codes
    //     $locationData = [];

    //     // Retrieve zip codes in chunks
    //     LocationZip::select('zip_code', 'county')
    //         ->chunk($chunkSize, function ($chunk) use (&$locationData) {
    //             foreach ($chunk as $row) {
    //                 $locationData[$row->zip_code] = $row->county;
    //             }
    //         });

    //     // Store the entire dataset in cache for 24 hours
    //     Cache::put('cached_zipcode', $locationData, now()->addDay());

    //     $this->info('Zip code data cached successfully!');
    // }
}
