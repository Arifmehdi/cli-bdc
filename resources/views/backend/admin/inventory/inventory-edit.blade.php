@extends('backend.admin.layouts.master')

@section('content')
@php
    $colors = ['red', 'blue', 'green', 'yellow', 'purple', 'gray', 'beige', 'indigo', 'lavender', 'orange',
               'brown', 'cyan', 'maroon', 'lime', 'gold', 'olive', 'black', 'pink'];
@endphp
<style>
    .line_height
    {
        position: relative;
        line-height: 63px;
    }
    .heading_content h3::before {
    background-color: #242424;
    bottom: 6px;
    content: "";
    height: 1px;
    left: 0;
    margin: 0 auto;
    right: 0;
    position: absolute;
    width: 99px;
}
.heading_content h3::after {
    background-color: #242424;
    bottom: 0;
    content: "";
    height: 1px;
    left: 0;
    margin: 0 auto;
    position: absolute;
    right: 0;
    width: 59px;
}
</style>
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card shadow p-4">
                                @if (session()->has('message'))
                                    <h3 class="text-success">{{ session()->get('message') }}</h3>
                                @endif
                                <div class="card-header">
                                    <h4>Edit : {{ $inventory->title }}</h4>
                                </div>
                                <div class="card-body">
                                <form id="edit_from_submit" action="{{ route('admin.update.inventory')}}" method="POST">
                                        @csrf

                                        <input type="hidden" value="{{ $inventory->id }}" name="inventory_id">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="heading_content">
                                                    <h3 class="text-center fw-bold line_height">Basic Information</h3>
                                                </div>

                                                <table class="table table-striped mt-4">
                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Vin</label><br />
                                                                <input type="text" value="{{ $inventory->vin ?? '' }}"
                                                                    class="form-control" name="vin" disabled>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Engine</label><br />
                                                                <input type="text" value="{{ $inventory->engine_details ?? '' }}"
                                                                    class="form-control" name="engine_details" disabled>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Make</label><br />
                                                                <input type="text" value="{{ $inventory->make ?? '' }}"
                                                                    class="form-control" name="make">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Model</label><br />
                                                                <input type="text" value="{{ $inventory->model ?? '' }}"
                                                                    class="form-control" name="model">
                                                            </div>
                                                        </td>

                                                    </tr>

                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Model Year</label><br />
                                                                <input type="text" value="{{ $inventory->year ?? '' }}"
                                                                    class="form-control" name="year">
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Condition</label><br />
                                                                <input type="text" value="{{ $inventory->type ?? '' }}"
                                                                    class="form-control" name="condition">
                                                            </div>
                                                        </td>


                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Trim Package</label><br />
                                                                <input type="text" value="{{ $inventory->trim ?? '' }}"
                                                                    class="form-control" name="trim">
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Body Style</label><br />
                                                                <input type="text" value="{{ $inventory->body_formated ?? '' }}"
                                                                    class="form-control" name="body_formated">
                                                            </div>
                                                        </td>

                                                    </tr>


                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Transmission</label><br />
                                                                <input type="text" value="{{ $inventory->transmission ?? '' }}"
                                                                    class="form-control" name="transmission">
                                                            </div>
                                                        </td>

                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Drivetrain</label><br />
                                                                <input type="text" value="{{ $inventory->drive_info }}"
                                                                    class="form-control" name="drive_info">
                                                            </div>
                                                        </td>

                                                    </tr>


                                                    <tr>

                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">City MPG</label><br />
                                                                <input type="text" value="{{ $inventory->mpg_city }}"
                                                                    class="form-control" name="mpg_city">
                                                            </div>

                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Highway MPG</label><br />
                                                                <input type="text" value="{{ $inventory->mpg_highway }}"
                                                                    class="form-control" name="mpg_hwy">
                                                            </div>

                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-group">
                                                                <label for="">Fuel Type</label><br />
                                                                <input type="text" value="{{ $inventory->fuel ?? '' }}"
                                                                    class="form-control" name="fuel">
                                                            </div>

                                                        </td>

                                                    </tr>


                                                </table>
                                                <div class="heading_content">
                                                    <h3 class="text-center fw-bold line_height mt-3">Standard Information</h3>
                                                </div>

                                                <div class="form-group">
                                                    <label for="">Mileage <span style="color:red">*</span></label><br />
                                                    <input type="text" value="{{ $inventory->miles }}"
                                                        class="form-control" name="miles">

                                                </div>
                                                <div class="form-group">
                                                    <label for="">Stock Number <span style="color:red">*</span></label>
                                                    <input type="text" value="{{ $inventory->stock }}"
                                                        class="form-control" name="stock">
                                                </div>

                                                <div class="form-group">
                                                    <label for="">Price <span style="color:red">*</span></label>
                                                    <input type="text" value="{{ $inventory->price }}"
                                                        class="form-control" name="price">

                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="">Purchase Price</label>
                                                            <input type="text" value="{{ $inventory->purchase_price }}"
                                                                class="form-control" name="purchase_price">
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <label for="">Purchase Date</label>
                                                        <input type="date" value="{{ $inventory->stock_date_formated }}"
                                                            class="form-control" name="purchase_date">
                                                    </div>

                                                </div>

                                                <div class="heading_content">
                                                    <h3 class="text-center line_height mt-3 mb-3"> Color Information</h3>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <div id="container" style="height:16px;width:50%;margin-top: 50px;position: absolute;background-color: white;">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">

                                                                    <label for="">Exterior Color</label>
                                                                    <select id="colorlist" name="exterior_color" class="form-control">
                                                                        <option value="">Select Color</option>
                                                                        @foreach ($colors as $color)
                                                                            <option value="{{ $color }}" {{ $inventory->exterior_color == $color ? 'selected' : '' }}>
                                                                                {{ ucfirst($color) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="">Exterior Color Description</label>
                                                            <input type="text"
                                                                value="{{ $inventory->exterior_description }}"
                                                                class="form-control" name="exterior_description">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="row">
                                                            <div class="col-md-1">
                                                                <div id="interior_container"
                                                                    style="height:16px;width:50%;margin-top: 50px;
                                        position: absolute;
                                        background-color: white;">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="">Interior Color</label>
                                                                    <select id="colorlist" name="interior_color" class="form-control">
                                                                        <option value="">Select Color</option>
                                                                        @foreach ($colors as $color)
                                                                            <option value="{{ $color }}" {{ $inventory->interior_color == $color ? 'selected' : '' }}>
                                                                                {{ ucfirst($color) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="">Interior Color Description</label>
                                                            <input type="text"
                                                                value="{{ $inventory->interior_description }}"
                                                                class="form-control" name="interior_description">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="">Description</label>
                                                            <textarea class="form-control" name="description" id="" cols="30" rows="10">{{ $inventory->vehicle_feature_description }}</textarea>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="heading_content">
                                                    <h3 class="text-center fw-bold line_height">Inventory Image</h3>
                                                </div>
                                                <div class="row mb-2 mt-4">
                                                    @php
                                                        $images = [];
                                                        // Process each element in the $all_images array
                                                        foreach ($all_images as $image) {
                                                            // Remove square brackets and single quotes, then trim spaces from each element
                                                            $images[] = trim(str_replace(['[', "'", ']'], '', $image));
                                                        }
                                                    @endphp

                                                    @foreach ($images as $img)
                                                        <div class="col-md-3">
                                                            <div class="card card-body shadow"
                                                                style="padding: 0px; margin:0px;margin-bottom:10px">
                                                                <a href="javascript:void(0)">
                                                                    <img alt=""
                                                                        src="{{ asset($img) }}"
                                                                        width="100%" />
                                                                </a>

                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary float-right">Save changes</button>
                                </div>

                                </form>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('submit', '#edit_from_submit', function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                $.ajax({
                    processData: false,
                    contentType: false,
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    success: function(res) {
                        console.log(res);

                        $('.error-message').html('');

                        if (res.errors) {
                            // Display validation errors dynamically
                            $.each(res.errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                        }

                        if (res.status === 'success') {

                            toastr.success(res.message);


                        }
                    },

                    error: function(xhr) {
                        // Handle error response
                        var errors = xhr.responseJSON.errors;

                        // Display validation errors dynamically
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    }
                });
            });
        })

    </script>
@endpush
