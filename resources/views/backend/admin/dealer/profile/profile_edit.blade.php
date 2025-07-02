<form action="{{route('admin.user.update')}}" method="post" id="userUpdate" enctype="multipart/form-data">
    @csrf
<ul class="list-group list-group-unbordered mb-3">
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <b>Name</b>
        <input type="text" class="form-control w-auto" name="up_user_name" value="{{ $user->name ?? '' }}">
        <input type="hidden" class="form-control w-auto" name="up_user_role" value="{{ $user->roles->first()->name ?? '' }}">
        <input type="hidden" class="form-control w-auto" name="up_user_id" value="{{ $user->id ?? '' }}">
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <b>Email</b>
        <input type="text" class="form-control w-auto" name="up_user_email" value="{{ $user->email ?? '' }}">
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <b>Gender</b>
        <input type="text" class="form-control w-auto" name="gender" value="{{ $user->gender ?? '' }}">
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <b>Address</b>
        <input type="text" class="form-control w-auto" name="up_user_address" value="{{ $user->address ?? '' }}">
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <b>Cell</b>
        <input type="text" class="form-control w-auto telephoneInput" name="up_user_phone" value="{{ $user->phone ?? '' }}">
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <b>City</b>
        <input type="text" class="form-control w-auto" name="city" value="{{ $user->city ?? '' }}">
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <b>State</b>
        <input type="text" class="form-control w-auto" name="state" value="{{ $user->state ?? '' }}">
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <b>Zip</b>
        <input type="text" class="form-control w-auto" name="zip" value="{{ $user->zip ?? '' }}">
    </li>
    <li class="list-group-item d-flex justify-content-between align-items-center">
        <b>Member Since</b>
        <input type="text" disabled class="form-control w-auto" value="{{ \Carbon\Carbon::parse($user->created_at)->diffForHumans() ?? 'Null' }}">
    </li>
<li class="list-group-item d-flex justify-content-between align-items-center">
        <b>Image</b>
        <input type="file" class="form-control w-auto" name="image" id="profile_image">
    </li>

    <li class="list-group-item ">
        <button class="btn btn-success" style="float: right">Update</button>
    </li>

</ul>

</form>
<hr>
