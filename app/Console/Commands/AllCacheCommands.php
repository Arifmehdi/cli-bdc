<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AllCacheCommands extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'all-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run all specified commands sequentially';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // "add-san-diago-cache",
        $commands = [

            // // "add-dealer-cache",
            // // "add-zip-cache",
            // // "inventory:move-sold",

            // // "delete-inventory-img",

            // // add and check here 100%
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
            // "add-memphis-shelby-cache",
            // "add-nashville-davidson-cache",
            // "add-newyork-cache",
            // "add-philadelphia-cache",
            // "add-phoenix-maricopa-cache",
            // "add-sanantonio-bexar-cache",

            // "add-san-diago-cache",
            // "add-san-fransisco-cache",
            // "add-sanjose-santaclara-cache",
            // "add-seattle-king-cache",
            // "add-washington-dc-cache",

            "surrounding-cache travis",
            "surrounding-cache suffolk",
            "surrounding-cache mecklenburg",
            "surrounding-cache cook",
            "surrounding-cache franklin",
            "surrounding-cache dallas",
            "surrounding-cache denver",
            "surrounding-cache wayne",
            "surrounding-cache el_paso",
            "surrounding-cache tarrant",
            "surrounding-cache harris",
            "surrounding-cache marion",
            "surrounding-cache duval",
            "surrounding-cache los_angeles",
            "surrounding-cache shelby",
            "surrounding-cache davidson",
            "surrounding-cache new_york",
            "surrounding-cache philadelphia",
            "surrounding-cache maricopa",
            "surrounding-cache bexar",
            "surrounding-cache san_diego",
            "surrounding-cache city_and_county_of_san_francisco",
            "surrounding-cache santa_clara",
            "surrounding-cache king",
            "surrounding-cache district_of_columbia",

            // "add-austin-bastrop-cache",
            // "add-austin-burnet-cache",
            // "add-austin-blanco-cache",

            "surrounding-cache bastrop",
            "surrounding-cache burnet",
            "surrounding-cache blanco",
            "surrounding-cache hays",
            "surrounding-cache caldwell",
            "surrounding-cache williamson",

            "surrounding-cache fayette",
            "surrounding-cache bosque",
            "surrounding-cache blanco",
            "surrounding-cache ellis",
            "surrounding-cache johnson",
            "surrounding-cache limestone",
            "surrounding-cache mclennan",
            "surrounding-cache navarro",

            // Add more commands here
        ];

        // Run each command
        foreach ($commands as $command) {
            $this->info("Running command: {$command}");
            Artisan::call($command);
            $this->info("Command {$command} completed.");
        }

        $this->info('All commands have been executed successfully.');
        return 0;
    }
}
