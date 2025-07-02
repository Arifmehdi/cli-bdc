<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class zipCodePluckByApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'add-zip-pluck-api';
    // The name and signature of the console command
    protected $signature = 'zipcodes:get {zipcode} {minimumradius=0} {maximumradius=100}';


    /**
     * The console command description.
     *
     * @var string
     */
    // The console command description
    protected $description = 'Fetch zip codes in a given radius from a specific zip code';

    /**
     * Execute the console command.
     */

    // Execute the console command
    public function handle()
    {
        $zipcode = $this->argument('zipcode');  // Zip code passed to the command
        $minimumRadius = $this->argument('minimumradius');  // Minimum radius
        $maximumRadius = $this->argument('maximumradius');  // Maximum radius
        $apiKey = 'DEMOAPIKEY';  // Replace with your actual API key
        $fileName = 'boston.txt';  // File name option

        // Making the API request
        $response = Http::get("https://api.zip-codes.com/ZipCodesAPI.svc/1.0/FindZipCodesInRadius", [
            'zipcode' => $zipcode,
            'minimumradius' => $minimumRadius,
            'maximumradius' => $maximumRadius,
            'country' => 'ALL',
            'key' => $apiKey,
        ]);

        // Check if the request was successful
        if ($response->successful()) {
            // Decode the JSON response
            $data = $response->json();

            // Extract only the zip codes (Code) from the DataList
            $zipCodes = collect($data['DataList'])->pluck('Code')->toArray();

            // If zip codes were found, write them to a file
            if (count($zipCodes) > 0) {
                // Convert the array of zip codes to the desired format
                $zipCodesString = json_encode($zipCodes);

                // Write to a text file
                File::put(storage_path("app/{$fileName}"), $zipCodesString);

                $this->info("Zip codes saved to {$fileName}");
            } else {
                $this->info("No zip codes found within the specified radius.");
            }
        } else {
            // Handle error (API request failed)
            $this->error('Unable to fetch data. Please check the API or your internet connection.');
        }
    }
}
