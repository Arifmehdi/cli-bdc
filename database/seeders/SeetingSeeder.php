<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeetingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GeneralSetting::insert([
            [
              'site_title'=>'Dream Best Car',
              'image'=>'1711875856.png',
              'slider_image'=>'1711875856.png',
              'slider_title'=>'Find Your Dream Car Here',
              'slider_subtitle'=>'Start your car search here and find your dream best car',
              'fav_image'=>'1711875856.png',
              'site_map'=>'location.latitude',
              'email'=>'ofarid27@gmail.com',
              'phone'=>'6666 333 444',
              'pagination'=>'10',
              'separator'=>'/',
              'timezone'=>'UTC',
              'language'=>'english',
              'date_formate'=>'m/d/Y',
              'time_formate'=>'g:i a',


            ]
            ]);
    }
}
