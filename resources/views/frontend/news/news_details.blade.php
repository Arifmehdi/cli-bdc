@extends('frontend.website.layout.app')
@push('head')
    <link rel="canonical" href="{{ url()->current() }}">
@endpush
@section('meta_description', "$data->seo_description")
@section('meta_keyword', "$data->seo_keyword")
@section('gtm')
{!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title',  $data->title .' | BestDreamCar.com' ?? app('globalSeo')['og_title'])
@section('og_description', $data->description ?? app('globalSeo')['og_description'])

@section('og_type', app('globalSeo')['og_type'])
@section('og_url', url()->current() ?? app('globalSeo')['og_url'])
@section('og_site_name', 'BestDreamCar.com' ?? app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('og_img', asset('/frontend/assets/images/news/' . $data->img) ?? app('globalSeo')['og_img'])


@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title',   $data->title ?? app('globalSeo')['twitter_title'])
@section('twitter_description', $data->description ?? app('globalSeo')['twitter_description'])

@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', asset('/frontend/assets/images/news/' . $data->img) ?? app('globalSeo')['og_img'])
@section('title', $data->title)

@section('content')
<!--Breadcrumb-->
<section>
    <div class="bannerimg cover-image bg-background3 sptb-2" data-image-src="{{asset('frontend/assets')}}/images/banners/banner2.jpg">
        <div class="header-text mb-0">
            <div class="container">
                <div class="text-center text-white ">
                    <ol class="breadcrumb text-center new-details-bred">
                        <li>
                            <a style="color:white;" href="{{ route('home') }}">
                                Home<span style="margin: 0 4px;">/</span>
                            </a>
                        </li>
                        <li>
                            <a style="color:white;" href="{{route('frontend.research.review')}}">
                                Research<span style="margin: 0 4px;">/</span>
                            </a>
                        </li>
                            <a style="color:white;" href="{{route('frontend.research.auto.news')}}">
                                Auto News<span style="margin: 0 4px;">/</span>
                            </a>
                        </li>
                        {{--<li class="active {{ strlen($data->title) > 21 ? 'long-title' : 'short-title' }}" aria-current="page" style="color:white;">
                            {{$data->title}}
                        </li>--}}
                    </ol>
                    <h3 style="font-weight:500" class="new-details-tilte">{{$data->title}}</h3>

                </div>
            </div>
        </div>
    </div>
</section>
<!--/Breadcrumb-->

<!--news details-->
<section class="sptb">
    <div class="container">
        <div style="margin:0 auto" class="row">
            <div class="col-xl-8 col-lg-8 col-md-12 news-left">
                <div class="news-card">
                    <div class="">
                        <div>
                            <h1 style="font-size:25px; font-weight:600" class="mt-0 mb-4 news-top">{{$data->title}}</h1>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <!-- Author on the left -->
                            <div >
                                <!-- <p class="mb-1 fw-bold">By Arif</p> -->
                                <p class="mb-0 text-muted">{{ \Carbon\Carbon::parse($data->created_at)->format('d F, Y') }}</p>
                            </div>

                            <!-- Share section on the right -->
                            <div class="d-flex align-items-center">
                                <span class="me-2">Share:</span>
                                <!-- <a href="https://www.facebook.com/sharer/sharer.php?u={{url()->current()}}" target="_blank">
                                    <img src="{{ asset('frontend/assets/social/fb.png') }}" alt="Facebook" class="social-icon mx-2" style="width: 24px; height: 24px; margin: 0 8px;">
                                </a> -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ url()->current() }}&quote={{ $data->title }}" target="_blank">
                                    <img src="{{ asset('frontend/assets/social/fb.png') }}" alt="Facebook" class="social-icon mx-2" style="width: 24px; height: 24px; margin: 0 8px;">
                                </a>
                                <!-- <a href="https://twitter.com/intent/tweet?url={{url()->current()}}" target="_blank">
                                    <img src="{{ asset('frontend/assets/social/x.png') }}" alt="Twitter X" class="social-icon mx-2" style="width: 24px; height: 24px; margin: 0 8px;">
                                </a> -->
                                <a href="https://x.com/intent/tweet?url={{ url()->current() }}&text={{ $data->title }}&via=bestdreamcar" target="_blank">
                                    <img src="{{ asset('frontend/assets/social/x.png') }}" alt="Twitter X" class="social-icon mx-2" style="width: 24px; height: 24px; margin: 0 8px;">
                                </a>

                                <!-- Three-dot menu -->
                                <div class="dropdown">
                                    <i class="fa fa-ellipsis-v fa-lg mx-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"></i>
                                    <!-- <img src="menu-icon.png" alt="More" class="social-icon mx-2" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer;"> -->
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="copyToClipboard()">
                                                <!-- <img src="clipboard-icon.png" alt="Copy Link" class="icon-small"> Copy Link -->
                                                <i class="fa fa-clipboard"></i> Copy Link
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="mailto:?subject=Check this out!&body=Here's something interesting: {{$data->title}}">
                                                <!-- <img src="email-icon.png" alt="Email" class="icon-small"> Email -->
                                                <i class="fa fa-envelope"></i> Email
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                        </div>
                        <div class="item7-card-img mt-2">

                            @isset($data->img)
                            <img height="500px" src="{{ asset('/frontend/assets/images/news/' . $data->img) }}" alt="img" class="w-100 br-5 mb-4 art-image" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"/>
                            @else

                            <img  height="500px" class="w-100 br-5 mb-4 art-image" src="{{ asset('frontend/NotFound.png') }}"
                                alt="News img">

                            @endisset
                        </div>
                            <p class="mb-2 news-top">{{$data->sub_title}}</p>
                           <div style="font-size:16px; width:98%" class="mb-5 me-5">{!! $data->description !!}</div>

                    </div>
                </div>

            </div>

            <!--Rightside Content-->
            <div class="col-xl-4 col-lg-4 col-md-12 details-bottom">
                @include('frontend.common.blog_categories')

                <p style="font-size:19px; font-weight:600; margin-top:-7px; opacity:95%; margin-bottom:5px">Get Even More Great Car News</p>


                <p style="font-size:16px;">The latest news, reviews, and cool cars delivered to your inbox.</p>
                <form id="subscribe" action="{{route('frontend.subscribe.user')}}" method="post">
                        @csrf
                <div class="d-flex news-right-title">

                    <input height="80px" class="me-2 subs-email-input" type="email" name="email" placeholder="Enter your email">
                    <button type="submit" class="btn btn-primary subscribe-button text-center">Subscribe</button>


                </div>
                </form>

                <p style="font-size:18px; font-weight:500;">Trending Now</p>


                <div class="rated-products">
                    <ul class="vertical-scroll">
                        @forelse ($news as $new)
                        <li class="item mb-3">
                            <div class="media m-0 mt-0 p-1">
                                <a href="{{ route('frontend.news.details', ['slug' => $new->slug]) }}">
                                    @if (isset($new->img))
                                        <img
                                        class="me-2  tending-img"
                                        src="{{ asset('/frontend/assets/images/news/' . $new->img) }}"
                                        alt="news-details" onerror="this.onerror=null; this.src='{{ asset('frontend/NotFound.png') }}';"/>
                                    @else
                                    <a href="#">
                                        <img  src="{{ asset('frontend/found/NotFound.png') }}" alt="img"
                                        class="me-2  tending-img">
                                    </a>

                                    @endif
                                </a>
                                @php
                                    $title = Str::limit($new->title,'17','...' );
                                    $desc = Str::limit(strip_tags($new->description), 50, '...');
                                @endphp
                                <div class="media-body">
                                    <h6 style="font-weight:600" class="mb-0 dea-tit">
                                        <a style="" class="mt-2 d-block hyperlink-title"
                                        href="{{ route('frontend.news.details', ['slug' => $new->slug]) }}">{{ $new->title }}</h4>
                                    </a>
                                    </h6>
                                    <p style="font-size:15px" class="mb-1 mt-0">{!! $desc !!}</p>
                                    <p style="font-size:12px; font-weight:400" class="mb-0"> {{\Carbon\Carbon::parse($new->created_at)->format('F d, Y')}} </p>

                                    {{-- <a style="color:rgb(5, 140, 150); cursor:pointer" class="mt-2 d-block"
                                        href="{{ route('frontend.news.details', ['id' => $new->id, 'title' => str_replace(' ', '-', $new->title)]) }}">Read more</h4>
                                    </a> --}}
                                    @php
                                    $des = Str::substr($new->description, 0, 25)
                                    @endphp
                                </div>
                            </div>
                        </li>
                        @empty
                        <p style="text-align:center">No Related Seller Ads</p>
                        @endforelse
                    </ul>
                </div>



                {{-- <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Popular News</h3>
                    </div>

                    <div class="card-body">
                        <div class="rated-products">
                            <ul class="vertical-scroll">
                                @forelse ($news as $new)
                                <li class="item">
                                    <div class="media m-0 mt-0 p-1">


                                        <a
                                            href="{{ route('frontend.news.details', ['id' => $new->id, 'title' => str_replace(' ', '-', $new->title)]) }}"><img
                                                class="me-2 br-3 related-img"
                                                src="{{ asset('/frontend/assets/images/news/' . $new->img) }}"
                                                alt="">
                                        </a>
                                        <div class="media-body">
                                            <a
                                                href="{{ route('frontend.news.details', ['id' => $new->id, 'title' => str_replace(' ', '-', $new->title)]) }}">{{ $new->title }}</h4>
                                            </a>
                                            @php
                                            $des = Str::substr($new->description, 0, 25)
                                            @endphp




                                        </div>
                                    </div>
                                </li>
                                @empty
                                <p style="text-align:center">No Related Seller Ads</p>
                                @endforelse


                            </ul>
                        </div>
                    </div>
                </div> --}}


            </div>
            <!--/Rightside Content-->
        </div>
    </div>
</section>
<!--/Add details-->

{{--@include('frontend.common.mobile_blog_categories')--}}
@include('frontend.website.layout.new_footer_beyond_sub_menu')
<script>
    function copyToClipboard() {
        const url = window.location.href;

        if (navigator.clipboard) {
            // Clipboard API available
            navigator.clipboard.writeText(url).then(() => {
                alert('Link copied to clipboard!');
            }).catch(err => {
                console.error('Error copying text with Clipboard API: ', err);
                fallbackCopyToClipboard(url);  // Fallback to execCommand if Clipboard API fails
            });
        } else {
            // Clipboard API not available, using fallback
            fallbackCopyToClipboard(url);
        }
    }

    function fallbackCopyToClipboard(text) {
        // Create a temporary input element to hold the text to copy
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);

        // Select the text and copy it to the clipboard
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices
        document.execCommand('copy');

        // Remove the temporary input element
        document.body.removeChild(tempInput);

        // show toast notification
        toastr.success('Link copied to clipboard!')
    }
</script>

@endsection

@push('js')
<script>

$.ajaxSetup({
         headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
       });

    $('#subscribe').on('submit', function(e) {
                e.preventDefault();

                // Serialize the form data

                let sub= $('.subs-email-input').val();
                if(sub){
                    var formData = new FormData($(this)[0]);
                $('.subscribe-button').text('Loading...');
                    $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle success response
                        console.log(response);
                        $('#subscribe')[0].reset();
                        toastr.success(response.message);
                        $('#MobileFilterModal').modal('hide');
                        $('.subscribe-button').text('Subscribe');
                    },
                    error: function(xhr) {
                        // Handle error response
                        var errors = xhr.responseJSON.errors;

                    }
                });
                }else{
                    Swal.fire({
    title: 'Must Enter Your Email',
    icon: 'warning', // Change to 'warning' for a warning type alert
    confirmButtonText: 'OK'
});


                }


            });
</script>
@endpush
