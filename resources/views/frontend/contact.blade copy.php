@extends('frontend.website.layout.app')
@section('meta_description', app('globalSeo')['description'])
@section('meta_keyword', app('globalSeo')['keyword'])
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
@section('title', 'Contact-Us | ' . app('globalSeo')['name'])
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


    <!--Contact-->


    {{-- contact modal start --}}
    <div class="modal fade" id="ContactModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-body">
                    <button
                        style="position: absolute; top:15px; right:10px; background-color: white; z-index:9; padding:10px; border-radius:50%; color:black; font-size:11px"
                        type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>


                    <div class="row">

                        <div class="col-lg-12 col-xl-12 col-md-12">
                            <div class="single-page">
                                <div class="col-lg-12 col-md-12 mx-auto p-0 d-block">

                                    <div style="text-align:center" class="mt-5">
                                        <img style="width:150px;"
                                            src="https://media.geeksforgeeks.org/wp-content/uploads/20230822131732/images.png"
                                            alt="Welcome to our Contact Us page">
                                        <h3 style="text-align:center">Get in Touch With Us</h3>
                                        <p style="text-align:center">
                                            We're here to answer any questions you may have.
                                        </p>
                                    </div>
                                    <form id="contact" action="{{ route('contact.store') }}" method="post">
                                        @csrf
                                        <div style="margin-top:30px" class="form-group">
                                            <input type="text" class="form-control" id="name" name="name"
                                                placeholder="Your Name">
                                            <span id="name-error" class="error-message text-danger"></span>
                                        </div>
                                        <div class="form-group">
                                            <input type="email" class="form-control" id="email" name="email"
                                                placeholder="Email Address">
                                            <span id="email-error" class="error-message text-danger"></span>
                                        </div>
                                        <div class="form-group">
                                            <textarea class="form-control" name="message" id="message" rows="6" placeholder="Message"></textarea>
                                            <span id="message-error" class="error-message text-danger"></span>
                                        </div>
                                        <button style="background:darkcyan; color:white; float:right" type="submit"
                                            id="contact_button" class="btn">Send Message</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
    {{-- contact modal close --}}
    <section class="sptb2 border-top">
        <div class="container">

            <div class="row">
                <div style="text-align:center" class="col-md-12 col-lg-4 col-sm-12">
                    <div class=" mt-0 mb-5 mb-lg-0  br-3  p-4 ">

                        <div class="">
                            <h1 class="help" style="font-weight:600">How Can We Help You?</h1>
                        </div>
                    </div>
                </div>

                <div style="text-align:center" class="col-md-6 col-lg-4">
                    <div class="d-flex mt-0 mb-0  br-3  p-4 ">

                        <div style="text-align:center" class="">
                            <div style="display:flex; margin-bottom:10px;">
                                <i style="font-size:25px; color:darkcyan" class="fa fa-comment me-2"></i>

                                <h3 style="" class="">Message
                                </h3>
                            </div>

                            <button data-bs-toggle="modal" data-bs-target="#ContactModal"
                                style="background: none; border:2px solid rgb(88, 4, 114); padding-left:60px; padding-right:60px; padding-top:14px; padding-bottom:14px; border-radius:7px; font-size:18px">Send
                                Message</button>
                        </div>
                    </div>
                </div>


                <div style="text-align:center" class="col-md-6 col-lg-4">
                    <div class="d-flex mt-0 mb-5 mb-lg-0  br-3  p-4 ">

                        <div style="text-align:center" class="">
                            <div style="display:flex; margin-bottom:10px">
                                <i style="font-size:25px; color:darkcyan" class="fa fa-envelope me-2"></i>

                                <h3 style="" class="">Feedback
                                </h3>
                            </div>
                            <button
                                style="background: none; border:2px solid rgb(88, 4, 114); padding-left:60px; padding-right:60px; padding-top:14px; padding-bottom:14px; border-radius:7px; font-size:18px">Leave
                                feedback</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Contact-->

    <section class="sptb2 bg-white border-top">
        <div class="container">
            <div class="row contact-part">
                <h2 style="margin-bottom:37px" class="contact-title text-center">Have a question? Let's connect</h2>
                <div class="col-lg-7 col-xl-4 col-md-5 question-item"
                    style="border-right: 2px solid gray; margin-left: 175px;">
                    <div class="sub-newsletter text-center">
                        <img style="width:50%" src="{{ asset('/frontend/assets/images/Lightbulb.svg') }}" />
                        <h3>Frequently Asked Questions</h3>
                        <p class="mt-3">Check out our most popular questions, or reach out anytime by phone, chat, or
                            feedback.</p>
                        <a href="{{ route('frontend.faq') }}" class="btn btn-primary"
                            style="background: rgb(88, 4, 114); border-radius: 7px; font-size: 15px; color: white; margin-top: 5px; padding: 7px 30px;">See
                            FAQ</a>
                    </div>
                </div>
                <div style="margin-left: 15px" class="col-lg-5 col-xl-4 col-md-5 contact-info">
                    <div class="mb-4">
                        <p style="font-size: 19px; margin: 0;">Contact Customer Service</p>
                        <p class="mb-1" style="margin: 0;"><a href=""><i class="fa fa-envelope me-2"></i><span
                                    style="color:rgb(128, 13, 173)">support@dreambestcar.com</span></a></p>

                    </div>
                    <div class="mb-4">
                        <p style="font-size: 19px; margin: 0;">Contact our Advertising team</p>
                        <p style="margin: 0;"><a href=""><i class="fa fa-envelope me-2"></i><span
                                    style="color:rgb(128, 13, 173)">NationalSales@dreambestcar.com</span></a></p>
                    </div>
                    <div class="mb-4">
                        <p style="font-size: 19px; margin: 0;">Contact our Public Relations team</p>
                        <p class="mb-1" style="margin: 0;"><a href=""><i class="fa fa-envelope me-2"></i><span
                                    style="color:rgb(128, 13, 173)">support@dreambestcar.com</span></a></p>

                    </div>
                    <div class="mb-4">
                        <p style="font-size: 19px; margin: 0;">Contact our Billing Department</p>
                        <p class="mb-1" style="margin: 0;"><a href=""><i class="fa fa-envelope me-2"></i><span
                                    style="color:rgb(128, 13, 173)">billing@dreambestcar.com</span></a></p>
                        <p style="margin: 0;"><a href=""><i class="fa fa-phone me-2"></i><span
                                    style="color:rgb(128, 13, 173)">1-877-585-3753</span></a></p>
                    </div>
                    <div class="mb-4">
                        <p style="font-size: 19px; margin: 0;">Contact the Cars.com offices</p>
                        <p class="mb-1" style="margin: 0;"><a href=""><i class="fa fa-envelope me-2"></i><span
                                    style="color:rgb(128, 13, 173)">support@dreambestcar.com</span></a></p>
                        <p style="margin: 0;"><a href=""><i class="fa fa-phone me-2"></i><span
                                    style="color:rgb(128, 13, 173)">1-877-585-3753</span></a></p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <section>
        <div class="sptb-3 sptb-tab contact-card" style="background: #250858; height:420px">
            <div class="">
                <div class="container">
                    <h1 style="color:white; margin-top:-35px">Better together with innovative solutions</h1>
                    <p style="color:white; margin-bottom:10px">CARS is an incubator for the most innovative products and
                        solutions driving the
                        future of the automotive industry, and importantly, helping to drive our customers’ businesses. We
                        are made up of dreambestcar.com, Dealer Inspire and DealerRater.</p>

                    <div style="margin-top:35px" class="row">
                        <div style="margin-right:35px" class="col-md-12 col-lg-3">



                            <img style="margin-bottom: 35px; margin-top:45px"
                                src="{{ asset('/frontend/assets/images/jj.svg') }}" />


                        </div>
                        <div class="col-md-12 col-lg-4 mb-1">
                            <div class="d-flex mt-0 mb-5 mb-lg-0  br-3  p-4">

                                <div class="text-white">
                                    <h3 class="mt-0 mb-1 fs-20 mb-3">Dealer
                                        Information</h3>
                                    <p class="fs-14">From search to signature, we innovate connected
                                        marketing and technology solutions that make automotive retail faster, easier, and
                                        smarter for both shoppers and dealers. We future-proof the dealership
                                        experience.</p>


                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-lg-4">
                            <div class="d-flex mt-0 mb-5 mb-lg-0  br-3  p-4">

                                <div class="text-white">
                                    <h3 class="mt-0 mb-1 fs-20 mb-3">Dealer Rater</h3>
                                    <p class="fs-14">Today, nearly 30 consumer reviews later, DealerRater is the global
                                        standard for car dealership reviews and research — and more than 14 million
                                        consumers are exposed to our reviewers' content every month.</p>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div><!-- /header-text -->
        </div>
    </section>


    <!--section-->
    <section style="margin-top:-25px" class="sptb">
        <div class="container">
            <div class="section-title center-block text-center">
                <h2 style="margin-top:35px">Why Contact Us?</h2>

            </div>
            <div style="margin-bottom:65px" class="row ">
                <div class="col-md-6 col-lg-4 features">
                    <div class="card  box-shadow2">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-success mb-3">
                                    <i class="fa fa-shield  text-white"></i>
                                </div>
                                <h3 class="mt-2 mb-2">Best Security</h3>
                                <p class="text-muted mb-0">Dream Best Car Provide You Best Security</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 features">
                    <div class="card box-shadow2">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-primary mb-3">
                                    <i class="fa fa-headphones  text-white"></i>
                                </div>
                                <h3 class="mt-2 mb-2">24/7 Customer Support</h3>
                                <p class="text-muted mb-0">24/7 Customer Support Any Time</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 features">
                    <div class="card box-shadow2">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-secondary mb-3">
                                    <i class="fa fa-info  text-white"></i>
                                </div>
                                <h3>More Information</h3>
                                <p>Need more information contact us</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 features">
                    <div class="card mb-lg-0 box-shadow2">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-warning mb-3">
                                    <i class="fa fa-line-chart   text-white"></i>
                                </div>
                                <h3>Promote bussiness</h3>
                                <p>Dream best car help you promote bussiness </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 features">
                    <div class="card mb-lg-0 mb-md-0 box-shadow2">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-danger mb-3">
                                    <i class="fa fa-handshake-o   text-white"></i>
                                </div>
                                <h3>Dealer support</h3>
                                <p>Anytime dealer support</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4 features">
                    <div class="card mb-0 box-shadow2">
                        <div class="card-body text-center">
                            <div class="feature">
                                <div class="fa-stack fa-lg  fea-icon bg-info mb-3">
                                    <i class="fa fa-phone  text-white"></i>
                                </div>
                                <h3>24/7 Call Service</h3>
                                <p>Contact Dream best car for more update</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/section-->
@endsection

@push('js')
    <script>
        $(document).ready(function() {


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
                            $('#ContactModal').modal('hide');

                            $('#contact_button').text('Send Message');
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
