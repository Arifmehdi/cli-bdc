<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\FooterContent;
use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;
use Database\Seeders\MakeSeeder;
use Database\Seeders\YearSeeder;
use Database\Seeders\ModelSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // // \App\Models\User::factory(10)->create();
        // // $this->call(NewsSeeder::class);

        $this->call(CacheCommandsTableSeeder::class);
        // $this->call(UserSeeder::class);
        // $this->call(AddSeeder::class);
        // $this->call(MenuSeeder::class);
        // $this->call(YearSeeder::class);
        // $this->call(MakeSeeder::class);
        // $this->call(ModelSeeder::class);
        //  $this->call(SliderSeeder::class);
        // $this->call(FaqSeeder::class);
        // $this->call(LinkSeeder::class);
        // $this->call(TermsConditionSeeder::class);
        // $this->call(SeoSeeder::class);
        // $this->call(FooterSeeder::class);
        // $this->call(LogoSeeder::class);
        // $this->call(SeetingSeeder::class);
        // $this->call(MenuPrioritySeeder::class);
        // $this->call(BodySeeder::class);
        // $this->call(StaticPageSeeder::class);
        // $this->call(BannerSeeder::class);
        // $this->call(MembershipSeeder::class);
    }
}
