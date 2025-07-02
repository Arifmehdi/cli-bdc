<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Menu::insert([
            [

                'name' => 'Used',
                'parent' => 0,
                'slug' => 'used',
                'position' => 'Main Menu',
                'route_url' => null,
                'param' => null,
                'created_by'=>1
            ],
            [

                'name' => 'News',
                'parent' => 0,
                'slug' => 'news',
                'position' => 'Main Menu',
                'route_url' => 'frontend.news.page',
                'param' => null,
                'created_by'=>1
            ],
            [

                'name' => 'Research',
                'parent' => 0,
                'slug' => 'research',
                'position' => 'Main Menu',
                'route_url' => null,
                'param' => null,
                'created_by'=>1
            ],
            [

                'name' => 'Used Car Listings',
                'parent' => 1,
                'slug' => 'used-car-listings',
                'position' => 'Main Menu',
                'route_url' => 'auto',
                'param' => null,
                'created_by'=>1
            ],
            [

                'name' => 'Used EVs',
                'parent' => 1,
                'slug' => 'used-evs',
                'position' => 'Main Menu',
                'route_url' => 'auto',
                'param' => 'ev',
                'created_by'=>1
            ],
            [

                'name' => 'Used SUVs',
                'parent' => 1,
                'slug' => 'used-suvs',
                'position' => 'Main Menu',
                'route_url' => 'auto',
                'param' => 'suv',
                'created_by'=>1
            ],
            [

                'name' => 'Used Trucks',
                'parent' => 1,
                'slug' => 'used-trucks',
                'position' => 'Main Menu',
                'route_url' => 'auto',
                'param' => 'truck',
                'created_by'=>1
            ],
            [

                'name' => 'Used Vans',
                'parent' => 1,
                'slug' => 'used-vans',
                'position' => 'Main Menu',
                'route_url' => 'auto',
                'param' => 'van',
                'created_by'=>1
            ],
            [

                'name' => 'Used Converibles',
                'parent' => 1,
                'slug' => 'used-converibles',
                'position' => 'Main Menu',
                'route_url' => 'auto',
                'param' => 'convertible',
                'created_by'=>1
            ],


        ]);




    }
}
