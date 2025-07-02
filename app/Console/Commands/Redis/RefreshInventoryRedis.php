<?php

namespace App\Console\Commands\Redis;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\MainInventory;
use App\Models\VehicleMake;

class RefreshInventoryRedis extends Command
{
    protected $signature = 'inventory:refresh-redis';
    protected $description = 'Refresh all inventory data in Redis';

    public function handle()
    {
        $this->info('Starting Redis data refresh...');

        // Refresh filter data
        $this->call('inventory:refresh-filters');

        // Refresh vehicle data
        $this->refreshVehicleData();

        $this->info('Redis data refresh completed!');
    }

    private function refreshVehicleData()
    {
        $startTime = microtime(true);
        $count = 0;

        if (!empty($countyKeys)) {
            Redis::del(...$countyKeys);
        }

        if (!empty($filterKeys)) {
            Redis::del(...$filterKeys);
        }


        // Process all vehicles in chunks
        MainInventory::chunk(200, function ($vehicles) use (&$count) {
            foreach ($vehicles as $vehicle) {
                $county = $this->getCountyForVehicle($vehicle);
                $countyKey = 'inventory:county:' . str_replace(' ', '_', strtolower($county));

                // Store vehicle details in hash
                Redis::hset($countyKey, $vehicle->vin, json_encode([
                    'id' => $vehicle->id,
                    'deal_id' => $vehicle->deal_id,
                    'vin' => $vehicle->vin,
                    'year' => $vehicle->year,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'price' => $vehicle->price,
                    'title' => $vehicle->title,
                    'miles' => $vehicle->miles,
                    'zip_code' => $vehicle->zip_code,
                    'latitude' => $vehicle->latitude,
                    'longitude' => $vehicle->longitude,
                    'payment_price' => $vehicle->payment_price,
                    'type' => $vehicle->type,
                    'transmission' => $vehicle->transmission,
                    'fuel' => $vehicle->fuel,
                    'body_formated' => $vehicle->body_formated,
                    'exterior_color' => $vehicle->exterior_color,
                    'interior_color' => $vehicle->interior_color,
                    'drive_info' => $vehicle->drive_info,
                    'engine_details' => $vehicle->engine_details,
                    'stock_date_formated' => $vehicle->stock_date_formated
                ]));

                // Add to county set
                Redis::sadd($countyKey . ':vins', $vehicle->vin);

                // Add to nationwide set
                Redis::sadd('inventory:nationwide', $vehicle->vin);

                // Add to filter sets
                Redis::sadd('inventory:filters:make:' . strtolower($vehicle->make), $vehicle->vin);
                Redis::sadd('inventory:filters:model:' . strtolower($vehicle->model), $vehicle->vin);
                Redis::sadd('inventory:filters:body:' . strtolower($vehicle->body_formated), $vehicle->vin);

                $count++;
            }
        });

        $this->info("Processed $count vehicles in " . round(microtime(true) - $startTime, 2) . " seconds");
    }

    private function getCountyForVehicle($vehicle)
    {
        // Implement your county lookup logic here
        // This could be from a zipcode-county mapping table
        return 'Unknown';
    }
}
