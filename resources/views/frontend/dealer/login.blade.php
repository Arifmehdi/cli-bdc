@extends('frontend.website.layout.app')

@foreach(app('globalStaticPage') as $page)
@if ($page->slug == 'find-dealership')
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
{{ $page->title . '| ' . app('globalSeo')['name'] }}
@endsection
@endif
@endforeach

@section('gtm')
{!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title', app('globalSeo')['og_title'])
@section('og_description', app('globalSeo')['og_description'])
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title', app('globalSeo')['twitter_title'])
@section('twitter_description', app('globalSeo')['twitter_description'])
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@push('css')
    <style>
    .showInventoryBtn
    {
        border: 1px solid rgb(73, 4, 100);
        padding: 5px;
        text-transform: uppercase;
        color: rgb(73, 4, 100);
        border-radius:7px;
        font-weight: 500;

    }
    .showInventoryBtn:hover
    {
        background-color: red;
        transition: .5s ease all;
        color: white;
    }

    .dealer-result-name
    {

        font-weight: 700;
        color: rgb(31, 17, 17);
        text-transform: uppercase;
    }
    .dealer-result-location
    {


        color: rgb(31, 17, 17);
        text-transform: uppercase;
        font-size: 12px
    }
    </style>
@endpush
@section('content')

	<!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3 sptb-2" data-image-src="{{asset('frontend/assets')}}/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h1 class="">Dealer Login</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">Login</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->


	<!--Statistics-->
    <section>
       <div class="container">
        <div style="margin-top:75px; margin-bottom:125px" class="row">
            @if (session()->has('message'))
            <span class="text-danger mt-2">{{ session()->get('message') }}</span>
            @endif
            <div style="background:white; margin:0 auto; border-radius:7px; box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px;" class="col-lg-6 col-md-12 p-5">
            <form action="{{ route('frontend.dealer.login.submit')}}" method="POST">
                @csrf
                <div class="mb-5">
                    <label for="exampleFormControlInput1" class="form-label">Enter Your Secret Key</label>
                    <input type="text" class="form-control" placeholder="Your secret key" name="secret_key" value="{{ old('secret_key')}}">
                    @if ($errors->has('secret_key'))
                        <span class="text-danger mt-2">{{ $errors->first('secret_key') }}</span>
                    @endif
                  </div>
                  <div class="mb-5">
                    <label for="exampleFormControlInput1" class="form-label">Enter new password</label>
                    <input type="password" class="form-control" name="password" placeholder="Enter new password">
                    @if ($errors->has('password'))
                    <span class="text-danger mt-2">{{ $errors->first('password') }}</span>
                    @endif
                  </div>
                  <div class="mb-5">
                    <label for="exampleFormControlInput1" class="form-label">Enter Confirm password</label>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Enter confirm password">
                    @if ($errors->has('confirm_password'))
                    <span class="text-danger mt-2">{{ $errors->first('confirm_password') }}</span>
                    @endif
                  </div>
                  <div class="mb-3">
                    <button class="btn  px-5" style="float: right; background:darkcyan; color:white; font-size:15px">Submit</button>

                  </div>
            </form>
            </div>
           
        </div>
       </div>
    </section>

@endsection

@push('js')

@endpush
