<?php

namespace Database\Seeders;

use App\Models\MenuPriority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = ['1','2','3','4','5','6','7','8'];
        foreach($positions as $position)
        {
            MenuPriority::create([
                'position' => $position,
            ]);
        }
    }
}
