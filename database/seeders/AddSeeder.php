<?php

namespace Database\Seeders;

use App\Models\Advertisement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = Advertisement::insert([
            [
                'title' => 'Do You Want to buy A Car?',
                'description' => 'Dream Best Car helps you sell your car without charging any fees for using our services.',
                'status'=>"1",

            ],
            [
                'title' => 'Are You Looking For A Car?',
                'description' => 'Dream Best Car helps you buy your car without charging any fees for using our services.',
                'status'=>"1",

            ],

        ]);
    }
}
