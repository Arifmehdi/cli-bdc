<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\News;
use App\Models\Review;
use App\Models\LatestVideo;
use Illuminate\Http\Request;

class NewsReviewController extends Controller
{
    // public function researchall()
    // {
    //     dd('Research Reviews');
    //     return view('backend.admin.news_review.index');
    // }

    public function researchall()
    {
        $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
        $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
        $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


        $slug = 'research';
        $slug_info = $slug;
        $review_id = 1;

        $datas = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();

        $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
        $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
        $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

        return view('frontend.reviews.reviewall', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    }



    public function researchLatestCarBuyingDevice()
    {
        $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
        $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
        $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


        $slug = 'car_buying_advice';
        $slug_info = $slug;
        $review_id = 1;

        $datas = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();

        $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
        $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
        $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

        return view('frontend.reviews.latestcarbuyingadvice', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    }

    public function researchToolsAndExpertAdvice()
    {

        $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
        $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
        $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


        $slug = 'tools_&_expert_advice';
        $slug_info = $slug;
        $review_id = 1;

        // switch ($slug) {
        //     case 'tools_&_expert_device':
        //         $review_id = 1;
        //         break;
        //     case 'car_buying_advice':
        //         $review_id = 2;
        //         break;
        //     case 'beyond_cars':
        //         $review_id = 3;
        //         break;
        //     default:
        //         $review_id = null; // Or some default value
        //         break;
        // }

        // dd($review_id);
        // $datas = Blog::orderByDesc('id')->where('type', $review_id)->where('status', '1')->get();
        $datas = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();

        $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
        $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
        $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

        return view('frontend.reviews.tools_and_expert_advice', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    }



    public function beyondCartoolsAndAdvice()
    {
        $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
        $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
        $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


        $slug = 'tools & advice';
        $slug_info = $slug;
        $review_id = 1;

        $datas = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();

        $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
        $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
        $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

        return view('frontend.beyond_car.beyond_car_tools_advice', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    }

}
