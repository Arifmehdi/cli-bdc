@extends('frontend.website.layout.app')
@foreach(app('globalStaticPage') as $page)
@if ($page->slug == 'terms-condition')
@if ($page->description)
@section('meta_description',$page->description)
@else
@section('meta_description', app('globalSeo')['description'])
@endif
@if ($page->keyword)
@section('meta_keyword', $page->keyword)
@else
@section('meta_keyword', app('globalSeo')['keyword'])
@endif
@section('title')
{{ $termsconditions->title }}
@endsection
@endif
@endforeach
@section('gtm')
{!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title', $termsconditions->title . ' | Best Used Cars for Sale - bestdreamcar.com®')

{{--@section('og_description', app('globalSeo')['og_description'])--}}

{{--@section('og_title')
{{ $page->title }}
@endsection--}}

@section('og_description')
{{ $page->description }}
@endsection


@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])

@section('twitter_title', $termsconditions->title . ' | Best Used Cars for Sale - bestdreamcar.com®')
{{--@section('twitter_description', app('globalSeo')['twitter_description'])--}}

{{--@section('twitter_title')
{{ $page->title }}
@endsection--}}

@section('twitter_description')
{{ $page->description }}
@endsection


@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@push('css')
    <style>
        .terms_contidion_link {
            color: #b93434;
        }

        .terms_contidion_link:hover {

            text-decoration: underline;
            color: red !important;

        }
    </style>
@endpush
@section('content')


    <!--Breadcrumb-->
<section>
    <div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
        <div class="header-text mb-0">
            <div class="container">
                <div class="text-center text-white">
                    <h1 class="favorite-header">{{$termsconditions->title}}</h1>

                    <ol class="breadcrumb text-center">
                        <li   class="favorite-bc"><a style="color:white" href="{{ route('home') }}">Home<span style="margin-left:4px; margin-right:4px;">/</span> </a></li>
                        <li  class="favorite-bc"><a style="color:white" href="javascript:void(0);">{{$termsconditions->title}}</a></li>

                    </ol>
                </div>
            </div>
        </div>
    </div>
</section>
<!--/Breadcrumb-->

    <!--Faq section-->
    <section class="sptb">
        <div class="container">
            <div style="background:white; padding:28px" class="row">
                <div style="margin:0 auto"  class="col-xl-9 col-lg-9 ">
                    {{--<h2 style="font-weight:600; opacity:90%" class="mt-5 mb-3">TERMS AND CONDITION OF USE</h2>

                    <p style="font-size:18px" class="mt-5 mb-5">Effective Date: August 25, 2024</p>
                    <p style="font-size:16px" class="mb-5">PLEASE READ THESE TERMS AND CONDITIONS OF USE CAREFULLY. BY ACCESSING THIS WEBSITE, ANY OF ITS PAGES, OR ITS ASSOCIATED APPLICATIONS, YOU AGREE THAT YOU ARE BOUND BY THESE TERMS AND CONDITIONS OF USE AS THEY MAY BE AMENDED FROM TIME TO TIME. IF YOU DO NOT AGREE WITH ANY OF THESE TERMS AND CONDITIONS OF USE, PLEASE EXIT THIS WEBSITE IMMEDIATELY.</p>


                    <p style="font-size:18px; font-weight:600">Table of Contents</p>--}}



                       {{-- @foreach ($termsconditions as $key=>$item )
                        <p class="p-0 m-0 pb-2"><a style="font-size:15px; color:rgb(6, 123, 128); font-weight:500" href="#link{{$key+1}}" class="">{{$key+1}}.
                             {{$item->title}}</a></p>
                        @endforeach

                        @foreach ($termsconditions as  $key=>$itemshow )
                        <div id="link{{$key+1}}">
                            <h2 class="mt-5">{{$key+1}}.  {{$itemshow->title}}</h2>
                            <p style="text-align: justify">{!! $itemshow->description !!}</p>

                        </div>
                        @endforeach--}}
                        
                        <div id="">
                            <h2 class="mt-5">{{$termsconditions->title}}</h2>
                            <p style="text-align: justify">{!! $termsconditions->description !!}</p>
                        </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Faq section-->


@endsection

@push('js')
    @include('frontend.reapted_js')
@endpush
