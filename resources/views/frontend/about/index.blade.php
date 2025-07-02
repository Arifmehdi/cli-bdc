@extends('frontend.website.layout.app')
@foreach(app('globalStaticPage') as $page)
@if ($page->slug == 'about-us')
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

@section('content')

	<!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3 sptb-2" data-image-src="{{asset('frontend/assets')}}/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h1 class="">About Us</h1>

                        <ol class="breadcrumb text-center">
                            <li   class=""><a style="color:white" href="{{ route('home') }}">Home<span style="margin-left:4px; margin-right:4px;">/</span> </a></li>
                            <li  class=""><a style="color:white" href="javascript:void(0);">About Us</a></li>

                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->


		<!--section-->
		<section class="sptb">
			<div class="container">
				<div class="text-justify">
					<h2 class="mb-4">Why Best Dream car?</h2>
					<h4 class="leading-Automatic">

                    </h4>
					<p class="leading-Automatic">bestdreamcar.com is an independent company that works side by side with consumers, sellers, and dealers for transparency and fairness in the marketplace. Best Dream car does not have the complete history of every vehicle. Use the Best Dream car search as one important tool, along with a vehicle inspection and test drive, to make a better decision about your next used car.</p>
                    <p class="leading-Automatic mb-0">To search for vehicles fitting your exact specifications, try our Advanced Search! On this page, you can focus on specific vehicle details—age, mileage, features, etc.—to ensure that your search results show exactly what you’re looking for..</p>
                    <br>
					<p class="leading-Automatic">You can locate your vehicle by performing a search on our website. It is best practice to limit the mileage in your search as most of our consumers do not choose 'All Miles' when searching for a vehicle. Listings show live within 24 hours of approval..
                    </p>

				</div>
			</div>
		</section>
		<!--/section-->
	<!--Statistics-->
    <section>
        <div class="about-1 cover-image sptb bg-background-color" data-image-src="{{asset('frontend/assets')}}/images/banners/banner2.jpg">
            <div class="content-text mb-0 text-white info">
                <div class="container">
                    <div class="row text-center">
                        <div class="col-lg-3 col-md-6">
                            <div class="counter-status md-mb-0">
                                <div class="counter-icon">
                                    <i class="ti-user"></i>
                                </div>
                                <h5>Customers</h5>
                                <h2 class="counter mb-0">1500</h2>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="counter-status status-1 md-mb-0">
                                <div class="counter-icon text-warning">
                                    <i class="ti-car"></i>
                                </div>
                                <h5>Car Sales</h5>
                                <h2 class="counter mb-0">100</h2>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="counter-status status md-mb-0">
                                <div class="counter-icon text-primary">
                                    <i class="ti-package"></i>
                                </div>
                                <h5>Rented Cars</h5>
                                <h2 class="counter mb-0">120</h2>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="counter-status status">
                                <div class="counter-icon text-success">
                                    <i class="ti-face-smile"></i>
                                </div>
                                <h5>Happy Customers</h5>
                                <h2 class="counter mb-0">1000</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Statistics-->
		<!--How to work-->
		<section class="sptb bg-white">
			<div class="container">
				<div class="section-title center-block text-center">
					<h2>How It Works?</h2>
					<p>Best Dream car provides a space for vehicle sellers and prospective buyers to get result</p>
				</div>
				<div class="row">
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="">
							<div class="mb-lg-0 mb-4">
								<div class="service-card text-center">
									<div class="bg-light icon-bg icon-service text-purple about box-shadow2">
										<img src="{{asset('frontend/assets')}}/images/products/about/employees.png" alt="img">
									</div>
									<div class="servic-data mt-3">
										<h4 class="font-weight-semibold mb-2">Register</h4>
										<p class="text-muted mb-0">At first register your account</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="">
							<div class="mb-lg-0 mb-4">
								<div class="service-card text-center">
									<div class="bg-light icon-bg icon-service text-purple about box-shadow2">
										<img src="{{asset('frontend/assets')}}/images/products/about/megaphone.png" alt="img">
									</div>
									<div class="servic-data mt-3">
										<h4 class="font-weight-semibold mb-2">Create Account</h4>
										<p class="text-muted mb-0">Create your account for more result</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="">
							<div class="mb-sm-0 mb-4">
								<div class="service-card text-center">
									<div class="bg-light icon-bg icon-service text-purple about box-shadow2">
										<img src="{{asset('frontend/assets')}}/images/products/about/pencil.png" alt="img">
									</div>
									<div class="servic-data mt-3">
										<h4 class="font-weight-semibold mb-2">Add Posts</h4>
										<p class="text-muted mb-0">Post your ad easily anytime</p>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-3 col-md-6 col-sm-6">
						<div class="">
							<div class="">
								<div class="service-card text-center">
									<div class="bg-light icon-bg icon-service text-purple about box-shadow2">
										<img src="{{asset('frontend/assets')}}/images/products/about/coins.png" alt="img">
									</div>
									<div class="servic-data mt-3">
										<h4 class="font-weight-semibold mb-2">Get Earnings</h4>
										<p class="text-muted mb-0">Start Earning here easily</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!--/How to work-->


		<!--Section-->
		<section>
			<div class="cover-image sptb bg-background-color" data-image-src="{{asset('frontend/assets')}}/images/banners/banner4.jpg">
				<div class="content-text mb-0">
					<div class="container">
						<div class="text-center text-white ">
							<h2 class="mb-2 display-5">Are you ready for the posting you ads on this Site?</h2>
							<p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.</p>
							<div class="mt-5">
								<a style="background:darkcyan; color:white" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn  btn-pill">Free Post Ad</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!--/Section-->

		<!--section-->
		<section class="sptb">
			<div class="container">
				<div class="section-title center-block text-center">
					<h2>Why Choose Us?</h2>
					<p>Six step follow for justify Best Dream car</p>
				</div>
				<div class="row ">
					<div class="col-md-6 col-lg-4 features">
						<div class="card  box-shadow2">
							<div class="card-body text-center">
								<div class="feature">
									<div class="fa-stack fa-lg  fea-icon bg-success mb-3">
										<i class="fa fa-bullhorn  text-white"></i>
									</div>
									<h3>Provide Free Ads</h3>
									<p>Dream best provide Free Ads.</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-lg-4 features">
						<div class="card box-shadow2">
							<div class="card-body text-center">
								<div class="feature">
									<div class="fa-stack fa-lg  fea-icon bg-primary mb-3">
										<i class="fa fa-heart  text-white"></i>
									</div>
									<h3>Best Ad Ratings</h3>
									<p>Best ad ratings anytime anywhere</p>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-6 col-lg-4 features">
						<div class="card box-shadow2">
							<div class="card-body text-center">
								<div class="feature">
									<div class="fa-stack fa-lg  fea-icon bg-secondary mb-3">
										<i class="fa fa-bookmark  text-white"></i>
									</div>
									<h3>Provide Post Features</h3>
									<p>Best Dream car provide post features for you</p>
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
									<h3>See  your Ad Progress</h3>
									<p>Provide Ad progress for grow your bussiness </p>
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
									<h3>User Friendly</h3>
									<p> User riendly experience both web and mobile</p>
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
									<h3>24/7 Support</h3>
									<p>Best Dream car provide 24/7 support</p>
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

@endpush
