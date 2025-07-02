<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Blog;
use App\Models\News;
use App\Models\Tips;
use App\Models\Review;
use App\Models\LatestVideo;
use Illuminate\Http\Request;

class BeyondCarBlogController extends Controller
{
    public function beyondCar()
    {
        $news = Blog::where('sub_category_id', 8)->orderByDesc('id')->where('status', '1')->get();
        $innovation = Blog::where('sub_category_id', 9)->orderByDesc('id')->where('status', '1')->get();
        $opinion = Blog::where('sub_category_id', 10)->orderByDesc('id')->where('status', '1')->get();
        $opinion = Blog::where('sub_category_id', 10)->orderByDesc('id')->where('status', '1')->get();
        $financial = Blog::where('sub_category_id', 11)->orderByDesc('id')->where('status', '1')->get();
        // $tips = Tips::orderByDesc('id')->where('status', '1')->get();
        // $sels = Blog::where('type', 'Selling your car')->where('status', '1')->get();
        // $shops = Blog::where('type', 'Shopping & negotiating')->where('status', '1')->get();
        // $owners = Blog::where('type', 'Ownership & maintenance')->where('status', '1')->get();
        $owners = Blog::where('type', 3)->orderByDesc('id')->where('status', '1')->get();
        $faqs = Faq::where('status', '1')->where('type', 'research')->get();

        return view('frontend.beyond_car.beyond_car', compact('news', 'innovation', 'opinion', 'owners', 'faqs','financial'));
    }

    public function beyondCarFinancial()
    {
        $slug = ucwords('financial');
        $slug_info = $slug;
        $datas = Blog::orderByDesc('id')->where('sub_category_id', 11)->where('status', '1')->get();
        return view('frontend.beyond_car.beyond_car_financial', compact('datas', 'slug','slug_info'));
    }


    public function beyondCarNews()
    {
        $slug = ucwords('news');
        $slug_info = $slug;
        $datas = Blog::orderByDesc('id')->where('sub_category_id', 8)->where('status', '1')->get();
        return view('frontend.beyond_car.beyond_car_news', compact('datas', 'slug'));
    }


    public function beyondCarOpinion()
    {
        $slug = ucwords('opinion');
        $slug_info = $slug;
        $datas = Blog::orderByDesc('id')->where('sub_category_id', 10)->where('status', '1')->get();
        return view('frontend.beyond_car.beyond_car_opinion_tips', compact('datas', 'slug'));
    }


    public function beyondCarInnovation()
    {
        $slug = ucwords('innovation');
        $slug_info = $slug;
        $datas = Blog::orderByDesc('id')->where('sub_category_id', 9)->where('status', '1')->get();
        return view('frontend.beyond_car.beyond_car_innovation', compact('datas', 'slug'));
    }


    // public function beyondCarFinancial()
    // {
    //     $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


    //     $slug = ucwords('financial');
    //     $slug_info = $slug;
    //     $review_id = 1;

    //     $datas = Blog::orderByDesc('id')->where('type', 0)->where('status', '1')->get();

    //     $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
    //     $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
    //     $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

    //     return view('frontend.beyond_car.beyond_car_financial', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    // }


    // public function beyondCarNews()
    // {
    //     $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


    //     $slug = 'News';
    //     $slug_info = $slug;
    //     $review_id = 1;

    //     $datas = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();
    //     $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
    //     $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
    //     $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

    //     return view('frontend.beyond_car.beyond_car_news', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    // }




    // public function beyondCarOpinion()
    // {
    //     $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


    //     $slug = ucwords('opinion');
    //     $slug_info = $slug;
    //     $review_id = 1;

    //     $datas = Blog::orderByDesc('id')->where('type', 0)->where('status', '1')->get();

    //     $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
    //     $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
    //     $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

    //     return view('frontend.beyond_car.beyond_car_opinion_tips', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    // }



    // public function beyondCarInnovation()
    // {
    //     $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


    //     $slug = ucwords('innovation');
    //     $slug_info = $slug;
    //     $review_id = 1;

    //     $datas = Blog::orderByDesc('id')->where('type', 0)->where('status', '1')->get();

    //     $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
    //     $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
    //     $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

    //     return view('frontend.beyond_car.beyond_car_innovation', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    // }
}
