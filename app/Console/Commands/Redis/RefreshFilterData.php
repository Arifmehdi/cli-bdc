<?php

namespace App\Console\Commands\Redis;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use App\Models\MainInventory;
use App\Models\VehicleMake;

class RefreshFilterData extends Command
{
    protected $signature = 'inventory:refresh-filters';
    protected $description = 'Refresh filter data in Redis';

    public function handle()
    {
        $startTime = microtime(true);

        // Store makes
        $makes = VehicleMake::orderBy('make_name')->where('status', 1)->pluck('make_name');
        Redis::set('inventory:filters:makes', json_encode($makes));

        // Store body types
        $bodyTypes = MainInventory::distinct()->pluck('body_formated')->toArray();
        Redis::set('inventory:filters:body_types', json_encode($bodyTypes));

        // Store fuel types
        $fuelTypes = MainInventory::distinct()->whereNotNull('fuel')->pluck('fuel')->toArray();
        sort($fuelTypes);
        Redis::set('inventory:filters:fuel_types', json_encode($fuelTypes));

        // Store price range
        $priceRange = [
            'max' => (int)MainInventory::where('price', '!=', 'N/A')->max('price'),
            'min' => (int)MainInventory::where('price', '!=', 'N/A')->min('price')
        ];
        Redis::set('inventory:filters:price_range', json_encode($priceRange));

        // Store mileage range
        $mileageRange = [
            'max' => MainInventory::where('miles', '!=', 'N/A')->max('miles'),
            'min' => MainInventory::where('miles', '!=', 'N/A')->min('miles')
        ];
        Redis::set('inventory:filters:mileage_range', json_encode($mileageRange));

        $this->info('Filter data refreshed in ' . round(microtime(true) - $startTime, 2) . ' seconds');
    }
}
