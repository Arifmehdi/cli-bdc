<?php

namespace Database\Seeders;

use App\Models\Seo;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $gtmContainerId = 'GTM-XXXXXXX'; // Replace XXXXXXX with your actual GTM Container ID
            $gtmScript = "(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
            })(window,document,'script','dataLayer','$gtmContainerId');";

        Seo::insert([
            [
                'name' => 'New Cars, Used Cars, Car News, Car Reviews and Pricing | Dream Best Car',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'twitter_creator' => 'ofarid27@gmail.com',               
                'twitter_site' => '@DreamBestCar',
                'twitter_description' => 'Explore a wide selection of pre-owned vehicles at DreamBestCar. Find the perfect used car for your needs with our hassle-free shopping experience.',
                'twitter_title' => 'Quality Used Cars for Sale | DreamBestCar',
                'twitter_card' => 'summary_large_image',
                'og_locale' => 'en_US',
                'og_site_name' => 'DreamBestCar',
                'og_type' => 'website',
                'og_url' => 'https://www.dreambestcar.com',
                'og_description' => 'Explore a wide selection of pre-owned vehicles at DreamBestCar. Find the perfect used car for your needs with our hassle-free shopping experience.',
                'og_title' => 'Quality Used Cars for Sale | DreamBestCar',
                'gtm' => $gtmScript,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
