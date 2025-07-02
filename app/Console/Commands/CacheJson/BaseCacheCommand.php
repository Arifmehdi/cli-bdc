<?php

namespace App\Console\Commands\CacheJson;

use App\Models\CacheCommand;
use App\Models\MainInventory;
use Illuminate\Console\Command;

abstract class BaseCacheCommand extends Command
{
    protected $cacheCommand;

    public function handle()
    {
        $this->cacheCommand = CacheCommand::where('command', $this->signature)->first();

        if (!$this->cacheCommand) {
            $this->error('Cache command not found in database');
            return;
        }

        if (!$this->cacheCommand->status) {
            $this->error('This cache command is disabled');
            return;
        }

        $this->generateCache();
    }

    protected function generateCache()
    {
        $zipCodes = $this->cacheCommand->zip_codes_array;
        $chunkSize = 1000;
        $cacheFilePath = $this->cacheCommand->cache_file;

        // Ensure directory exists
        if (!file_exists(dirname($cacheFilePath))) {
            mkdir(dirname($cacheFilePath), 0755, true);
        }

        // Clear old cache file
        if (file_exists($cacheFilePath)) {
            unlink($cacheFilePath);
        }

        $file = fopen($cacheFilePath, 'a');
        fwrite($file, '[');

        $firstChunk = true;

        MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles',
                'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type',
                'engine_details', 'payment_price', 'exterior_color', 'interior_color', 'fuel',
                'body_formated','drive_info', 'transmission','stock_date_formated')
            ->with([
                'dealer' => function ($query) {
                    $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating',
                            'review', 'phone', 'city', 'zip', 'role_id')
                        ->addSelect('id');
                },
                'additionalInventory' => function ($query) {
                    $query->select('main_inventory_id', 'local_img_url')
                        ->addSelect('id');
                },
                'mainPriceHistory' => function ($query) {
                    $query->select('main_inventory_id', 'change_amount')
                        ->addSelect('id');
                }
            ])
            ->whereIn('zip_code', $zipCodes)
            ->chunk($chunkSize, function ($chunk, $index) use ($file, &$firstChunk) {
                foreach ($chunk as $dealer) {
                    $dealerJson = json_encode($dealer);

                    if (!$firstChunk) {
                        fwrite($file, ',');
                    } else {
                        $firstChunk = false;
                    }

                    fwrite($file, $dealerJson);
                }
            });

        fwrite($file, ']');
        fclose($file);

        $this->info($this->cacheCommand->name . ' data cached successfully in file: ' . $cacheFilePath);
    }
}
