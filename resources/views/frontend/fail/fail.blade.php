@extends('frontend.website.layout.app')


@section('content')
    <!--news details-->
    <section class="sptb">
        <div style="margin-top:110px" class="container success-article">
            <div class="row">
                <div style="margin-bottom:45px" class="col-xl-12 col-lg-12 col-md-12">
                    <div style="background: white; box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px; border-radius:15px;; width:100%; overflow:hidden"
                        class="p-4 fail-all">
                        <div style="margin: 0 auto; width:40%" class="fail-content">
                            <img style="margin-top:-100px;" width="100%" height="450px"
                                src="{{ asset('/frontend/assets/images/fail.jpg') }}" />

                            <h3 style="text-align:center; margin-top:-100px" class="">Payment Fail</h3>
                            <p style="text-align:center">Try again later</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Add details-->
@endsection
