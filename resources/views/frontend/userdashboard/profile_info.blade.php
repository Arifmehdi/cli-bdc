@extends('frontend.userdashboard.master')
@section('content-user')
<div class="card mb-0">
    <div style="background:white" class="card-header">
        <span style="font-size:17px" class="">Profile Information</span>
    </div>

    <div class="card-body">
        <h4 class="profile-info-title">Manage Your Name, ID And Email Addresses.
            </h4>
            <p >Below are the name and email addresses on file for your account.</p>
            <div class="row ">
                <div class="col-md-12 mt-5">
                    <div class="row">

                        <!-- Name -->
                        <div class="col-sm-12 col-md-12 mb-3 info-item">
                            <div class="d-flex justify-content-between">
                                <h5 class="info-label">Name:</h5>
                                <p class="info-value">{{$user_profile->name ? $user_profile->name : ''}}</p>
                            </div>
                        </div>
                        <hr style="width:97%" class="profine-info-line">
                        {{-- <!-- gender -->
                        <div class="col-sm-12 col-md-12 mb-3 info-item">
                            <div class="d-flex justify-content-between">
                                <h5 class="info-label">Gender:</h5>
                                <p class="info-value">{{$user_profile->gender ? $user_profile->gender : ''}}</p>
                            </div>
                        </div>
                        <hr> --}}

                        <!-- Email Address -->
                        <div class="col-sm-12 col-md-12 mb-3 info-item">
                            <div class="d-flex justify-content-between">
                                <h5 class="info-label">Email Address:</h5>
                                <p class="info-value">{{$user_profile->email ? $user_profile->email : ''}}</p>
                            </div>
                        </div>
                        <hr style="width:97%">

                        <!-- Cell Number -->
                        <div class="col-sm-12 col-md-12 mb-3 info-item">
                            <div class="d-flex justify-content-between">
                                <h5 class="info-label">Cell Number:</h5>
                                <p class="info-value">{{$user_profile->phone ? $user_profile->phone : ''}}</p>
                            </div>
                        </div>
                        <hr style="width:97%">

                        <!-- Address -->
                        <div class="col-sm-12 col-md-12 mb-3 info-item">
                            <div class="d-flex justify-content-between">
                                <h5 class="info-label">Address:</h5>
                                <p class="info-value">{{$user_profile->address ? $user_profile->address : ''}}</p>
                            </div>
                        </div>
                        <hr style="width:97%">

                    </div>
                </div>
            </div>
    </div>

</div>
@endsection


