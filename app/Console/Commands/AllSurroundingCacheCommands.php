<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MainInventory;
use Illuminate\Support\Facades\Config;

class AllSurroundingCacheCommands extends Command
{
    protected $countyKey;

    protected $signature = 'surrounding-cache {county : The county key (e.g., bastrop, williamson)} {--chunk=10000 : Number of records to process at a time}';

    protected $description = 'Run all specified commands sequentially';

    public function handle()
    {

        $this->countyKey = $this->argument('county');
        $config = Config::get("counties.counties.{$this->countyKey}");

        if (!$config) {
            $this->error("County configuration not found!");
            return 1;
        }

        $cacheFile = storage_path("app/{$config['file']}");

        // Clear existing file
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }

        $file = fopen($cacheFile, 'a');
        if (!$file) {
            $this->error("Failed to open cache file for writing!");
            return 1;
        }

        try {
            fwrite($file, '[');
            $firstRecord = true;

            $d = MainInventory::select(
                'id',
                'deal_id',
                'vin',
                'year',
                'make',
                'model',
                'price',
                'title',
                'miles',
                'price_rating',
                'zip_code',
                'latitude',
                'longitude',
                'payment_price',
                'type',
                'engine_details',
                'exterior_color',
                'interior_color',
                'fuel',
                'body_formated',
                'drive_info',
                'transmission',
                'stock_date_formated'
            )
            ->with([
                'dealer' => fn($q) => $q->select(
                    'dealer_id',
                    'name',
                    'state',
                    'brand_website',
                    'rating',
                    'review',
                    'phone',
                    'city',
                    'zip',
                    'role_id'
                )->addSelect('id'),
                'additionalInventory' => fn($q) => $q->select('main_inventory_id', 'local_img_url')
                    ->addSelect('id'),
                'mainPriceHistory' => fn($q) => $q->select('main_inventory_id', 'change_amount')
                    ->addSelect('id')
            ])
            ->whereIn('zip_code', $config['zips'])
            ->chunk($this->option('chunk'), function ($records) use ($file, &$firstRecord) {
                foreach ($records as $record) {
                    if (!$firstRecord) {
                        fwrite($file, ',');
                    }
                    fwrite($file, json_encode($record));
                    $firstRecord = false;
                }
            });


            fwrite($file, ']');
            $this->info("{$config['name']} data cached successfully: $cacheFile");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error caching data: " . $e->getMessage());
            return 1;
        } finally {
            fclose($file);
        }
    }

    // public function handle()
    // {
    //     $this->countyKey = $this->argument('county');
    //     $config = Config::get("counties.counties.{$this->countyKey}");

    //     if (!$config) {
    //         $this->error("County configuration not found!");
    //         return 1;
    //     }

    //     $cacheFile = storage_path("app/{$config['file']}");

    //     // Clear existing file
    //     if (file_exists($cacheFile)) {
    //         unlink($cacheFile);
    //     }

    //     $file = fopen($cacheFile, 'a');
    //     if (!$file) {
    //         $this->error("Failed to open cache file for writing!");
    //         return 1;
    //     }

    //     try {
    //         fwrite($file, '[');
    //         $firstRecord = true;

    //         MainInventory::select([
    //                 'main_inventories.id',
    //                 'main_inventories.deal_id',
    //                 'main_inventories.vin',
    //                 'main_inventories.year',
    //                 'main_inventories.make',
    //                 'main_inventories.model',
    //                 'main_inventories.price',
    //                 'main_inventories.title',
    //                 'main_inventories.miles',
    //                 'main_inventories.price_rating',
    //                 'main_inventories.zip_code',
    //                 'main_inventories.latitude',
    //                 'main_inventories.longitude',
    //                 'main_inventories.payment_price',
    //                 'main_inventories.type',
    //                 'main_inventories.engine_details',
    //                 'main_inventories.exterior_color',
    //                 'main_inventories.interior_color',
    //                 'main_inventories.fuel',
    //                 'main_inventories.body_formated',
    //                 'main_inventories.drive_info',
    //                 'main_inventories.transmission',
    //                 'main_inventories.stock_date_formated',
    //                 'admins.dealer_id',
    //                 'admins.name',
    //                 'admins.state',
    //                 'admins.brand_website',
    //                 'admins.rating',
    //                 'admins.review',
    //                 'admins.phone',
    //                 'admins.city',
    //                 'admins.zip',
    //                 'admins.role_id',
    //                 'admins.id as dealer_table_id'
    //             ])
    //             ->leftJoin('admins', 'main_inventories.deal_id', '=', 'admins.id')
    //             ->leftJoin('admins', 'main_inventories.deal_id', '=', 'admins.id')
    //             ->leftJoin('admins', 'main_inventories.deal_id', '=', 'admins.id')
    //             ->whereIn('main_inventories.zip_code', $config['zips'])
    //             ->chunk($this->option('chunk'), function ($records) use ($file, &$firstRecord) {
    //                 foreach ($records as $record) {
    //                     $formatted = [
    //                         'id' => $record->id,
    //                         'deal_id' => $record->deal_id,
    //                         'vin' => $record->vin,
    //                         'year' => $record->year,
    //                         'make' => $record->make,
    //                         'model' => $record->model,
    //                         'price' => $record->price,
    //                         'title' => $record->title,
    //                         'miles' => $record->miles,
    //                         'price_rating' => $record->price_rating,
    //                         'zip_code' => $record->zip_code,
    //                         'latitude' => $record->latitude,
    //                         'longitude' => $record->longitude,
    //                         'payment_price' => $record->payment_price,
    //                         'type' => $record->type,
    //                         'engine_details' => $record->engine_details,
    //                         'exterior_color' => $record->exterior_color,
    //                         'interior_color' => $record->interior_color,
    //                         'fuel' => $record->fuel,
    //                         'body_formated' => $record->body_formated,
    //                         'drive_info' => $record->drive_info,
    //                         'transmission' => $record->transmission,
    //                         'stock_date_formated' => $record->stock_date_formated,
    //                         'dealer' => [
    //                             'dealer_id' => $record->dealer_id,
    //                             'name' => $record->name,
    //                             'state' => $record->state,
    //                             'brand_website' => $record->brand_website,
    //                             'rating' => $record->rating,
    //                             'review' => $record->review,
    //                             'phone' => $record->phone,
    //                             'city' => $record->city,
    //                             'zip' => $record->zip,
    //                             'role_id' => $record->role_id,
    //                             'id' => $record->dealer_table_id,
    //                         ]
    //                     ];

    //                     if (!$firstRecord) {
    //                         fwrite($file, ',');
    //                     }
    //                     fwrite($file, json_encode($formatted));
    //                     $firstRecord = false;
    //                 }
    //             });

    //         fwrite($file, ']');
    //         $this->info("{$config['name']} data cached successfully: $cacheFile");
    //         return 0;
    //     } catch (\Exception $e) {
    //         $this->error("Error caching data: " . $e->getMessage());
    //         return 1;
    //     } finally {
    //         fclose($file);
    //     }
    // }
}
