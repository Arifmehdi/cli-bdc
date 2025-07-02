@extends('frontend.website.layout.app')

@push('css')
<style>
    .page_404 {
        padding: 40px 0;
        /* background: black; */
        font-family: 'Arvo', serif;
        color: white;
        text-align: center;
        margin-top: 50px;
    }

    .warning-message {
        background: black;
        color: white;
        /* background: #ffcc00;
        color: black; */
        font-size: 24px;
        font-weight: bold;
        padding: 15px;
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        max-width: 600px;
        margin: 0 auto;
    }

    .warning-icon {
        font-size: 30px;
    }

    /* Media query for PC or laptop screens */
    @media (min-width: 768px) {
        .warning-message {
            margin-bottom: 200px; /* Add 200px space after the warning message */
        }
    }
</style>
@endpush

@section('content')

<div class="container mt-4 mb-5">
    <section class="page_404">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div style="margin: 0 auto" class="col-sm-10 col-sm-offset-1 text-center">
                        <!-- <div class="four_zero_four_bg">
                            <h1 class="text-center text-white">404</h1>
                        </div> -->

                        <div class="four_zero_four_bg">
                            <div class="warning-message">
                                <span class="warning-icon">⚠️</span>
                                No longer listed. Sorry, this vehicle is no longer available.
                                <span class="warning-icon">⚠️</span>
                            </div>

                            <a href="{{ route('home')}}" class="link_404 mt-3" style="border-bottom:1px solid">Go to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

