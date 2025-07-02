<?php

namespace App\Console\Commands\CacheJson;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class AddNationwideCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-nationwide-cache
                            {--csv=storage/app/detail/best_dream_car_urls_06_20.csv : Path to CSV file}
                            {--output=storage/app/nationwide_county.json : Output JSON file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the Nationwide Zip Code data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = microtime(true); // Start timing

        $csvFile = $this->option('csv') ?: storage_path('app/detail/best_dream_car_urls_06_20.csv');
        $outputFile = $this->option('output') ?: storage_path('app/nationwide_county.json');

        if (!file_exists($csvFile)) {
            $this->error("CSV file not found: {$csvFile}");
            return 1;
        }

        $urls = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $vins = [];
        $notFoundVins = [];
        $successCount = 0;

        foreach ($urls as $index => $url) {
            if ($index === 0) continue; // Skip header
            if (preg_match('/listing\/([A-Z0-9]{17})\//', $url, $matches)) {
                $vins[] = $matches[1];
            }
        }

        if (empty($vins)) {
            $this->error("No VIN numbers found in the file.");
            return 1;
        }

        $this->info("Found " . count($vins) . " VINs to process...");
        $progressBar = $this->output->createProgressBar(count($vins));
        $progressBar->setFormat("%current%/%max% [%bar%] %percent:3s%% %message%");
        $progressBar->setMessage('Starting...');
        $progressBar->start();

        // Open the output file for writing
        $file = fopen($outputFile, 'w');
        fwrite($file, '[');
        $firstRecord = true;

        foreach ($vins as $vin) {
            $progressBar->setMessage("Processing VIN: {$vin}");

            $listing = MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles', 'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type', 'engine_details', 'exterior_color', 'interior_color', 'fuel', 'body_formated', 'drive_info', 'transmission', 'stock_date_formated')
                ->with([
                    'dealer' => function ($query) {
                        $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating', 'review', 'phone', 'city', 'zip', 'role_id', 'id');
                    },
                    'additionalInventory' => function ($query) {
                        $query->select('main_inventory_id', 'local_img_url', 'id');
                    },
                    'mainPriceHistory' => function ($query) {
                        $query->select('main_inventory_id', 'change_amount', 'id');
                    }
                ])
                ->where('vin', $vin)
                ->first();

            if ($listing) {
                if (!$firstRecord) {
                    fwrite($file, ',');
                } else {
                    $firstRecord = false;
                }

                fwrite($file, json_encode($listing, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                $successCount++;
            } else {
                $notFoundVins[] = $vin;
            }

            $progressBar->advance();
        }

        fwrite($file, ']');
        fclose($file);

        $progressBar->setMessage("Completed");
        $progressBar->finish();
        $this->newLine();

        // Save not found VINs to file
        if (!empty($notFoundVins)) {
            $notFoundFile = storage_path('app/detail/not_listing_found_vins_' . date('Ymd_His') . '.txt');
            file_put_contents($notFoundFile, implode(PHP_EOL, $notFoundVins));
            $this->warn(count($notFoundVins) . " VINs not found - saved to: " . basename($notFoundFile));
        }

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime); // in seconds

        // Convert to minutes and seconds if over 60 seconds
        if ($executionTime > 60) {
            $minutes = floor($executionTime / 60);
            $seconds = round($executionTime % 60, 2);
            $timeString = "{$minutes}m {$seconds}s";
        } else {
            $timeString = round($executionTime, 2) . "s";
        }

        $this->info("Successfully processed: {$successCount} listings");
        $this->info("Not found: " . count($notFoundVins) . " VINs");
        $this->info("Execution time: " . $timeString);
        $this->info('Nationwide data cached successfully in file: ' . $outputFile);

        return 0;
    }
}
