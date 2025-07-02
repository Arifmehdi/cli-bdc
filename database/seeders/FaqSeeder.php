<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faqs = Faq::insert([
            [
                'title' => 'What is Best Dream Car?',
                'description' => 'Best dream car is an information and technology platform that enables its users to communicate with Best dream car Certified Dealers for a great car buying experience. Our mission is simple: make the car buying process simple, fair and fun. ',

            ],
            [
                'title' => 'How do I use the Price Report?',
                'description' => 'On the dream best car Price Report, we show you what you can expect to pay on average for new cars in your area, based on what other people actually paid for their cars.',

            ],
            [
                'title' => 'What should I do if the dealer does not have my exact vehicle?',
                'description' => 'Dealers generally will do their best to match the vehicle you have configured on Best dream car, but many times they will not have an exact match for the car you are looking to purchase.',

            ],
            [
                'title' => 'Where can I find more car buying tips and advice? Is there a fee for using this service?',
                'description' => 'You can visit the localcarz company blog. Click here to read it for yourself.',

            ],
            [
                'title' => 'Is there a fee for using this service?',
                'description' => 'We do not charge you any fees for using the services. We ordinarily receive fees from our Certified Dealers in connection with the services.',

            ],

        ]);
    }
}
