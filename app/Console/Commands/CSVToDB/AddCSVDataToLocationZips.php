<?php

namespace App\Console\Commands\CSVToDB;

use App\Models\CSVTmpLocation;
use App\Models\LocationCity;
use App\Models\LocationState;
use App\Models\LocationZip;
use Illuminate\Console\Command;

class AddCSVDataToLocationZips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db-to-main-location-zip {--limit=500}';

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
        $limit = $this->option('limit');
    
        $datas = CSVTmpLocation::where('import_status', 0)->limit($limit)->get();
    
        foreach ($datas as $data) {
            // Insert or get the state
            $state = LocationState::firstOrCreate(
                ['short_name' => $data->short_name],
                [
                    'state_name' => $data->state,
                    'sales_tax' => str_replace('%', '', $data->combine_tax),
                    'status' => 0,
                    'is_read' => 0
                ]
            );
    
            // Insert or get the city
            $city = LocationCity::updateOrCreate(
                [
                    'location_state_id' => $state->id,
                    'city_name' => $data->city
                ],
                [
                    'latitude' => $data->latitude,
                    'longitude' => $data->longitude,
                    'sales_tax' => str_replace('%', '', $data->combine_tax),
                    'status' => 0,
                    'is_read' => 0
                ]
            );
    
            // Insert zip data
            LocationZip::updateOrCreate(
                [
                    'location_city_id' => $city->id,
                    'zip_code' => $data->zip_code
                ],
                [
                    'county' => $data->county,
                    'latitude' => $data->latitude,
                    'longitude' => $data->longitude,
                    'sales_tax' => str_replace('%', '', $data->combine_tax),
                    'src_url' => $data->src_url,
                    'status' => 0,
                    'is_read' => 0
                ]
            );
    
            // Mark CSV entry as processed
            $data->update(['import_status' => 1]);
        }
    
        $this->info('Data inserted successfully into all tables.');
    }

}
