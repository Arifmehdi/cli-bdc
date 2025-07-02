@extends('frontend.website.layout.app')

@push('head')
<link rel="canonical" href="{{ url()->current() }}">
@endpush
@foreach(app('globalStaticPage') as $page)
@if ($page->slug == 'faq')
@if ($page->description)
{{--@section('meta_description',$page->description)--}}
@section('meta_description')
Get answers to frequently asked questions about bestdreamcar.
@endsection

@else
@section('meta_description')
Get answers to frequently asked questions about bestdreamcar.
@endsection
@endif
@if ($page->keyword)
@section('meta_keyword', $page->keyword)
@else
@section('meta_keyword', app('globalSeo')['keyword'])
@endif
@section('title')
FAQ | Best Used Cars for Sale - bestdreamcar.com®
@endsection
@endif
@endforeach



@section('gtm')
{!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])

@section('og_title')
FAQ | Best Used Cars for Sale - bestdreamcar.com®
@endsection

@section('og_description')
Get answers to frequently asked questions about bestdreamcar.
@endsection
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])

@section('twitter_title')
FAQ | Best Used Cars for Sale - bestdreamcar.com®
@endsection
@section('twitter_description')
Get answers to frequently asked questions about bestdreamcar.
@endsection
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@section('content')

    <!--Breadcrumb-->
    <section>
        <x-breadcrumb :slug="$slug" category="true" main="Research"  :route="route('frontend.research.review')" />
    </section>
    <!--/Breadcrumb-->

    {{-- sub menu start here  --}}
    @include('frontend.website.layout.research_sub_menu')
    {{-- sub menu end here  --}}


<section id="error" class="section-padding-80 components">
    <div class="container">
        <div style="margin-top:65px; margin-bottom:150px" class="row">
            <div class="col-md-7 margin-bottom-40">
                <div style="width:90%" class="contact-first-part">
                    @if($faqs->count() > 1)
                    <p style="font-size:35px; font-weight:500" class="faqs-title"><img width="60px" src="{{asset('/frontend/assets/images/Light.svg')}}" />Frequently Asked Questions</p>
                    @endif

                    @if($faqs->isNotEmpty())
                    <div class="panel-group1" id="accordion2">
                        @foreach ($faqs as $index => $faq)

                        <div class="panel panel-default mb-4 border p-0">
                            <div class="panel-heading1">
                                <h4 class="panel-title1">
                                    <a class="accordion-toggle {{ $index == 0 ? '' : 'collapsed' }}" data-bs-toggle="collapse" data-parent="#accordion2" href="#collapse{{$faq->id}}" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}">
                                        {{$faq->title}}
                                    </a>
                                </h4>
                            </div>
                            <div id="collapse{{$faq->id}}" class="panel-collapse collapse {{ $index == 0 ? 'show' : '' }}" role="tabpanel" aria-expanded="{{ $index == 0 ? 'true' : 'false' }}">
                                <div class="panel-body bg-white">
                                    <p>{!! $faq->description !!}</p>
                                </div>
                            </div>
                        </div>

                        @endforeach
                    </div>
                    @endif
                </div>

            </div>
            <div class="col-md-5 margin-bottom-40">
                <img style="margin-top:72px;" src="{{asset('/frontend/assets/images/faq_image.jpg')}}" />
                <h5 style="margin-top:1rem; font-weight:800">WE HELP YOU GET A GREAT DEAL ON A CAR!</h5>
                <p class="mt-4">At BestDreamCar, we assist you in finding a fantastic deal on a new or used vehicle by providing you with up-to-date car pricing information. This ensures you have all the knowledge you need while shopping for a car. Additionally, we help buyers with less-than-perfect credit discover special financing options through our network of trusted lending partners.</p>
                <p style="margin-top:1rem; font-weight:600">How We Do It...</p>

                <h5 style="margin-top:1rem; font-weight:800">EXCLUSIVE, OBJECTIVE PRICING INSIGHTS</h5>
                <p class="mt-4">Our dedicated team of pricing experts continuously analyzes the latest car pricing data to identify great deals and emerging pricing trends that benefit car shoppers. In fact, our experts are so thorough that they often uncover industry news stories that are later reported by major car news publications!</p>
                <p class="mt-4">As an independent company, you can trust that our pricing information is completely objective and unaffected by external influences.</p>

                <div style="border-left: 2px solid darkcyan">
                    <div style="padding-left:55px">
                        <p class="p-0 mb-2" style="font-size:22px; margin-top:55px; font-weight:600; opacity:80%">Let's Us Contact?</p>
                        <p class="p-0" style="font-size: 16px;">Check out our contact page and let's connect us for more information.</p>
                        <a href="{{ route('contact')}}" style="padding:7px 55px 7px 55px; background:none; border:1px solid darkcyan; border-radius:12px; font-size:16px; color:darkcyan ">Contact Us</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!--Section-->

<!--Faq section-->
<!-- <section class="sptb">
        <div class="container">
            <div class="panel-group1" id="accordion2">
                @foreach ($faqs as $faq )


                <div class="panel panel-default mb-4 border p-0">
                    <div class="panel-heading1">
                        <h4 class="panel-title1">
                            <a class="accordion-toggle collapsed" data-bs-toggle="collapse" data-parent="#accordion2" href="#collapse{{$faq->id}}" aria-expanded="false">{{$faq->title}}</a>
                        </h4>
                    </div>
                    <div id="collapse{{$faq->id}}" class="panel-collapse collapse active" role="tabpanel" aria-expanded="false">
                        <div class="panel-body bg-white">
                            <p>{!! $faq->description !!}</p>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>
    </section> -->
<!--/Faq section-->


    {{-- sub menu start here  --}}
    @include('frontend.website.layout.new_footer_beyond_sub_menu')
    {{-- sub menu end here  --}}

@endsection

@push('js')

@include('frontend.reapted_js')
@endpush
