@extends('frontend.userdashboard.master')
@section('content-user')
<div class="card mb-0">
    <div style="background:white" class="card-header">
        <h3 class="card-title">Edit Profile</h3>
    </div>
    <div class="card-body mt-3">
        <form action="{{ route('buyer.profile.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
        <div class="row">
            <x-frontend-user-input type="text" placeholder="First Name" label="First Name" name="fname" value="{{$user->fname ? $user->fname : ''  }}"/>

            <x-frontend-user-input type="text" placeholder="Last Name" label="Last Name" name="lname" value="{{$user->lname ? $user->lname : ''  }}"/>

            <x-frontend-user-input type="email" placeholder="Email" label="Email address" name="email" value="{{$user->email ? $user->email : ''  }}"/>

            <x-frontend-user-input type="tel" placeholder="Cell Number" label="Cell Number" name="phone" value="{{$user->phone ? $user->phone : ''  }}"/>
            <x-frontend-user-input type="text" placeholder=" Address" label="Address" class="col-md-12" name="address" value="{{$user->address ? $user->address : ''  }}"/>
            <x-frontend-user-input type="text" placeholder=" City" label="City" class="col-sm-6 col-md-4" name="city" value="{{$user->city ? $user->city : ''  }}"/>
            <x-frontend-user-input type="number" placeholder="ZIP Code" label="Postal Code" class="col-sm-6 col-md-3" name="zip" value="{{$user->zip ? $user->zip : ''  }}"/>
            <x-frontend-user-select  label="Country" name="country" value="{{$user->country ? $user->country : ''}}"/>
            <x-frontend-user-input type="text" placeholder="Enter facebook link" label="Facebook" name="facebook" value="{{$user->facebook ? $user->facebook : ''  }}"/>
            <x-frontend-user-input type="text" placeholder="Enter google link" label="Google" name="google" value="{{$user->google ? $user->google : ''  }}"/>
            <x-frontend-user-input type="text" placeholder="Enter twitter link" label="Twitter" name="twitter" value="{{$user->twitter ? $user->twitter : ''  }}"/>
            <x-frontend-user-input type="text" placeholder="Enter pinterest link" label="Pinterest" name="pinterest" value="{{$user->pinterest ? $user->pinterest : ''  }}"/>
            <x-textarea type="text" rows="5" placeholder="Enter About your description" label="About Me" name="about_me" value="{{$user->about_me ? $user->about_me : ''  }}"/>
           <x-frontend-user-input type="file" label="Upload Image" name="image"/>
        </div>
    </div>
    <div class="card-footer">
        <x-primary-button style="float:right; background:darkcyan">
            Updated Profile
        </x-primary-button>
    </div>
</form>
</div>
@endsection


