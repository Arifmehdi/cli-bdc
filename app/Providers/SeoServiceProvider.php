<?php

namespace App\Providers;

use App\Models\GeneralSetting;
use App\Models\Seo;
use Illuminate\Support\ServiceProvider;

class SeoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //  dd(env('APP_NAME'));
        $this->app->singleton('globalSeo', function ($app) {
            $seo = Seo::where('status', 1)->latest()->first();
            $setting = GeneralSetting::first();

            $site_title = $setting->site_title;

            // $gtm = <<<EOD . $seo->gtm . EOD;
            //  dd($gtm);
            if ($seo) {
                return [
                    'name' => $seo->name,
                    'keyword' => $seo->keyword,
                    'description' => $seo->description,
                    'twitter_creator' => $seo->twitter_creator,
                    'twitter_site' => $seo->twitter_site,
                    'twitter_description' => $seo->twitter_description,
                    'twitter_title' => $seo->twitter_title,
                    'twitter_card' => $seo->twitter_card,
                    'og_locale' => $seo->og_locale,
                    'og_site_name' => $seo->og_site_name,
                    'og_type' => $seo->og_type,
                    'og_url' => $seo->og_url,
                    'og_description' => $seo->og_description,
                    'og_title' => $seo->og_title,
                    'gtm' => $seo->gtm,
                    'app_id' => $seo->app_id,
                    'twitter_image' => $seo->twitter_img,
                    'og_img' => $seo->og_img,
                    'site_title' => $site_title,
                ];
            } else {
                return [
                    'name' => null,
                    'keyword' => null,
                    'description' => null,
                    'twitter_creator' => null,
                    'twitter_site' => null,
                    'twitter_description' => null,
                    'twitter_title' => null,
                    'twitter_card' => null,
                    'og_locale' => null,
                    'og_site_name' => null,
                    'og_type' => null,
                    'og_url' => null,
                    'og_description' => null,
                    'og_title' => null,
                    'gtm' => null,
                    'app_id' => null,
                    'twitter_image' => null,
                    'og_img' => null,
                    'site_title' => null,
                ];
            }
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
