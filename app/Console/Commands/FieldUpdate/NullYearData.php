<?php

namespace App\Console\Commands\FieldUpdate;

use App\Models\MainInventory;
use Illuminate\Console\Command;

class NullYearData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'year_data';

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
        $mainInventory = MainInventory::whereNull('year')->select('id', 'year', 'title', 'vin')->get();
    
        foreach ($mainInventory as $inventory) {
            $titleParts = explode(' ', $inventory->title); // Split title by spaces
            $year = $titleParts[0]; // Extract the first part (year)
    
            // Find inventory by VIN
            $updateyear = MainInventory::where('vin', $inventory->vin)->first();
    
            if ($updateyear) { // Check if record exists
                $updateyear->year = $year;
                $updateyear->save();
                dump("Updated VIN: {$updateyear->vin} with Year: {$year}");
            } else {
                dump("No inventory found for VIN: {$inventory->vin}");
            }
        }
    }
}
