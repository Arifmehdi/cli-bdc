<?php

namespace App\Console\Commands\DetailpageCache;

use App\Models\CacheCommand;
use App\Models\LocationCity;
use App\Models\LocationState;
use App\Models\MainInventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DetailCacheFromCsv extends Command
{
    protected $signature = 'active-detail-cache:generate
                            {--vin= : Process a single VIN}
                            {--all : Process all VINs from the CSV file}
                            {--csv=storage/app/detail/best_dream_car_urls_06_20.csv : Path to CSV file}';

    protected $description = 'Generate detail page cache for vehicles by VIN';

    public function handle()
    {
        // First, clean up existing cache files
        $this->cleanCacheDirectory();

        if ($this->option('vin')) {
            $this->processSingleVin($this->option('vin'));
        } elseif ($this->option('all')) {
            $this->processAllVinsFromCsv();
        } else {
            $this->error('Please specify either --vin or --all option');
            return 1;
        }

        $this->info('Cache generation completed!');
        return 0;
    }

    protected function processSingleVin($vin)
    {
        $vehicle = MainInventory::where('vin', $vin)->first();

        if (!$vehicle) {
            $this->error("Vehicle with VIN {$vin} not found");
            return;
        }

        $this->generateVinFile($vin, "{$vin}.json");
        $this->info("Generated cache file for VIN: {$vin}");
    }

    // protected function processAllVinsFromCsv()
    // {
    //     $csvFile = $this->option('csv') ?: storage_path('app/detail/best_dream_car_urls_06_20.csv');

    //     if (!file_exists($csvFile)) {
    //         $this->error("CSV file not found: {$csvFile}");
    //         return 1;
    //     }

    //     $urls = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    //     $vins = [];

    //     foreach ($urls as $index => $url) {
    //         // Skip first row (header)
    //         if ($index === 0) {
    //             continue;
    //         }

    //         if (preg_match('/listing\/([A-Z0-9]{17})\//', $url, $matches)) {
    //             $vins[] = $matches[1];
    //         }
    //     }

    //     if (empty($vins)) {
    //         $this->error("No VIN numbers found in the file.");
    //         return 1;
    //     }

    //     $this->info("Found " . count($vins) . " VINs to process...");
    //     $progressBar = $this->output->createProgressBar(count($vins));
    //     $progressBar->start();

    //     foreach ($vins as $vin) {
    //         $this->generateVinFile($vin, "{$vin}.json");
    //         $progressBar->advance();
    //     }

    //     $progressBar->finish();
    //     $this->newLine();
    //     $this->info("Generated individual JSON files for all VINs");
    // }


protected function processAllVinsFromCsv()
{
    $startTime = microtime(true); // Start timing

    $csvFile = $this->option('csv') ?: storage_path('app/detail/best_dream_car_urls_06_20.csv');

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

    foreach ($vins as $vin) {
        $progressBar->setMessage("Processing VIN: {$vin}");

        $vehicle = MainInventory::where('vin', $vin)->exists();
        if (!$vehicle) {
            $notFoundVins[] = $vin;
            $progressBar->advance();
            continue;
        }

        $this->generateVinFile($vin, "{$vin}.json");
        $successCount++;
        $progressBar->advance();
    }

    $progressBar->setMessage("Completed");
    $progressBar->finish();
    $this->newLine();

    // Save not found VINs to file
    if (!empty($notFoundVins)) {
        $notFoundFile = storage_path('app/detail/not_found_vins_' . date('Ymd_His') . '.txt');
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

    $this->info("Successfully processed: {$successCount} VINs");
    $this->info("Not found: " . count($notFoundVins) . " VINs");
    $this->info("Execution time: " . $timeString);

    return 0;
}

    protected function generateVinFile($vin, $filename)
    {
        $cacheFilePath = "detail_cache/{$filename}";

        // Get main vehicle data
        $vehicle = MainInventory::with(['additionalInventory', 'mainPriceHistory', 'dealer'])
            ->where('vin', $vin)
            ->first();

        // if (!$vehicle) {
        //     $this->error("No data found for VIN: {$vin}");
        //     return;
        // }

        if (!$vehicle) {
            return false;
        }

        // Prepare related vehicles data
        $lowerPrice = (float)$vehicle->price - 5000;
        $higherPrice = (float)$vehicle->price + 5000;

        $otherVehicles = $this->getOtherVehicles($vehicle, $lowerPrice, $higherPrice);
        $excludedVins = $otherVehicles->pluck('vin')->toArray();
        $relatedVehicles = $this->getRelatedVehicles($vehicle, $lowerPrice, $higherPrice, $excludedVins);

        // Prepare share data
        $shareData = $this->prepareShareData($vehicle);

        // Get tax rates
        $taxRates = $this->getTaxRates($vehicle);

        // Prepare the complete cache data structure
        $cacheData = [
            'vehicle' => $vehicle->toArray(),
            'related_vehicles' => $relatedVehicles->toArray(),
            'other_vehicles' => $otherVehicles->toArray(),
            'share_data' => $shareData,
            'tax_data' => $taxRates,
        ];

        // Store the cache file
        Storage::put($cacheFilePath, json_encode($cacheData, JSON_PRETTY_PRINT));
    }

    protected function prepareShareData($vehicle)
    {
        $urlId = $vehicle->year . '-' . $vehicle->make . '-' . $vehicle->model . '-in-' .
            $vehicle->dealer->city . '-' . strtoupper($vehicle->dealer->state);
        $shareUrl = url('/best-used-cars-for-sale/listing/' . $vehicle->vin . '/' . $urlId);

        return [
            'url_id' => $urlId,
            'share_url' => $shareUrl,
            'title' => $vehicle->title,
        ];
    }

    protected function getTaxRates($inventory)
    {
        $stateData = strtoupper($inventory->dealer->state);
        $stateCity = ucwords($inventory->dealer->city);

        $stateRate = optional(LocationState::where('short_name', $stateData)->first())->sales_tax ?? 0;
        $cityRate = optional(LocationCity::where('city_name', $stateCity)->first())->sales_tax ?? 0;

        return [
            'state_rate' => (float)$stateRate,
            'city_rate' => (float)$cityRate,
        ];
    }

    protected function getOtherVehicles($inventory, $lowerPrice, $higherPrice)
    {
        return MainInventory::with([
            'additionalInventory' => function ($query) {
                $query->select('id', 'main_inventory_id', 'local_img_url');
            },
            'dealer' => function ($query) {
                $query->select('id', 'name', 'city', 'state');
            }
        ])
            ->where('body_formated', $inventory->body_formated)
            ->where('id', '!=', $inventory->id)
            ->where('deal_id', $inventory->deal_id)
            ->whereBetween('price', [$lowerPrice, $higherPrice])
            ->select(
                'id',
                'deal_id',
                'title',
                'type',
                'transmission',
                'price',
                'payment_price',
                'miles',
                'price_rating',
                'vin',
                'year',
                'make',
                'model'
            )
            ->take(12)
            ->get();
    }

    protected function getRelatedVehicles($inventory, $lowerPrice, $higherPrice, $excludedVins = [])
    {
        return MainInventory::with([
            'additionalInventory' => function ($query) {
                $query->select('id', 'main_inventory_id', 'local_img_url');
            },
            'dealer' => function ($query) {
                $query->select('id', 'name', 'city', 'state', 'zip');
            }
        ])
            ->where('body_formated', $inventory->body_formated)
            ->where('id', '!=', $inventory->id)
            ->when(!empty($excludedVins), function ($query) use ($excludedVins) {
                $query->whereNotIn('vin', $excludedVins);
            })
            ->whereBetween('price', [$lowerPrice, $higherPrice])
            ->select(
                'id',
                'deal_id',
                'title',
                'type',
                'transmission',
                'price',
                'payment_price',
                'year',
                'make',
                'model',
                'miles',
                'price_rating',
                'vin'
            )
            ->take(12)
            ->get();
    }

    protected function cleanCacheDirectory()
    {
        $directory = 'detail_cache/';

        // Check if directory exists
        if (Storage::exists($directory)) {
            // Delete all files in the directory
            $files = Storage::files($directory);
            Storage::delete($files);
            $this->info('Cleared existing cache files.');
        } else {
            // Create the directory if it doesn't exist
            Storage::makeDirectory($directory);
            $this->info('Created cache directory as it didn\'t exist.');
        }
    }
}
