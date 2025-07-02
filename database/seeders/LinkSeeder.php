<?php

namespace Database\Seeders;

use App\Models\Icon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $icons = Icon::insert([
            [
                'title' => 'Facebook',
                'link' => 'https://www.facebook.com',
                'image' => 'facebook.png',
                'status' => '1'
            ],
            [
                'title' => 'Twitter',
                'link' => 'https://twitter.com',
                'image' => 'twitter.png',
                'status' => '1'
            ],
            [
                'title' => 'Linkedin',
                'link' => 'https://linkedin.com',
                'image' => 'linkedin.png',
                'status' => '1'
            ],


        ]);
    }
}
