<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Slider::insert([
            [
                'title' => 'Find The Best Cars Buy & Sell Near By You',
                'sub_title' => 'It is a long established fact that a reader will be distracted by the when looking at its layout.',
                'image' => 'slider_images/0101.jpg',
                'status' => '1',
                'added_by' => '1',
            ],

        ]);
    }
}
