<?php

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Membership::insert([
            [
                'name' =>'Standard',
                'type' =>'Membership',
                'membership_price' =>'0',
                'status' =>'1'
            ],
            [
                'name' =>'Copper',
                'type' =>'Membership',
                'membership_price' =>'100',
                'status' =>'1'
            ],
            [
                'name' =>'Silver',
                'type' =>'Membership',
                'membership_price' =>'200',
                'status' =>'1'
            ],
            [
                'name' =>'Gold',
                'type' =>'Membership',
                'membership_price' =>'300',
                'status' =>'1'
            ],
            [
                'name' =>'Platinum',
                'type' =>'Membership',
                'membership_price' =>'400',
                'status' =>'1'
            ],
        ]);
    }
}
