@extends('backend.admin.layouts.master')

@section('content')


<div class="row">
    <div class="col-md-12">
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Lead Details-Source: dreambestcar.com</h3>
                </div>
                <div class="card-body">

                    <div style="display:flex; margin:0 auto !important" class="row">
                        <div style="border:2px dotted black; border-radius:7px; margin-right:20px; padding:20px" class="col-md-5">
                           @php
                             $image_obj =  $lead->mainInventory->local_img_url;
                            $image_splice = explode(',',$image_obj);
                            $image = str_replace(["[", "'"], "", $image_splice[0]);
                           @endphp

                            <img src="{{ asset('frontend/') }}/{{$image}}" alt="img" class="mb-2" width="100%">
                            <h4>Vehicle Title : {{$lead->mainInventory->title ?? ''}}</h4>
                            <h5>Mileage : {{ number_format($lead->mainInventory->miles) ?? ''}}</h5>
                            <h5>Stock : {{$lead->mainInventory->stock ?? ''}}</h5>
                            <h5>Price : {{$lead->mainInventory->price_formate ?? ''}}</h5>
                            <h5>Trim : {{$lead->mainInventory->trim ?? ''}}</h5>
                            <hr>

                            <p>Trade In Information:</p>

                            <div class="row">
                                <div class="col-md-6">
                                    <p>Year : {{$lead->year ?? ''}}</p>
                                    <p>Make : {{$lead->make ?? ''}}</p>
                                    <p>Model : {{$lead->model ?? ''}}</p>
                                </div>
                                <div class="col-md-6">
                                    <p>Mileage : {{ number_format($lead->mileage) ?? ''}}</p>
                                    <p>Color : {{$lead->color ?? ''}}</p>
                                    <p>Vin : {{$lead->vin ?? ''}}</p>
                                </div>
                            </div>




                        </div>
                        <div style=" border:2px dotted black; border-radius:7px; margin-right:20px; padding:20px" class="col-md-5 cus-view">
                            <p>Dealer Information: </p>
                            <h5>Name : {{$lead->dealer->name ? $lead->dealer->name : 'N/A'}}</h5>
                            <h5>Email : {{$lead->dealer->email ? $lead->dealer->email : 'N/A'}}</h5>
                            <h5>Address : {{$lead->dealer->address ? $lead->dealer->address : 'N/A' }}</h5>
                            <h5>Zip code : {{$lead->dealer->zip ? $lead->dealer->zip : 'N/A'}}</h5>
                            <h5>Cell: {{ $lead->dealer->phone ? formatPhoneNumber($lead->dealer->phone) : 'N/A' }}</h5>
                            <hr>
                            <p>Buyer Information: </p>
                            @if (Auth::user()->hasAllaccess())
                            <h5>Name :  {{ $lead->customer->name ?? ''}}</h5>
                            <h5>Email : {{  $lead->customer->email  ?? ''}}</h5>
                            <h5>Cell : {{  formatPhoneNumber($lead->customer->phone ?? '')  ?? ''}}</h5>
                            @else
                            <h5>Name : {{ Auth::user()->package != '0' ? $lead->customer->name : '[hide]' }}</h5>
                            <h5>Email : {{ Auth::user()->package != '0' ? $lead->customer->email : '[hide]' }}</h5>
                            <h5>Cell : {{ Auth::user()->package != '0' ? formatPhoneNumber($lead->customer->phone) : '[hide]' }}</h5>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>


@endsection
