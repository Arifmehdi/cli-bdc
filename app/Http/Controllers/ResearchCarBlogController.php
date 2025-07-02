<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\News;
use App\Models\Review;
use App\Models\Faq;
use App\Models\LatestVideo;
use App\Models\Tips;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class ResearchCarBlogController extends Controller
{


    public function reseacrhReviewArticleDetails(Request $request, string $categorySlug, string $slug)
    {
        // if($categorySlug == 'auto-news'){
        //     try {
        //         $data = News::where('slug', $slug)->firstOrFail();

        //         $news = News::whereNot('slug', $slug)->orderBy('created_at', 'desc')->get();
        //         return view('frontend.news.news_details', compact('data', 'news'));
        //     } catch (ModelNotFoundException $e) {
        //         abort(404, 'News article not found.');
        //     }
        // }elseif($categorySlug == 'car-tips'){

        //     try {
        //         $data = Tips::where('slug', $slug)->firstOrFail();
        //         $news = Tips::whereNot('slug', $slug)->orderBy('created_at', 'desc')->get();
        //         return view('frontend.news.tips_details', compact('data', 'news'));
        //     } catch (ModelNotFoundException $e) {
        //         abort(404, 'News article not found.');
        //     }
        // }
            $segment = $request->segment(1);
            if($segment == 'research-review'){
                $segmentData = 'Research';
                $urlData = 'frontend.research.review';
            }
            if($segment == 'beyond-car'){
                $segmentData = 'Beyond Car';
                $urlData = 'frontend.beyondcar';
            }

            $encodedSlug = urlencode($slug);
            // $art = Blog::where('slug', 'LIKE', "%{$encodedSlug}%")->first();
            $art = Blog::where('slug',  $slug)->first();

            // $art = Blog::where('slug',$slug)->first();
            // dd($art, $slug, $encodedSlug);
            $artId = $art->id;
            $category = str_replace(['-', '&amp;'], ' ', $categorySlug);
            $category = ucwords($category);
            $rels = Blog::whereNot('id', $artId)->where('type',$art->type)->limit(6)->get();

            return view('frontend.article-details', compact('art', 'rels', 'category','segmentData','urlData'));


    }


    public function research()
    {
        $news = News::with('user')->orderByDesc('id')->where('status', '1')->get();
        $tips = Tips::orderByDesc('id')->where('status', '1')->get();
        // $sels = Blog::where('type', 'Selling your car')->where('status', '1')->get();
        // $shops = Blog::where('type', 'Shopping & negotiating')->where('status', '1')->get();
        // $owners = Blog::where('type', 'Ownership & maintenance')->where('status', '1')->get();
        $sels = Blog::where('type', 1)->orderByDesc('id')->where('status', '1')->get();
        $shops = Blog::where('type', 2)->orderByDesc('id')->where('status', '1')->get();
        $owners = Blog::where('type', 3)->orderByDesc('id')->where('status', '1')->get();
        $faqs = Faq::where('status', '1')->where('type', 'research')->get();

        return view('frontend.research.autoresearch', compact('news', 'sels', 'shops', 'owners', 'faqs','tips'));
        return view('frontend.research', compact('news', 'sels', 'shops', 'owners', 'faqs','tips'));
    }


    public function researchVideos()
    {
        $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
        $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
        $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


        $slug = 'videos';
        $slug_info = $slug;
        $review_id = 1;

        $datas = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();

        $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
        $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
        $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

        return view('frontend.research.reviewvideos', compact('videos','datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
        return view('frontend.reviews.reviewvideos', compact('datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    }

    public function researchCarTips()
    {
        $slug = ucwords('car tips');
        $slug_info = $slug;
        $datas = Blog::orderByDesc('id')->where('sub_category_id', 5)->where('status', '1')->get();
        return view('frontend.research.cartips', compact('datas', 'slug'));
    }

    public function faq()
    {
        $slug = 'faq';
        $slug_info = $slug;
        $faqs = Faq::where('status', '1')->where('type', 'faq')->get();
        return view('frontend.faq.index', compact('faqs', 'slug'));
    }

    public function researchCarBuyingAdvice()
    {
        $slug = ucwords('car buying advice');
        $slug_info = $slug;
        $datas = Blog::orderByDesc('id')->where('sub_category_id', 4)->where('status', '1')->get();
        return view('frontend.research.carbuyingadvice', compact('datas', 'slug'));
    }

    public function researchToolsAndAdvice()
    {
        $slug = ucwords('Tools and Advice');
        $slug_info = $slug;
        $datas = Blog::orderByDesc('id')->where('sub_category_id', 3)->where('status', '1')->get();
        return view('frontend.research.toolsandexpertadvice', compact('datas', 'slug'));
    }

    public function autonews()
    {
        $slug = ucwords('Auto News');
        $slug_info = $slug;
        $datas = Blog::orderByDesc('id')->where('sub_category_id', 1)->where('status', '1')->get();
        return view('frontend.research.autonews', compact('datas', 'slug'));
    }

    public function autoreview()
    {
        $slug = ucwords('Review');
        $slug_info = $slug;
        $datas = Blog::orderByDesc('id')->where('sub_category_id', 2)->where('status', '1')->get();
        return view('frontend.research.autoreview', compact('datas', 'slug'));
    }

        // public function researchCarTips()
    // {
    //     $videos = LatestVideo::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $review = Review::orderBy('created_at', 'desc')->where('status', '1')->get();
    //     $news = News::orderBy('created_at', 'desc')->where('status', '1')->get();


    //     $slug = ucwords('car tips');
    //     $slug_info = $slug;
    //     $review_id = 1;

    //     $datas = Blog::orderByDesc('id')->where('type', 4)->where('status', '1')->get();

    //     $info1 = Blog::orderByDesc('id')->where('type', 1)->where('status', '1')->get();
    //     $info2 = Blog::orderByDesc('id')->where('type', 2)->where('status', '1')->get();
    //     $info3 = Blog::orderByDesc('id')->where('type', 3)->where('status', '1')->get();

    //     return view('frontend.research.cartips', compact('videos','datas', 'slug','info1', 'info2', 'info3', 'slug_info', 'review_id'));
    // }

}
