@extends('frontend.website.layout.app')

@section('meta_description', app('globalSeo')['description'])
@section('meta_keyword', app('globalSeo')['keyword'])
@section('title', 'Account | ' . app('globalSeo')['name'])
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
<style>
    .account-settings .user-profile {
        margin: 0 0 1rem 0;
        padding-bottom: 1rem;
        text-align: center;
    }

    .account-settings .user-profile .user-avatar {
        margin: 0 0 1rem 0;
    }

    .account-settings .user-profile .user-avatar img {
        width: 100px;
        height: 100px;
        border-radius: 100px;
    }

    .account-settings .user-profile h5.user-name {
        margin: 0 0 0.5rem 0;
    }

    .account-settings .user-profile h6.user-email {
        margin: 0;
        font-size: 0.8rem;
        font-weight: 400;
        color: gray;
    }

    .account-settings .about {
        margin: 2rem 0 0 0;
        text-align: center;
    }

    .account-settings .about h5 {
        margin: 0 0 15px 0;
        color: #007ae1;
    }

    .account-settings .about p {
        font-size: 0.825rem;
    }

    .form-control {
        border: 1px solid #cfd1d8;
        border-radius: 5px;
        font-size: 0.825rem;
        background: #ffffff;
        color: #2e323c;
    }

    .card {
        background: #ffffff;
        border-radius: 5px;
        border: 0;
        margin-bottom: 1rem;
    }
</style>

<div class="container account-data" style="margin-top:160px; margin-bottom:60px">
    <div class="row gutters message-load">


        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="row gutters">
                        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                            <h4 class="mb-5 ms-3">Listing Information</h4>
                        </div>
                        <form id="buyerlistingadd" action="{{ route('buyer.listing.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="text" placeholder="Make*" label="make" name="make"
                                        value="" />
                                    <span style="" class="text-danger error-message mb-2  ms-3" id="make-error"></span>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="text" placeholder="Model*" label="Model" name="model"
                                        value="" />
                                    <span style="" class="text-danger error-message mb-2  ms-3" id="model-error"></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <div style="width:95.8%; margin-left:12px" class="form-outline mb-4 p-0">
                                        <label class="form-label" for="promoCode">Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                $
                                            </span>
                                            <input type="text" name="price" class="form-control" placeholder="Price*" />



                                        </div>
                                        <span style="" class="text-danger error-message mb-2  ms-0"
                                            id="price-error"></span>
                                    </div>
                                </div>




                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="text" placeholder="Mileage*" label="Mileage"
                                        name="miles" value="" />
                                    <span style="" class="text-danger error-message mb-2  ms-3" id="miles-error"></span>
                                </div>

                            </div>


                            <div class="row">

                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="text" placeholder="VIN*" label="VIN" name="vin"
                                        value="" />
                                    <span style="" class="text-danger error-message mb-2  ms-3" id="vin-error"></span>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="text" placeholder="Color*" label="Color"
                                        name="exterior_color" value="" />
                                    <span style="" class="text-danger error-message mb-2  ms-3"
                                        id="exterior_color-error"></span>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="text" placeholder="Fuel Type*" label="Fuel Type"
                                        name="fuel" value="" />
                                    <span style="" class="text-danger error-message mb-2  ms-3" id="fuel-error"></span>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="text" placeholder="Drivetrain*" label="Drivetrain"
                                        name="drive_info" value="" />
                                    <span style="" class="text-danger error-message mb-2  ms-3"
                                        id="drive_info-error"></span>
                                </div>
                            </div>
                            <div class="row">

                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="text" placeholder="Transmission*" label="Transmission"
                                        name="transmission" value="" />
                                    <span style="" class="text-danger error-message mb-2  ms-3"
                                        id="transmission-error"></span>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <div style="width:95.8%; margin-left:12px" class="form-outline mb-4 p-0">
                                        <label for="images" class="form-label">Upload Multiple Images</label>
                                        <input class="form-control" type="file" id="imageUpload" name="img_from_url[]"
                                            multiple accept="image/*" />
                                        <span style="" class="text-danger error-message mb-2  ms-3"
                                            id="img_from_url-error"></span>
                                        <div id="preview"></div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-6">
                                    <x-frontend-user-input type="text" placeholder="Year*" label="Year" name="year"
                                        value="" />
                                    <span style="" class="text-danger error-message mb-2  ms-3" id="year-error"></span>
                                </div>


                            </div>




                            <div class="row gutters">
                                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                                    <x-primary-button class="ms-3" type="submit" id="listing_button"
                                        style="float:right; background:darkcyan; margin-top:20px; margin-bottom:0px">
                                        Submit
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

@endsection

@push('js')

<script>
    document.getElementById('imageUpload').addEventListener('change', function (event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('preview');

        Array.from(files).forEach(file => {
            const reader = new FileReader();

            reader.onload = function (e) {
                // Create a new container for each image and its remove button
                const imageContainer = document.createElement('div');
                imageContainer.style.display = 'inline-block';
                imageContainer.style.margin = '5px';

                // Create image element
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.margin = '5px';

                // Create remove button
                const removeButton = document.createElement('button');
                removeButton.textContent = 'Remove';
                removeButton.style.display = 'block';
                removeButton.style.marginTop = '5px';

                // Add remove button event listener
                removeButton.addEventListener('click', function () {
                    imageContainer.remove();
                });

                // Append image and remove button to the container
                imageContainer.appendChild(img);
                imageContainer.appendChild(removeButton);

                // Append container to the preview area
                previewContainer.appendChild(imageContainer);
            };

            // Read the file as a data URL
            reader.readAsDataURL(file);
        });
    });
</script>
<script>
    $(document).ready(function(){
        $.ajaxSetup({
         headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
       });

    $(document).on('submit','#buyerlistingadd', function (e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);
        $('#listing_button').text('Loading...');
    var form = this;
    $.ajax({
        processData: false,
        contentType: false,
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: formData,
        success: function (res) {
            console.log(res);
            $('.error-message').html('');
            if (res.errors) {
                $.each(res.errors, function (key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
                $('#listing_button').text('Submit');
            }

            if (res.status === 'success') {
                
                toastr.success(res.message);
                
                form.reset();
                const previewContainer = document.getElementById('preview');
        if (previewContainer) {
            previewContainer.innerHTML = ''; // Clear all image previews
        }
                $('#listing_button').text('Submit');
               
            }
        },
        error: function (xhr) {
            var errors = xhr.responseJSON.errors;
            $.each(errors, function (key, value) {
                $('#' + key + '-error').text(value[0]);
            });
            $('#listing_button').text('Submit');
        }
    });
});

})
</script>







@include('frontend.reapted_js')
@endpush