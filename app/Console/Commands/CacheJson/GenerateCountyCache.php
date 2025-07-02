<?php

namespace App\Console\Commands\CacheJson;

use App\Models\CacheCommand;
use App\Models\MainInventory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class GenerateCountyCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:generate-county {id? : The ID of the cache command}';

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
        $id = $this->argument('id');
        $query = CacheCommand::query();


        if ($id) {
            $query->where('id', $id);
        }

        $cacheCommands = $query->get();

        foreach ($cacheCommands as $cache) {
            $this->processCacheCommand($cache);
        }

        $this->info('Cache generation completed!');
    }

    protected function processCacheCommand(CacheCommand $cache)
    {
        $zipCodes = json_decode($cache->zip_codes, true) ?: [];

        if (empty($zipCodes)) {
            $this->error("No zip codes found for cache command ID: {$cache->id}");
            return;
        }

        // Generate filename based on county
        $fileName = strtolower($cache->county) . '_county.json';

        $cacheFilePath = $fileName;

        // Clear old cache file if it exists
        if (Storage::exists($cacheFilePath)) {
            Storage::delete($cacheFilePath);
        }

        // Open file for writing
        $file = Storage::append($cacheFilePath, '[');

        $firstChunk = true;
        $chunkSize = 1000;

        // MainInventory::select('id', 'deal_id', 'vin', 'year', 'make', 'model', 'price', 'title', 'miles',
        //         'price_rating', 'zip_code', 'latitude', 'longitude', 'payment_price', 'type',
        //         'engine_details', 'exterior_color', 'interior_color', 'fuel', 'body_formated',
        //         'drive_info', 'transmission', 'stock_date_formated')
        //     ->with([
        //         'dealer' => function ($query) {
        //             $query->select('dealer_id', 'name', 'state', 'brand_website', 'rating',
        //                     'review', 'phone', 'city', 'zip', 'role_id');
        //         },
        //         'additionalInventory' => function ($query) {
        //             $query->select('main_inventory_id', 'local_img_url');
        //         },
        //         'mainPriceHistory' => function ($query) {
        //             $query->select('main_inventory_id', 'change_amount');
        //         }
        //     ])
        //     ->whereIn('zip_code', $zipCodes)
        //     ->chunk($chunkSize, function ($chunk) use ($cacheFilePath, &$firstChunk) {
        //         $data = [];
        //         foreach ($chunk as $dealer) {
        //             if (!$firstChunk) {
        //                 Storage::append($cacheFilePath, ',');
        //             }
        //             $firstChunk = false;
        //             Storage::append($cacheFilePath, json_encode($dealer));
        //         }
        //     });

        DB::table('main_inventories as mi')
            ->select(
                'mi.id',
                'mi.deal_id',
                'mi.vin',
                'mi.year',
                'mi.make',
                'mi.model',
                'mi.price',
                'mi.title',
                'mi.miles',
                'mi.price_rating',
                'mi.zip_code',
                'mi.latitude',
                'mi.longitude',
                'mi.payment_price',
                'mi.type',
                'mi.engine_details',
                'mi.exterior_color',
                'mi.interior_color',
                'mi.fuel',
                'mi.body_formated',
                'mi.drive_info',
                'mi.transmission',
                'mi.stock_date_formated',
                'd.id as dealer_id',
                'd.name as dealer_name',
                'd.state',
                'd.brand_website',
                'd.rating',
                'd.review',
                'd.phone',
                'd.city',
                'd.zip',
                'd.role_id',
                'ai.id as additional_inventory_id',
                'ai.local_img_url',
                'mph.change_amount'
            )
            ->leftJoin('dealers as d', 'mi.deal_id', '=', 'd.id')
            ->leftJoin('additional_inventories as ai', 'mi.id', '=', 'ai.main_inventory_id')
            ->leftJoin('main_price_history as mph', 'mi.id', '=', 'mph.main_inventory_id')
            ->whereIn('mi.zip_code', $zipCodes)
            ->orderBy('mi.id')
            ->chunk($chunkSize, function ($chunk) use ($cacheFilePath, &$firstChunk) {
                $groupedData = [];

                foreach ($chunk as $row) {
                    if (!isset($groupedData[$row->id])) {
                        $groupedData[$row->id] = [
                            'main' => $row,
                            'dealer' => null,
                            'additional_images' => [],
                            'additional_inventory_ids' => [],
                            'price_history' => []
                        ];

                        // Set dealer data
                        if ($row->dealer_id) {
                            $groupedData[$row->id]['dealer'] = (object)[
                                'dealer_id' => $row->dealer_id,
                                'name' => $row->dealer_name,
                                'state' => $row->state,
                                'brand_website' => $row->brand_website,
                                'rating' => $row->rating,
                                'review' => $row->review,
                                'phone' => $row->phone,
                                'city' => $row->city,
                                'zip' => $row->zip,
                                'role_id' => $row->role_id
                            ];
                        }
                    }

                    // Collect additional images and IDs
                    if ($row->local_img_url && !in_array($row->local_img_url, $groupedData[$row->id]['additional_images'])) {
                        $groupedData[$row->id]['additional_images'][] = $row->local_img_url;
                        if ($row->additional_inventory_id) {
                            $groupedData[$row->id]['additional_inventory_ids'][] = $row->additional_inventory_id;
                        }
                    }

                    // Collect price history
                    if ($row->change_amount && !in_array($row->change_amount, $groupedData[$row->id]['price_history'])) {
                        $groupedData[$row->id]['price_history'][] = $row->change_amount;
                    }
                }

                foreach ($groupedData as $item) {
                    // Format additional inventory as single object with all images
                    $additionalInventory = null;
                    if (!empty($item['additional_images'])) {
                        $additionalInventory = (object)[
                            'main_inventory_id' => $item['main']->id,
                            'local_img_url' => implode(',', $item['additional_images']),
                            'id' => !empty($item['additional_inventory_ids'])
                                ? min($item['additional_inventory_ids'])
                                : $item['main']->id
                        ];
                    }

                    $result = [
                        'id' => $item['main']->id,
                        'deal_id' => $item['main']->deal_id,
                        'vin' => $item['main']->vin,
                        'year' => $item['main']->year,
                        'make' => $item['main']->make,
                        'model' => $item['main']->model,
                        'price' => $item['main']->price,
                        'title' => $item['main']->title,
                        'miles' => $item['main']->miles,
                        'price_rating' => $item['main']->price_rating,
                        'zip_code' => $item['main']->zip_code,
                        'latitude' => $item['main']->latitude,
                        'longitude' => $item['main']->longitude,
                        'payment_price' => $item['main']->payment_price,
                        'type' => $item['main']->type,
                        'engine_details' => $item['main']->engine_details,
                        'exterior_color' => $item['main']->exterior_color,
                        'interior_color' => $item['main']->interior_color,
                        'fuel' => $item['main']->fuel,
                        'body_formated' => $item['main']->body_formated,
                        'drive_info' => $item['main']->drive_info,
                        'transmission' => $item['main']->transmission,
                        'stock_date_formated' => $item['main']->stock_date_formated,
                        'dealer' => $item['dealer'],
                        'additional_inventory' => $additionalInventory,
                        'main_price_history' => array_map(function ($amount) {
                            return ['change_amount' => $amount];
                        }, $item['price_history'])
                    ];

                    if (!$firstChunk) {
                        Storage::append($cacheFilePath, ',');
                    }
                    $firstChunk = false;
                    Storage::append($cacheFilePath, json_encode($result));
                }
            });

        Storage::append($cacheFilePath, ']');

        // Update cache_command record with the new file path
        $cache->update(['cache_file' => $cacheFilePath]);

        $this->info("Generated cache file for {$cache->county} at {$cacheFilePath}");
    }
}
