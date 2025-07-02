<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        StaticPage::insert([

            [
                'title' => 'Home',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'home',
            ],
            [
                'title' => 'Autos',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'autos',
            ],
            [
                'title' => 'About Us',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'about-us',
            ],
            [
                'title' => 'Contact Us',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'contact-us',
            ],
            [
                'title' => 'FAQ',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'faq',
            ],
            [
                'title' => 'Terms and Conditions',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'terms-condition',
            ],
            [
                'title' => 'Find Dealership',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'find-dealership',
            ],
            [
                'title' => 'Search',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'search',
            ],
            [
                'title' => 'New Cars',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'new-cars',
            ],
            [
                'title' => 'Used Cars',
                'description' => 'Used car, Cars for sale for sale at Dreambestcar, your trusted used or new car in USA',
                'keyword' => 'Cars for sale,Used car,New Car,Dream best car, Dream car best, Best car, Cars, New cars for sale, used cars, pre-owned vehicles, second-hand cars, car dealership, DreamBestCar',
                'status' => '1',
                'slug' => 'used-cars',
            ],

        ]);
    }
}
