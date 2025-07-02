@extends('frontend.website.layout.app')


@section('content')
    <!--news details-->
    <section class="sptb">
        <div style="margin-top:115px" class="container success-article">
            <div class="row">
                <div style="margin-bottom:45px" class="col-xl-7 col-lg-7 col-md-12">
                    <div style="margin-top:105px">
                        <h2 style="font-weight:600" class="mb-2">Payment Confirmed</h2>
                        <h2 style="font-weight:600;">Successfully!</h2>
                        <p style="width:85%" class="mt-5">Thank you for your purchase. Your payment has been processed
                            successfully. You will receive a confirmation email with your order details shortly. If you have
                            any questions, please contact our support team.</p>

                        <a href="" style=" text-decoration: underline; color:rgb(8, 120, 128)">Go back to home</a>
                    </div>
                </div>

                <!--Rightside Content-->
                <div class="col-xl-5 col-lg-5 col-md-12 mb-5">

                    <div style="background: white; box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px; border-radius:15px;"
                        class="p-4">
                        <div class="d-flex justify-content-between">
                            <div style="margin-top:26px" class="ps-5">
                                <h2 style="font-weight:600; font-size:34px" class="m-0">$500</h2>
                                <p class="mt-1">Payment Success!</p>
                            </div>
                            <img class="success-image" width="25%" class="pe-5 mt-4 mb-4"
                                src="{{ asset('/frontend/assets/images/check.png') }}" />
                        </div>

                    </div>
                    <div style="background: white; box-shadow: rgba(17, 17, 26, 0.1) 0px 0px 16px; border-radius:15px;"
                        class="p-4 mt-5">
                        <div class="ps-5">
                            <h4 style="font-weight:600" class="mt-3 fs-5">Payment Details</h4>
                            <div style="margin-top:37px" class="d-flex justify-content-between">
                                <p>Date</p>
                                <p style="font-weight:600">14 july 2024</p>

                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <p>Amount</p>
                                <p style="font-weight:600">$500</p>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <p>Refarance Number</p>
                                <p style="font-weight:600">1025f#d&k55</p>
                            </div>
                            <div class="d-flex justify-content-between mt-4">
                                <p>Payment Status</p>
                                <p style="font-weight:600">Success</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/Rightside Content-->
            </div>
        </div>
    </section>
    <!--/Add details-->
@endsection
