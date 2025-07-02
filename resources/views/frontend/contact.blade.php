@extends('frontend.website.layout.app')
@push('head')
    <link rel="canonical" href="{{ url()->current() }}">
@endpush
{{--@section('meta_description', app('globalSeo')['description'])--}}
@section('meta_description')
        Contact bestdreamcar.com via online chat, telephone or email and view frequently asked questions about buying and selling a vehicle on our website.
@endsection

@section('meta_keyword', app('globalSeo')['keyword'])
@section('gtm')
{!! app('globalSeo')['gtm'] !!}
@endsection
@section('app_id', app('globalSeo')['app_id'])
@section('og_title')
    Contact Us  - bestdreamcar.com®
@endsection
{{--@section('og_description', app('globalSeo')['og_description'])--}}
@section('og_description')
        Contact bestdreamcar.com via online chat, telephone or email and view frequently asked questions about buying and selling a vehicle on our website.
@endsection
@section('og_type', app('globalSeo')['og_type'])
@section('og_url', app('globalSeo')['og_url'])
@section('og_site_name', app('globalSeo')['og_site_name'])
@section('og_locale', app('globalSeo')['og_locale'])
@section('twitter_card', app('globalSeo')['twitter_card'])
@section('twitter_title')
    Contact Us  - bestdreamcar.com®
@endsection
{{--@section('twitter_description', app('globalSeo')['twitter_description'])--}}
@section('twitter_description')
        Contact bestdreamcar.com via online chat, telephone or email and view frequently asked questions about buying and selling a vehicle on our website.
@endsection
@section('twitter_site', app('globalSeo')['twitter_site'])
@section('twitter_creator', app('globalSeo')['twitter_creator'])
@section('twitter_image', app('globalSeo')['twitter_image'])
@section('og_img', app('globalSeo')['og_img'])
@section('title')
    Contact Us  - bestdreamcar.com®
@endsection
@section('content')
<!--Breadcrumb-->
<div>
    <div class="bannerimg cover-image bg-background3" data-image-src="../assets/images/banners/banner2.jpg">
        <div class="header-text mb-0">
            <div class="container">
                <div class="text-center text-white ">
                    <h1 class="">Contact Us</h1>

                    <ol class="breadcrumb text-center">
                        <li class=""><a style="color:white" href="{{ route('home') }}">Home<span
                                    style="margin-left:4px; margin-right:4px;">/</span> </a></li>
                        <li class=""><a style="color:white" href="javascript:void(0);">Contact</a></li>

                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<!--/Breadcrumb-->

<section id="error" class="section-padding-80 components">
    <div class="container">
        <div style="margin-top:65px; margin-bottom:150px" class="row">
            <div class="col-md-6 margin-bottom-40">
                <div style="width:90%" class="contact-first-part">
                <p style="font-size:35px; font-weight:500"><img style="margin-top:-10px" width="28px" class="me-2" src="{{'/frontend/assets/images/email.png'}}"  alt="Used cars for sale Best Dream car contact email image"/>Contact Us</p>
              {{-- <p style="font-size:17px; opacity:80%;">For information about our products, services or premium membership upgrade, please contact our support team at:</p> --}}
              <p style="font-size:17px; opacity:80%;">If you have an idea for BestDreamCar or a feature you'd like to see added, please fillout this form and submit.</p>

              <form id="contact" action="{{ route('contact.store') }}" method="post">
                                        @csrf
                                        <div style="margin-top:30px" class="form-group">
                                            <input style="height:55px; border-radius:7px;" type="text" class="form-control" id="name" name="name"
                                                placeholder="Enter yor name">
                                            <span id="name-error" class="error-message text-danger"></span>
                                        </div>
                                        <div class="form-group">
                                            <input style="height:55px; border-radius:7px;" type="email" class="form-control" id="email" name="email"
                                                placeholder="Enter your email">
                                            <span id="email-error" class="error-message text-danger"></span>
                                        </div>
                                        <div class="form-group">
                                            <textarea style="border-radius:7px;" class="form-control" name="message" id="message" rows="6" placeholder="Enter your message"></textarea>
                                            <span id="message-error" class="error-message text-danger"></span>
                                        </div>
                                        <div class="form-group">
                                            <p
                                                style="color:rgb(67, 68, 68); font-weight:600; margin-bottom:15px; margin-top:10px">
                                                <span class="text-black">*</span> Security Question (Enter the
                                                Correct answer)
                                            </p>

                                            <div style="display: flex">
                                                <div id="TestcaptchaLabelContact"
                                                    style="background-color:white; width:60%; margin-right:10px; text-align:center; padding-top:10px; font-weight:600; margin-top:2px; margin-left:3px; height:45px; border-radius:5px; border:1px solid rgb(189, 188, 188)">
                                                    {{ app('mathcaptcha')->label() }}
                                                </div>
                                                <div>
                                                    <input style="height:45px"
                                                        class="form-control @error('mathcaptcha') is-invalid @enderror mb-2"
                                                        type="text" name="mathcaptcha"
                                                        placeholder="Enter your result">
                                                    <span id="mathcaptcha-error" class="error-message text-danger"></span>
                                                </div>
                                            </div>





                                        </div>
                                        <button style="background:darkcyan; color:white; width:100%;height:50px; border-radius:7px; font-size:18px" type="submit"
                                            id="contact_button" class="btn">Send Message</button>
                                    </form>



                </div>


                {{--<p style="font-size:35px; font-weight:500; margin-top:75px"><img style="margin-top:-15px" width="40px" src="{{'/frontend/assets/images/info.png'}}"  alt="Used cars for sale Best Dream car contact info image"/><span style="margin-left:2px">About Us</span></p>
                <p class="about-mini-title">Why Best Dream car?</p>
                <p class="about-mini-para">Bestdreamcar.com is an independent company that works side by side with consumers, sellers, and dealers for transparency and fairness in the marketplace. Best Dream car does not have the complete history of every vehicle. Use the Best Dream car search as one important tool, along with a vehicle inspection and test drive, to make a better decision about your next used car</p>
                <p class="about-mini-title">Why Choose Us?</p>
                <p class="point"><img  width="20px" class="me-2" src="{{'/frontend/assets/images/point.png'}}" alt="Used cars for sale Best Dream car contact point image1"/>24/7 Support</p>
                <p class="point"><img  width="20px" class="me-2" src="{{'/frontend/assets/images/point.png'}}" alt="Used cars for sale Best Dream car contact point image2"/>Transparency and Trust</p>
                <p class="point"><img  width="20px" class="me-2" src="{{'/frontend/assets/images/point.png'}}" alt="Used cars for sale Best Dream car contact point image3"/>User Friendly Experience</p>

                <p class="point"><img  width="20px" class="me-2" src="{{'/frontend/assets/images/point.png'}}" alt="Used cars for sale Best Dream car contact point image4"/>Reliable Service and Maintenance</p>
                <p class="point"><img  width="20px" class="me-2" src="{{'/frontend/assets/images/point.png'}}" alt="Used cars for sale Best Dream car contact point image5"/>Competitive Pricing and Financing Options</p>--}}



            </div>
            <div class="col-md-6 margin-bottom-40">
                <img style="margin-top:72px;" class="cont-img" src="{{asset('/frontend/assets/images/conatct_02.jpg')}}"  alt="Used cars for sale Dream Best contact page contact image"/>

                <p class="mt-4">Looking for your perfect vehicle that fits your budget and lifestyle is everyone's dream. Are you ready to find your dream car?</p>
                <p>Going through countless listings on various car websites can be overwhelming and time-consuming. That's why we developed the BestDreamCar search engine for new, used, and certified cars—to streamline your search and eliminate the guesswork. We are dedicated to helping you find your ideal vehicle, whether you need a reliable daily car, a robust work truck, or the car of your dreams. With BestDreamCar, your search for the perfect automobile becomes easy and enjoyable, allowing you to focus on what truly matters—driving.</p>
                <p>Our mission is to provide you with all the essential resources you need to confidently choose a vehicle that suits your needs. Your feedback is crucial for our continuous improvement, and we strive to make BestDreamCar the ultimate destination for purchasing your next car. You can trust us to be your reliable partner every step of the way.</p>
                <p>Additionally, don’t overlook the BestDreamCar News and Research section, which offers valuable insights, expert advice, and practical guides to help you navigate the car-buying process. Equip yourself with knowledge and make informed decisions as you embark on this exciting journey to find your next dream car.</p>

                <div style="border-left: 2px solid darkcyan">
                                        <div style="padding-left:55px">
                                        <p class="p-0 mb-2" style="font-size:22px; margin-top:55px; font-weight:600; opacity:80%">Frequently Asked Questions?</p>
                                    <p class="p-0" style="font-size: 16px;">Check out some of our most commonly asked questions.
                                    See FAQs</p>
                                    <a href="{{ route('frontend.faq')}}" style="padding:7px 55px 7px 55px; background:none; border:1px solid darkcyan; border-radius:12px; font-size:16px; color:darkcyan ">See FAQs</a>
                                        </div>
                                    </div>

            </div>
        </div>
    </div>
</section>



@endsection

@push('js')
<script>

function contactCaptcha() {

console.log('hello');
$.ajax({
    url: '/refresh-captcha',
    type: 'GET',
    success: function(data) {
        console.log(data);
        $('#TestcaptchaLabelContact').html(data.label);


    },
    error: function(xhr, status, error) {
        console.error('Error refreshing captcha:', error);
    }
});
}



    $(document).ready(function() {

        contactCaptcha();

        $('#contact').submit(function(e) {
            e.preventDefault();

            var formData = new FormData($(this)[0]);

            var form = this;

            $('#contact_button').text('Loading ...');


            // Make Ajax request
            $.ajax({

                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                contentType: false,
                processData: false,
                success: function(res) {
                    console.log(res);

                    $('.error-message').html('');

                    if (res.errors) {
                        $.each(res.errors, function(key, value) {
                            $('#' + key + '-error').html(value[0]);
                        });
                        $('#contact_button').text('Send Message');
                    }

                    if (res.status == 'success') {
                        toastr.success(res.message);
                        form.reset();
                        $('#contact_button').text('Send Message');
                         contactCaptcha();
                    }
                },
                error: function(error) {
                    // Handle error response
                    console.error(error);
                    $('#contact_button').text('Send Message');
                }
            });
        });
    });


</script>
@endpush
