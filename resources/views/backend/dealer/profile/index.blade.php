@php
use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')
@push('css')

<style>
    .change-password {
        display: inline-block;
        font-weight: bold;
        font-size: 20px;
        text-decoration: none;
        color: #1eb300; /* A modern blue color */
        padding: 5px 10px;
        border: 2px solid #0056b3;
        border-radius: 25px; /* Rounded corners */
        transition: all 0.3s ease; /* Smooth hover effect */
    }

    .change-password:hover {
        background-color: #1eb300; /* Blue background on hover */
        color: #fff; /* White text on hover */
        text-decoration: none; /* No underline on hover */
        transform: scale(1.1); /* Slight zoom on hover */
    }

    .change-password i {
        margin-right: 8px; /* Space between icon and text */
    }
</style>
@endpush
@section('content')

<div class="row">

    <div class="col-md-12">
        @if (session()->has('message'))
        <div class="alert alert-success" role="alert">
            {{ session()->get('message') }}
            </div>
        @endif
        <!-- Main content -->
        <section class="content row">


            <div class="col-md-3">
                <div class="dealer-image" style="text-align:center">
                    <img src="@if ($user->image)
                    {{ asset('frontend/assets/images/' . $user->image) }}
                 @else
                    {{ asset('frontend/assets/images/profile.png') }}
                 @endif" class="img-circle elevation-2" alt="User Image" width="300" height="300">

                </div>

                <div class="password" style="text-align:center; margin-top:36px;">
                    <a href="#" class="change-password" data-toggle="modal" data-target="#changePasswordModal">
                        <i class="fas fa-key"></i> Change Password
                    </a>
                </div>



            </div>
            <div class="col-md-9">

                    <div class="card card-body">
                        <form  action="{{ route('dealer.profile.update')}}" method="post" enctype="multipart/form-data">
                            @csrf
                        <!-- Name Field -->
                        <div class="form-group">
                            <label for="name">Name <span style="color: red;font-weight:bold">*</span></label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Enter Your Name" value="{{ $user->name ?? '' }}">
                            <span class="text-danger error-message" id="user_name-error"></span>
                        </div>
                        <!-- Email Field -->
                        <div class="form-group">
                            <label for="email">Email <span style="color: red;font-weight:bold">*</span></label>
                            <input type="email" class="form-control" name="email" id="userEmail" placeholder="Enter User E-mail" value="{{ $user->email ?? '' }}">
                            <span class=" text-danger error-message" id="email-error"></span>
                        </div>
                        <!-- Address Field -->
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" class="form-control" name="address" placeholder="Enter Your Address" value="{{ $user->address ?? '' }}">
                            <span class=" text-danger error-message" id="address-error"></span>
                        </div>
                        <!-- Phone Field -->
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="number" class="form-control" name="phone" id="phone" placeholder="Enter Your Phone" value="{{ $user->phone ?? '' }}">
                        </div>

                        <!-- Gender, City, and Zip Fields -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select name="gender" class="form-control">
                                        <option value="">Select gender</option>
                                        <option value="Male" {{ ($user->gender == 'Male') ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ ($user->gender == 'Female') ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ ($user->gender == 'Other') ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" name="city" value="{{ $user->city ?? '' }}" class="form-control" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="zip">Zip</label>
                                <input type="text" class="form-control" name="zip" value="{{ $user->zip ?? '' }}" placeholder="Enter Your Zip">
                            </div>
                        </div>

                        <!-- Image Upload Field -->
                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" name="image" id="image">
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn  btn-success float-right">Submit</button>
                        </div>
                    </form>


                    </div>



            </div>

        </section>
    </div>
</div>

{{-- change password modal --}}

<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Password Change Form -->
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="currentPassword" name="current_password" placeholder="Type Current Password">
                        <span class="text-danger error-message" id="current_password-error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="newPassword" name="new_password" placeholder="Type New Password">
                        <span class="text-danger error-message" id="new_password-error"></span>
                    </div>

                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirmPassword" name="new_password_confirmation" placeholder="Confirm New Password">
                        <span class="text-danger error-message" id="new_password_confirmation-error"></span>
                    </div>

                    <button type="submit" class="btn btn-success float-right" id="changeBtn">Submit</button>
                </form>

                <div id="passwordChangeAlert" class="mt-3"></div>
            </div>
        </div>
    </div>
</div>


 @endsection

 @push('js')
 <script>
  $(document).ready(function () {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#changePasswordForm').on('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        // Clear previous error messages
        $('.error-message').text('');

        $('#changeBtn').text('Loading...');
        // AJAX request
        $.ajax({
            url: "{{ route('change-password') }}",
            method: "POST",
            data: $(this).serialize(),
            success: function (res) {
                if (res.status === 'success') {
                    toastr.success(res.message); // Display success message
                    $('#changePasswordModal').modal('hide'); // Hide the modal
                    $('#changePasswordForm')[0].reset(); // Reset the form
                    $('#changeBtn').text('Submit');
                }
            },
            error: function (xhr) {
                $('#changeBtn').text('Submit');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, value) {
                        $('#' + key + '-error').text(value[0]);
                    });
                } else if (xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            }
        });
    });
});


 </script>

 @endpush
