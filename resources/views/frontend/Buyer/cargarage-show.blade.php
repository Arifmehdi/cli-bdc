@extends('frontend.website.layout.app')

@section('meta_description', app('globalSeo')['description'])
@section('meta_keyword', app('globalSeo')['keyword'])
@section('title', 'Account | ' . app('globalSeo')['name'])
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

@section('content')
<style>
    .square-card {
        width: 100%;
        /* Smaller size */
        aspect-ratio: 1;
        /* Ensures the card is square */
        overflow: hidden;
        border-radius: 5px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
        height: 280px;
    }

    .square-card img {
        width: 100%;
        height: 202px;
        object-fit: cover;
        transition: transform 0.3s;
    }

    .square-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
    }

    .square-card:hover img {
        transform: scale(1.05);
    }

    .card-content {

        background: rgba(0, 0, 0, 0.6);
        color: #fff;
        padding: 10px;
        text-align: center;
        width: 100%;
        height: 80px;
    }

    .card-content h5 {
        margin: 0;
        font-size: 0.9rem;
        /* Smaller font size */
    }

    .card-content p {
        font-size: 18px;
        /* Smaller font size */
        margin: 5px 0 0;
    }

    .car-card {
        margin-top: 5px;
    }
</style>
<div class="container" style="margin-top:160px; margin-bottom:250px">
    <div style="margin:0 auto" class="row">
        <div class="card car-card">
            
            <div class="container my-5">
                <div class="d-flex justify-content-between w-100">
                    <h3 class="mb-4">Pending Car</h3>
                    <a style="font-size:15px; color:rgb(68, 68, 68); background:cadetblue; width:120px; border-radius:5px; text-align:center; display:inline-flex; justify-content:center; align-items:center; padding:10px; color:white"
                        class="mb-4" href="{{ route('buyer.cargarage') }}">Add Listing</a>

                </div>

                <div class="row">
                    @forelse($datas as $data)
                    @php

                    $image_obj = $data->img_from_url;
                    $images = json_decode($image_obj, true);
                    $title = $data->make . ' ' . $data->model . ' ' . $data->year;
                    @endphp

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-5">
                        <div class="square-card">
                            @if(is_array($images) && count($images) > 0)
                            <img src="{{ asset('frontend/assets/images/listings/' . $images[0]) }}" alt="Listing Image"
                                class="">
                            @else
                            <img class="" width="100%" src="{{ asset('frontend/NotFound.png') }}"
                                alt="Used cars for sale coming soon image dream best">
                            @endif
                            <div class="card-content">
                                <h5>{{$title}}</h5>
                                <p>{{ $data->price_formate}}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <h6 class="test-center mt-5 mb-5">No pending Listing</h6>
                    @endforelse

                </div>
            </div>
            <div class="container my-5">
                <div class="d-flex justify-content-between w-100">
                    <h3 class="mb-4">Approved Car</h3>
                    

                </div>

                <div class="row">
                    @forelse($approves as $data)
                    @php

                    $image_obj = $data->img_from_url;
                    $images = json_decode($image_obj, true);
                    $title = $data->make . ' ' . $data->model . ' ' . $data->year;
                    @endphp

                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 mb-5">
                        <div class="square-card">
                            @if(is_array($images) && count($images) > 0)
                            <img src="{{ asset('frontend/assets/images/listings/' . $images[0]) }}" alt="Listing Image"
                                class="">
                            @else
                            <img class="" width="100%" src="{{ asset('frontend/NotFound.png') }}"
                                alt="Used cars for sale coming soon image dream best">
                            @endif
                            <div class="card-content">
                                <h5>{{$title}}</h5>
                                <p>{{ $data->price_formate}}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <h6 class="test-center mt-5 mb-5">No Approved Listing</h6>
                    @endforelse

                </div>
            </div>
        </div>
    </div>


</div>






@endsection

@push('js')

@endpush