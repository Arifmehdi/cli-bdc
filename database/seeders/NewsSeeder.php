<?php

namespace Database\Seeders;

use App\Models\News;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $news = News::insert([
            [
                'title' => 'Best Used Cars Under $10000 For 2023',
                'sub_title'=>'There are many great used small cars available for under $10,000. Here are some options that are reliable, fuel-efficient, and offer good value for your money',
                'description' => 'There are many great used small cars available for under $10,000. Here are some options that are reliable, fuel-efficient, and offer good value for your money',
                'img' => 'newsimage1.png',
                'status' => '1',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'title' => 'Tesla Reduces Model S And Model X Prices',
                'sub_title'=>'Tesla issued another round of price cuts last Sunday evening, reducing the price of both the Model S and Model X in the US.',
                'description' => 'Tesla issued another round of price cuts last Sunday evening, reducing the price of both the Model S and Model X in the US.',
                'img' => 'newsimage2.png',
                'status' => '1',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'title' => 'Model car $15000 For 2024',
                'sub_title'=>'Model X saw a price reduction in Canada. The interesting part of the price reductions is that the Plaid variants of the Model S and Model X are now priced.',
                'description' => 'However, only the Model X saw a price reduction in Canada. The interesting part of the price reductions is that the Plaid variants of the Model S and Model X are now priced',
                'img' => 'newsimage3.png',
                'status' => '1',
                 'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'title' => 'Used Cars Under $8000',
                'sub_title'=>'There are many great used small cars available for under $10,000. Here are some options that are reliable.',
                'description' => 'There are many great used small cars available for under $10,000. Here are some options that are reliable, fuel-efficient, and offer good value for your money',
                'img' => 'newsimage4.png',
                'status' => '1',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],
            [
                'title' => 'Beautiful car for yoy',
                'sub_title'=>'Cars available for under $10,000.',
                'description' => 'Cars available for under $10,000. Here are some options that are reliable, fuel-efficient, and offer good value for your money',
                'img' => 'newsimage5.png',
                'status' => '1',
                'user_id'=>'1',
                'created_at'=>Carbon::now()->format('Y-m-d H:i:s')
            ],

        ]);


    }
}
