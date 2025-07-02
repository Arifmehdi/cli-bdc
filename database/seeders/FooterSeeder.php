<?php

namespace Database\Seeders;

use App\Models\FooterContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FooterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FooterContent::insert([
            'title' => 'ABOUT DREAMBESTCAR.COM®',
            'description' => 'Dreambestcar.com is an independent company that works side by side with consumers, sellers, and dealers for transparency and fairness in the marketplace. Dream Best Car does not have the complete history of every vehicle. Use the Dream Best Car search as one important tool, along with a vehicle inspection and test drive, to make a better decision about your next used car.',
            'copyright' => 'Copyright © 2024 <a href="javascript:void(0);" class="fs-14 text-primary">Dream Best Car</a>  All rights reserved.',
        ]);
    }
}
