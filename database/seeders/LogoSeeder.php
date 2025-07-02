<?php

namespace Database\Seeders;

use App\Models\Logo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Logo::insert([
            [
              'image'=>'1711875856.png',
              'upload_by'=>22,
              'status'=>'1'

            ]
            ]);
    }
}
