<?php

namespace Database\Seeders;

use App\Models\Banner;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = Banner::insert([

            [
                'name' => 'top Banner',
                'image' => '1245781245.png',
                'description' => 'best top banner',
                'position' => 'auto page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'middle Banner',
                'image' => '1245781240.png',
                'description' => 'best middle banner',
                'position' => 'auto page middle',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],

            [
                'name' => 'top Banner',
                'image' => '5245781240.png',
                'description' => 'best top banner',
                'position' => 'home page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'bottom Banner',
                'image' => '9245781240.png',
                'description' => 'best top banner',
                'position' => 'home page bottom',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'top Banner',
                'image' => '3245781240.png',
                'description' => 'best top banner',
                'position' => 'auto details page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'top Banner',
                'image' => '6245781240.png',
                'description' => 'best top banner',
                'position' => 'news page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'top Banner',
                'image' => '2545781240.png',
                'description' => 'best top banner',
                'position' => 'contact page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],

            [
                'name' => 'top Banner',
                'image' => '1245789240.png',
                'description' => 'best top banner',
                'position' => 'about page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'top Banner',
                'image' => '1245789240.png',
                'description' => 'best top banner',
                'position' => 'faq page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],

            [
                'name' => 'top Banner',
                'image' => '1245381240.png',
                'description' => 'best top banner',
                'position' => 'terms condition page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'top Banner',
                'image' => '1245381241.png',
                'description' => 'Used top banner',
                'position' => 'cars for sale page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'name' => 'top Banner',
                'image' => '1245381242.png',
                'description' => 'New top banner',
                'position' => 'new cars search page top',
                'status' => '1',
                'renew' => 'yes',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],



        ]);
    }
}
