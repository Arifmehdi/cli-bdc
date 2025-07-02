@php
use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')
@push('css')


@endpush
@section('content')
<div class="row">
    <div class="col-md-12">



{{-- logo edit modal start --}}
<!-- Modal -->
<div class="modal fade" id="editLogoModel" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">General Setting </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        </div>
        <div class="modal-body">
            <form action="{{route('admin.setting.update')}}" id="updateGeneralForm" method="POST"  enctype="multipart/form-data">
                @csrf

                <input type="hidden" id="setting_id" name="setting_id"/>
                <div class="row">
                    <div class="col-md-6">

                        <table  cellpadding="10">
                            <tr>
                                <td>Site Title </td>
                                <td>
                                    <input  type="text" name="site_title" id="site_title" class="form-control" >

                                </td>
                            </tr>
                            <tr>
                                <td>Site Logo </td>

                                <td>
                                    <span>Logo Size (140x40)</span>
                                    <input type="file" name="image" id="image" class="form-control">
                                    <img width="80px"  height="50px" src="" alt="" class="imagePreview" style="margin-top:5px">
                                    <div id="imagePreviewContainer">
                                        <img width="80px"  height="50px" id="imagePreview" style="display: none; margin-top:5px">
                                        <a id="removeImageButton" style="display: none;
                                        color:red; margin-top:5px; cursor:pointer">x</a>
                                    </div>
                                </td>

                            </tr>
                            <tr>
                                <td>Slider</td>

                                <td>
                                    <span>slider Size (1600x600)</span>
                                    <input type="file" name="slider_image" id="slider_image" class="form-control">
                                    <img width="80px"  height="50px" src="" alt="" class="sliderimagePreview" style="margin-top:5px">
                                    <div id="imagePreviewContainerSlider
                                    ">
                                        <img width="80px"  height="50px" id="SimagePreview" style="display: none; margin-top:5px">
                                        <a id="sliderremoveImageButton" style="display: none;
                                        color:red; margin-top:5px; cursor:pointer">x</a>
                                    </div>
                                </td>

                            </tr>
                            <tr>
                                <td>Slider Title </td>
                                <td>
                                    <input type="text" name="slider_title" id="slider_title" class="form-control" >

                                </td>
                            </tr>
                            <tr>
                                <td>Slider SubTitle </td>
                                <td>
                                    <input type="text" name="slider_subtitle" id="slider_subtitle" class="form-control" >

                                </td>
                            </tr>
                            <tr>
                                <td>Favicon </td>
                                <td>
                                    <span>Favicon Size (40x25)</span>
                                    <input type="file" name="fav_image" id="fav_image" class="form-control" >

                                    <img width="80px"  height="50px" src="" alt="" class="favimagePreview" style="margin-top:5px">


                                    <div id="imagePreviewContainerFavicon">
                                        <img width="80px" height="50px" id="faviconImagePreview" style="display: none; margin-top:5px">
                                        <a id="removeImageButtonFavicon" style="display: none;
                                        color:red; cursor:pointer">x</a>
                                    </div>

                                </td>
                            </tr>
                            </tr>
                            <tr>
                                <td>Pagination </td>
                                <td>
                                    <input type="text" name="pagination" id="pagination" class="form-control" >

                                </td>
                            </tr>

                            <tr>
                                <td>Email </td>
                                <td>
                                    <input type="text" name="email" id="email" class="form-control" >

                                </td>
                            </tr>
                            <tr>
                                <td>Phone </td>
                                <td>
                                    <input type="text" name="phone" id="telephoneInput" class="form-control" >
                                </td>
                            </tr>



                        </table>

                    </div>
                    <div class="col-md-6">

                        <table  cellpadding="10">

                            <tr>
                                <td>Site Map </td>
                                <td>
                                    <input type="text" name="site_map" id="site_map" class="form-control" >
                                </td>
                            </tr>
                            <tr>
                                <td>Language </td>
                                <td>
                                    <input type="text" name="language" id="language" class="form-control" >
                                </td>
                            </tr>
                            <tr>
                                <td>Separator </td>
                                <td>
                                    <input type="text" name="separator" id="separator" class="form-control" >
                                </td>
                            </tr>
                            <tr>
                                <td>Timezone </td>
                                <td>
                                    <select style="width:100%" name="timezone" id="timezone" class="form-control">
                                        <option value="UTC-6">UTC-6</option>

                                      </select>

                                </td>
                            </tr>
                            <tr style="position: relative">
                                <td style="position: absolute; top:0; left:0">Date Format </td>
                                <td>
                                    <div style="margin-left:55px">
                                        <div  class="form-check">
                                            <input name="date_formate" class="form-check-input" value="F j, Y" type="radio" value="" id="date_formate">
                                            <label class="form-check-label" for="date_formate">
                                              April 1, 2024
                                            </label>
                                            <input style="width:25%; height:20px; border-radius:5px; margin-left:25px;
                                            background:rgb(228, 226, 226); border: 1px solid rgb(250, 249, 249); padding:3px; font-size:15px; text-align:center" type="text" value="F j, Y" disabled/>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="date_formate" value="Y-m-d" id="flexRadioDefault2" checked>
                                            <label class="form-check-label" for="flexRadioDefault2">
                                              2024-04-01
                                            </label>
                                            <input style="width:25%; height:20px; border-radius:5px; margin-left:32px;
                                            background:rgb(228, 226, 226); border: 1px solid rgb(250, 249, 249); padding:3px; font-size:15px; text-align:center" type="text" value="Y-m-d" disabled/>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="date_formate" value="m/d/Y" id="flexRadioDefault3" checked>
                                            <label class="form-check-label" for="flexRadioDefault3">
                                              04/01/2024
                                            </label>
                                            <input style="width:25%; height:20px; border-radius:5px; margin-left:32px;
                                            background:rgb(228, 226, 226); border: 1px solid rgb(250, 249, 249); padding:3px; font-size:15px; text-align:center" type="text" value="m/d/Y" disabled/>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="date_formate" value="d/m/Y" id="flexRadioDefault4">
                                            <label class="form-check-label" for="flexRadioDefault4">
                                             01/04/2024
                                            </label>
                                            <input style="width:25%; height:20px; border-radius:5px; margin-left:32px;
                                            background:rgb(228, 226, 226); border: 1px solid rgb(250, 249, 249); padding:3px; font-size:15px; text-align:center" type="text" value="d/m/Y" disabled/>
                                          </div>

                                    </div>


                                </td>
                            </tr>
                            <tr style="position: relative">
                                <td style="position: absolute; top:0; left:0">Time Format </td>
                                <td>
                                    <div style="margin-left:55px">
                                        <div  class="form-check">
                                            <input class="form-check-input" type="radio" name="time_formate" value="g:i a"  id="flexRadioDefault5">
                                            <label class="form-check-label" for="flexRadioDefault5">
                                              6:32 am
                                            </label>
                                            <input style="width:25%; height:20px; border-radius:5px; margin-left:54px;
                                            background:rgb(228, 226, 226); border: 1px solid rgb(250, 249, 249); padding:3px; font-size:15px; text-align:center" type="text" value="g:i a" disabled/>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="time_formate" value="g:i A" id="flexRadioDefault6" checked>
                                            <label class="form-check-label" for="flexRadioDefault6">
                                                6:32 AM
                                            </label>
                                            <input style="width:25%; height:20px; border-radius:5px; margin-left:55px;
                                            background:rgb(228, 226, 226); border: 1px solid rgb(250, 249, 249); padding:3px; font-size:15px; text-align:center" type="text" value="g:i A" disabled/>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="time_formate" value="H:i" id="flexRadioDefault7">
                                            <label class="form-check-label" for="flexRadioDefault7">
                                                6:32
                                            </label>
                                            <input style="width:25%; height:20px; border-radius:5px; margin-left:79px;
                                            background:rgb(228, 226, 226); border: 1px solid rgb(250, 249, 249); padding:3px; font-size:15px; text-align:center" type="text" value="H:i" disabled/>
                                          </div>


                                    </div>


                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                <button class="btn btn-success float-right" id="setting_button">Submit</button>
        </div>
    </div>
</div>
</form>

        </div>

      </div>
    </div>
  </div>
{{-- logo edit modal end --}}


<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Logo List</h3>
            @if (session()->has('message'))
            <span style="margin-left: 100px" class="text-success">{{ session()->get('message') }}</span>
            @endif
        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped general-table">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Site Title</th>
                        <th>Logo</th>
                        <th>Slider</th>
                        <th>Favicon</th>
                        <th>Language</th>
                        <th>Timezone</th>
                        <th>Date Format</th>
                        <th>Time Format</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>

</section>
<!-- /.content -->
</div>
</div>

@endsection
@push('js')
<script>
$('.dropify').dropify();
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
$(document).ready(function() {
    $(function() {

        var table = $('.general-table').DataTable({

            dom: "lBfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],

            pageLength: 25,
            processing: true,
            serverSide: true,
            searchable: true,
            "ajax": {
                "url": "{{ route('admin.setting.index') }}",
                "datatype": "json",
                "dataSrc": "data",
                "data": function(data) {
                }
            },

            drawCallback: function(settings) {
                // Get DataTables API instance
                var api = new $.fn.dataTable.Api(settings);

                // Iterate through each row and add class based on 'status'
                api.rows().every(function(index, element) {
                    var status = this.data().sta;
                    if (status == 0) {
                        // $(this.node()).addClass('bg-dark');
                    }
                });

                // Additional code as needed
                $('#is_check_all').prop('checked', false);

            },

            columns: [
                {
                    name: 'DT_RowIndex',
                    data: 'DT_RowIndex',
                    sWidth: '3%'
                },
                {
                    data: 'site_title',
                    name: 'site_title',
                },
                {
                    data: 'logo',
                    name: 'logo',
                },
                {
                    data: 'slider',
                    name: 'slider',
                },
                {
                    data: 'favicon',
                    name: 'favicon'
                },
                {
                    data: 'language',
                    name: 'language',

                },
                {
                    data: 'timezone',
                    name: 'timezone',

                },
                {
                    data: 'date_formate',
                    name: 'date_formate',

                },
                {
                    data: 'time_formate',
                    name: 'time_formate',

                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },

            ],
            lengthMenu: [
                [10, 25, 50, 100, 500, 1000, -1],
                [10, 25, 50, 100, 500, 1000, "All"]
            ],
        });
        table.buttons().container().appendTo('#exportButtonsContainer');

        $(document.body).on('click', '#is_check_all', function(event) {
            alert('Checkbox clicked!');
            var checked = event.target.checked;
            if (true == checked) {
                $('.check1').prop('checked', true);
            }
            if (false == checked) {
                $('.check1').prop('checked', false);
            }
        });

        $('#is_check_all').parent().addClass('text-center');

        $(document.body).on('click', '.check1', function(event) {

            var allItem = $('.check1');

            var array = $.map(allItem, function(el, index) {
                return [el]
            })

            var allChecked = array.every(isSameAnswer);

            function isSameAnswer(el, index, arr) {
                if (index === 0) {
                    return true;
                } else {
                    return (el.checked === arr[index - 1].checked);
                }
            }

            if (allChecked && array[0].checked) {
                $('#is_check_all').prop('checked', true);
            } else {
                $('#is_check_all').prop('checked', false);
            }
        });

        //Submit filter form by select input changing
        $(document).on('change', '.submitable', function() {
            table.ajax.reload();
        });

    });

});

// submit data form

$(document).ready(function() {
    $('#telephoneInput').inputmask('(999) 999-9999');    // $('.dropify').dropify();

});


$(document).ready(function () {
    // When the file input changes
    $("#image").change(function () {
        readURL(this);
    });

    // Function to read the URL and display the image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                // Display the image preview
                $("#imagePreview").attr("src", e.target.result).show();

                // Show the remove button
                $("#removeImageButton").show();
                $(".imagePreview").hide();
            };

            // Read the file as a data URL
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Click event for the remove button
    $("#removeImageButton").click(function() {
        // Clear the file input and hide the image preview
        $("#image").val("");
        $("#imagePreview").attr("src", "").hide();
        // Hide the remove button
        $(this).hide();
    });
});
$(document).ready(function () {
    // When the file input changes
    $("#slider_image").change(function () {
        readURL(this);
    });

    // Function to read the URL and display the image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                // Display the image preview
                $("#SimagePreview").attr("src", e.target.result).show();

                // Show the remove button
                $("#sliderremoveImageButton").show();
                $(".sliderimagePreview").hide();
            };

            // Read the file as a data URL
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Click event for the remove button
    $("#sliderremoveImageButton").click(function() {
        // Clear the file input and hide the image preview
        $("#slider_image").val("");
        $("#SimagePreview").attr("src", "").hide();
        // Hide the remove button
        $(this).hide();
    });
});
$(document).ready(function () {
    // When the file input changes
    $("#fav_image").change(function () {
        readURL(this);
    });

    // Function to read the URL and display the image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                // Display the image preview
                $("#faviconImagePreview").attr("src", e.target.result).show();

                // Show the remove button
                $("#removeImageButtonFavicon").show();
                $(".favimagePreview").hide();
            };

            // Read the file as a data URL
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Click event for the remove button
    $("#removeImageButtonFavicon").click(function() {
        // Clear the file input and hide the image preview
        $("#fav_image").val("");
        $("#faviconImagePreview").attr("src", "").hide();
        // Hide the remove button
        $(this).hide();
    });
});


$(document).ready(function() {
    // Event handler for file input change
    $("#fav_image").change(function() {
        readURL(this);
    });

    // Function to read the URL and display the image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                // Display the image preview
                $("#faviconImagePreview").attr("src", e.target.result).show();
            };

            // Read the file as a data URL
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Event handler for clicking editLogo button
    $(document).on('click', '.editLogo', function() {
        var id = $(this).data('id');
        var site_title = $(this).data('site_title');
        var site_map = $(this).data('site_map');
        var email = $(this).data('email');
        var slider_title = $(this).data('slider_title');
        var slider_subtitle = $(this).data('slider_subtitle');
        var phone = $(this).data('phone');
        var pagination = $(this).data('pagination');
        var separator = $(this).data('separator');
        var date_formate = $(this).data('date_formate');
        var time_formate = $(this).data('time_formate');
        var timezone = $(this).data('timezone');
        var aprRate = $(this).data('apr_rate');
        var image = $(this).data('image');
        var fav_image = $(this).data('fav_image');
        var slider_image = $(this).data('slider_image');

        $('.imagePreview').attr('src', "{{ asset('/frontend/assets/images/logos') }}" + '/' + image);
        $('.favimagePreview').attr('src', "{{ asset('/frontend/assets/images/logos') }}" + '/' + fav_image);
        $('.sliderimagePreview').attr('src', "{{ asset('/frontend/assets/images/logos') }}" + '/' + slider_image);



        // Set values for form fields
        $('#setting_id').val(id);
        $('#site_title').val(site_title);
        $('#site_map').val(site_map);
        $('#email').val(email);
        $('#slider_title').val(slider_title);
        $('#slider_subtitle').val(slider_subtitle);
        $('#telephoneInput').val(phone);
        $('#pagination').val(pagination);
        $('#separator').val(separator);
        $('#timezone').val(timezone);
        $('#apr').val(aprRate);
        $('#language').val(language);


        // Check radio buttons
        $('input[name="date_formate"][value="' + date_formate + '"]').prop('checked', true);
        $('input[name="time_formate"][value="' + time_formate + '"]').prop('checked', true);

        // Show modal
        $('#editLogoModel').modal('show');


    });



    // Event handler for form submission
    $('#updateGeneralForm').submit(function(e) {
        e.preventDefault();

        // Serialize the form data
        var formData = new FormData(this);
            $('#setting_button').text('Loading...');
        // Make Ajax request
        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status == 'success') {
                    $('#editLogoModel').modal('hide');
                    toastr.success(response.message);
                    $('#updateGeneralForm')[0].reset();
                    $('.general-table').DataTable().draw(false);
                    $('#setting_button').text('Submit');
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                $('.up_input-error').text('');
                $.each(errors, function(key, value) {
                    // Display the error messages
                    $('#' + key + '_error').text(value[0]);
                });
                $('#setting_button').text('Submit');
            }
        });
    });

    // Event handler for status change
    $(document).on('change', '.action-select', function(e) {
        e.preventDefault();
        var id = $(this).data('id');

        $.ajax({
            url: "{{route('admin.logo.status.change')}}",
            method: "post",
            data: { id: id },
            success: function(res) {
                toastr.success(res.message);
                $('.logo-table').DataTable().draw(false);
            }
        });
    });
});


</script>
@endpush
