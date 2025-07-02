<?php

namespace Database\Seeders;

use App\Models\CacheCommand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CacheCommandsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
    //     //
    // }

    public function run()
    {

        // "add-austin-travis-cache",
        // "add-boston-suffolk-cache",
        // "add-charlotte-mecklenburg-cache",
        // "add-chicago-cook-cache",
        // "add-columbus-franklin-cache",
        // "add-dallas-cache",
        // "add-denver-cache",
        // "add-detroit-wayne-cache",
        // "add-el-paso-cache",
        // "add-fortworth-tarrant-cache",
        // "add-houston-harris-cache",
        // "add-indianapolis-marion-cache",
        // "add-jacksonville-duval-cache",
        // "add-los-angeles-cache",
        // "add-memphis-volusia-cache",
        // "add-nashville-davidson-cache",
        // "add-newyork-cache",
        // "add-philadelphia-cache",


        // "add-phoenix-maricopa-cache",
        // "add-sanantonio-bexar-cache",
        // "add-san-diago-cache",
        // "add-san-fransisco-cache",
        // "add-sanjose-santaclara-cache",
        // "add-seatle-king-cache",

        // "add-washington-dc-cache"
        $commands = [
            [
                'name' => 'Austin Travis Cache',
                'command' => 'add-austin-travis-cache',
                'state' => 'TX',
                'cache_file' => storage_path('app/travis_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Boston Suffolk Cache',
                'command' => 'add-boston-suffolk-cache',
                'state' => 'MA',
                'cache_file' => storage_path('app/suffolk_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Charlotte Mecklenburg Cache',
                'command' => 'add-charlotte-mecklenburg-cache',
                'state' => 'NC',
                'cache_file' => storage_path('app/mecklenburg_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Chicage Cook Cache',
                'command' => 'add-chicago-cook-cache',
                'state' => 'IL',
                'cache_file' => storage_path('app/cook_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Columbus Franklin Cache',
                'command' => 'add-columbus-franklin-cache',
                'state' => 'OH',
                'cache_file' => storage_path('app/franklin_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Dallas Cache',
                'command' => 'add-dallas-cache',
                'state' => 'TX',
                'cache_file' => storage_path('app/dallas_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Denver Cache',
                'command' => 'add-denver-cache',
                'state' => 'CO',
                'cache_file' => storage_path('app/denver_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Detroit Wayne Cache',
                'command' => 'add-detroit-wayne-cache',
                'state' => 'MI',
                'cache_file' => storage_path('app/wayne_county.json'),
                'status' => 1
            ],
            [
                'name' => 'El Passo Cache',
                'command' => 'add-el-paso-cache',
                'state' => 'TX',
                'cache_file' => storage_path('app/el_paso_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Fortworth Tarrant Cache',
                'command' => 'add-fortworth-tarrant-cache',
                'state' => 'TX',
                'cache_file' => storage_path('app/tarrant_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Houston Harris Cache',
                'command' => 'add-houston-harris-cache',
                'state' => 'TX',
                'cache_file' => storage_path('app/harris_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Indiana Polis Cache',
                'command' => 'add-indianapolis-marion-cache',
                'state' => 'IN',
                'cache_file' => storage_path('app/marion_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Jacksonville Duval Cache',
                'command' => 'add-jacksonville-duval-cache',
                'state' => 'FL',
                'cache_file' => storage_path('app/duval_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Los Angeles Cache',
                'command' => 'add-los-angeles-cache',
                'state' => 'CA',
                'cache_file' => storage_path('app/los_angeles_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Memphis Volusia Cache',
                'command' => 'add-memphis-shelby-cache',
                'state' => 'TN',
                'cache_file' => storage_path('app/shelby_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Nashville Davidson',
                'command' => 'add-nashville-davidson-cache',
                'state' => 'TN',
                'cache_file' => storage_path('app/davidson_county.json'),
                'status' => 1
            ],
            [
                'name' => 'New York Cache',
                'command' => 'add-newyork-cache',
                'state' => 'NY',
                'cache_file' => storage_path('app/new_york_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Philadelphia Davidson',
                'command' => 'add-philadelphia-cache',
                'state' => 'PA',
                'cache_file' => storage_path('app/philadelphia_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Phoenix Maricopa Cache',
                'command' => 'add-phoenix-maricopa-cache',
                'state' => 'AZ',
                'cache_file' => storage_path('app/maricopa_county.json'),
                'status' => 1
            ],
            [
                'name' => 'San Antonio Cache',
                'command' => 'add-sanantonio-bexar-cache',
                'state' => 'TX',
                'cache_file' => storage_path('app/bexar_county.json'),
                'status' => 1
            ],
            [
                'name' => 'San Diego Cache',
                'command' => 'add-san-diago-cache',
                'state' => 'CA',
                'cache_file' => storage_path('app/san_diego_county.json'),
                'status' => 1
            ],
            [
                'name' => 'San Fransisco Cache',
                'command' => 'add-san-fransisco-cache',
                'state' => 'CA',
                'cache_file' => storage_path('app/city_and_county_of_san_francisco_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Sanjose Santaclara Cache',
                'command' => 'add-sanjose-santaclara-cache',
                'state' => 'CA',
                'cache_file' => storage_path('app/santa_clara_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Seatle King Cache',
                'command' => 'add-seatle-king-cache',
                'state' => 'WA',
                'cache_file' => storage_path('app/king_county.json'),
                'status' => 1
            ],
            [
                'name' => 'Washington DC Cache',
                'command' => 'add-washington-dc-cache',
                'state' => 'DC',
                'cache_file' => storage_path('app/district_of_columbia_county.json'),
                'status' => 1
            ],
            // Add all your commands here
        ];

        foreach ($commands as $command) {
            CacheCommand::create($command);
        }
    }
}
