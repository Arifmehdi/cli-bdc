@extends('frontend.userdashboard.master')
@section('content-user')

<div class="card mb-0">



    <div class="card-body">
        <div class="row">
            <div style="margin: 0 auto"  class="col-md-10 col-sm-12">
                <div  class="row">

                    <div class="col-sm-6 col-md-6">
                        <div class="card" style="width: 18rem;">

                            <div style="background: rgb(4, 45, 65); border-radius:7px; border-bottom:3px solid rgb(172, 5, 5)" class="card-body">
                                @php
                                $favoriteCount = \App\Models\Favourite::where('user_id', Auth::id())->count();
                                 @endphp
                              <h5 style="color:white" class="card-title">{{$favoriteCount}}</h5>
                              <h6 style="color:white">FAVORITES</h6>

                            </div>
                          </div>
                    </div>
                    <div class="col-sm-6 col-md-6">
                        <div class="card" style="width: 18rem;">

                            <div style="background: rgb(5, 82, 82); border-radius:7px; border-bottom:3px solid rgb(6, 180, 6)" class="card-body">

                                @php
                                $total_mess_Count = \App\Models\Message::where('is_seen', 0)->count();
                                $mCount = $total_mess_Count > 0 ? $total_mess_Count - 1 : 0;
                                 @endphp
                              <h5 style="color:white" class="card-title">{{ $mCount}}</h5>
                              <h6 style="color:white">MESSAGE</h6>

                            </div>
                          </div>
                    </div>
                </div>


            </div>



        </div>
    </div>

</div>
@endsection


