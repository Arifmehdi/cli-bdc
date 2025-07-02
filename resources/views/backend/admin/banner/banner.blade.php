@extends('backend.admin.layouts.master')

@section('content')
    <div class="row">
        {{--   add user modal --}}
        {{-- <div class="modal fade" id="bannerAdd" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add Banner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <form action="{{ route('admin.banner.add') }}" enctype="multipart/form-data" method="post"
                            style="background-color:#ddd;padding:20px" id="bannerAddForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 text-center">
                                    <table align="left" cellpadding="10">
                                        <tr>
                                            <td align="left">Name<span style="color:red;font-weight:bold">*</span></td>
                                            <td>
                                                <input type="text" name="name" id="name"
                                                    placeholder="banner name" class="form-control">
                                                <div class="text-danger error-name" style="float: left;"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">Position<span style="color:red;font-weight:bold">*</span>
                                            </td>
                                            <td>
                                                <select id="position" name="position" class="form-control"
                                                    onchange="positionChange(this.value)">
                                                    <option value="">~ select position ~</option>
                                                    <option value="left_sidebar">Left Sidebar</option>
                                                    <option value="Right_sidebar">Right Sidebar</option>
                                                    <option value="top">Top</option>
                                                    <option value="Bottom">Bottom</option>
                                                    <option value="middle">Middle</option>
                                                    <option value="header_banner">Header Banner</option>
                                                </select>
                                                <div class="text-danger error-position" style="float: left;"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left">Image<span style="color:red;font-weight:bold">*</span></td>
                                            <td>
                                                <p style=" margin-top:7px;" id="recomanded">
                                                <p></label>
                                                    <input type="file" name="image" id="image"
                                                        class="form-control image" disabled>
                                                <div class="text-danger error-image" style="float: left;"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6 text-center">
                                    <table align="center" cellpadding="10">
                                        <tr>
                                            <td align="left">Status <span style="color:red;font-weight:bold">*</span></td>
                                            <td>
                                                <select id="status" name="status" name="account_type"
                                                    class="form-control">
                                                    <option value="">~ select ~</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                                <div class="text-danger error-status" style="float: left;"></div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td align="left">Renew <span style="color:red;font-weight:bold">*</span></td>
                                            <td>
                                                <select name="renew" id="renew" class="form-control">
                                                    <option value="">~ select State ~</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                                <div class="text-danger error-renew" style="float: left;"></div>
                                            </td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <table cellpadding="5">
                                        <tr>
                                            <td align="left"> Description<span style="color:red;font-weight:bold">*</span>
                                            </td>
                                            <td align="left">
                                                <textarea name="description" id="description" placeholder="banner description" class="form-control" rows="2"
                                                    cols="72"></textarea>
                                                <div class="text-danger error-description" style="float: left;"></div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <button style="padding-left:25px; padding-right:25px; margin-left:81%; margin-top:12px"
                                class="btn btn-success" type="submit">Submit</button>
                        </form>
                    </div>

                </div>
            </div>
        </div> --}}
        {{--  End add user modal --}}
        {{-- banner edit modal start --}}
        <div class="modal fade" id="BannerEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Edit Banner</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.banner.edit') }}" method="POST" id="bannerupdate"
                            enctype="multipart/form-data" style="background-color:#ddd;padding:20px">
                            @csrf
                            <input type="hidden" name="banner_id" id="banner_id">
                            <div class="row">
                                <div class="col-md-6 text-center">
                                    <table align="center" cellpadding="10">
                                        <tr>
                                            <td>Name<span style="color:red;font-weight:bold">*</span></td>
                                            <td>
                                                <input type="text" name="up_name" id="banner_name"
                                                    placeholder="banner name" class="form-control">
                                                <div class="text-danger error-up_name" style="float: left;"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Position<span style="color:red;font-weight:bold">*</span> </td>
                                            <td>
                                                <select id="up_position" name="up_position" class="form-control" disabled>
                                                    <option value="">~ select position ~</option>
                                                    <option value="auto page top">auto page top</option>
                                                    <option value="auto page middle">auto page middle</option>
                                                    <option value="home page top">home page top</option>
                                                    <option value="home page bottom">home page bottom</option>
                                                    <option value="auto details page top">auto details page top</option>
                                                    <option value="news page top">news page top</option>
                                                    <option value="contact page top">contact page top</option>
                                                    <option value="about page top">about page top</option>
                                                    <option value="faq page top">faq page top</option>
                                                    <option value="terms condition page top">terms condition page top</option>
                                                    <option value="cars for sale page top">cars for sale page top</option>
                                                    <option value="new cars search page top">tnew cars search page top
                                                    </option>
                                                </select>
                                                <div class="text-danger error-up_position" style="float: left;"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Image</td>
                                            <td>
                                                <p style=" margin-top:7px;" id="image_size">
                                                <p></label>
                                                    <input type="file" name="up_image" id="up_image"
                                                        class="form-control" id="imageInput" accept="image/*">

                                                   <div id="imagePreviewContainer">
                                                    <img width="250px" height="30px" id="imagePreview"
                                                        style="display: none; margin-top:5px">
                                                    <a id="removeImageButton"
                                                        style="display: none;
                                                            color:red; margin-top:5px; cursor:pointer">x</a>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6 text-center">
                                    <table align="center" cellpadding="10">
                                        <tr>
                                            <td>Status <span style="color:red;font-weight:bold">*</span></td>
                                            <td>
                                                <select id="up_status" name="up_status" name="account_type"
                                                    class="form-control">
                                                    <option value="">~ select ~</option>
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                                <div class="text-danger error-up_status" style="float: left;"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Renew <span style="color:red;font-weight:bold">*</span></td>
                                            <td>
                                                <select name="up_renew" id="up_renew" class="form-control">
                                                    <option value="">~ select State ~</option>
                                                    <option value="yes">Yes</option>
                                                    <option value="no">No</option>
                                                </select>
                                                <div class="text-danger error-up_renew" style="float: left;"></div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>Description <span style="color:red;font-weight:bold">*</span></td>
                                            <td>
                                                <textarea name="up_description" id="up_description" class="form-control"></textarea>
                                                <div class="text-danger error-up_description" style="float: left;"></div>
                                            </td>
                                        </tr>

                                    </table>
                                    <button
                                        style="padding-left:22px; padding-right:22px; margin-left:183px; margin-top:10px"
                                        class="btn btn-success" type="submit" id="banner_submit_btn">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        {{-- news edit modal close --}}




        <div class="col-md-12 pt-5 m-auto rounded">
            <div class="card">
                <div class="card-header">
                    <h6>All Banners</h6>
                    {{-- <a href="" class="float-right btn btn-primary btn-sm" data-toggle="modal"
                        data-target="#bannerAdd"> <i class="fas fa-plus-circle"></i> Add Banner</a> --}}
                </div>
                <div class="card-block">
                    @if (session()->has('message'))
                        <h3 class="text-success">{{ session()->get('message') }}</h3>
                    @endif
                    <div class="table-responsive dt-responsive">
                        <table id="dom-jqry" class="table table-striped table-bordered banners_table nowrap"
                            style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="width:5%">S.L</th>
                                    <th style="width:20%">Name</th>
                                    <th style="width:15%">Image</th>
                                    <th style="width:10%">Position</th>
                                    <th style="width:10%">Status</th>
                                    <th style="width:10%">Action</th>
                                </tr>
                            </thead>
                            <tbody>


                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $.ajaxSetup({
            beforeSend: function(xhr, type) {
                if (!type.crossDomain) {
                    xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                }
            },
        });



        $(document).ready(function() {
            $(function() {
                var table = $('.banners_table').DataTable({
                    dom: "lBfrtip",
                    buttons: ["copy", "csv", "excel", "pdf", "print"],
                    pageLength: 25,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    "ajax": {
                        "url": "{{ route('admin.banner.show') }}",
                        "datatype": "json",
                        "dataSrc": "data",
                        "data": function(data) {

                        }
                    },
                    drawCallback: function(settings) {
                        $('#is_check_all').prop('checked', false);

                    },
                    columns: [{
                            name: 'DT_RowIndex',
                            data: 'DT_RowIndex',
                            sWidth: '3%'
                        },

                        {
                            data: 'name',
                            name: 'name',
                            sWidth: '20%'
                        },

                        {
                            data: 'Image',
                            name: 'Image',
                            sWidth: '30%'
                        },
                        {
                            data: 'position',
                            name: 'position',
                            sWidth: '10%'
                        },

                        {
                            data: 'status',
                            name: 'status',
                            sWidth: '10%'
                        },

                        {
                            data: 'action',
                            name: 'action',
                            sWidth: "17%",
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

                $(document).ready(function() {
                    // When the file input changes
                    $("#up_image").change(function() {
                        readURL(this);
                    });
                    // Function to read the URL and display the image preview
                    function readURL(input) {
                        if (input.files && input.files[0]) {
                            var reader = new FileReader();

                            reader.onload = function(e) {
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
                $(document).on('click', '.editBanner', function(e) {
                    e.preventDefault();
                    let id = $(this).data('id');
                    let name = $(this).data('name');
                    let position = $(this).data('position');

                    let status = $(this).data('status');
                    let dealer_list = $(this).data('dealer_list');

                    let renew = $(this).data('renew');
                    let description = $(this).data('description');
                    var image = $(this).data('image');
                    console.log(image);
                    // let dealer_list = $(this).data('dealer_list');
                    $('#banner_id').val(id);
                    $('#banner_name').val(name);
                    $('#up_position').val(position);
                    $('#up_status').val(status);
                    // $('#up_dealer_list').val(dealer_list);
                    $('#up_renew').val(renew);

                    $('#up_description').text(description);
                    $('#imagePreview').attr('src', "{{ asset('/dashboard/images/banners') }}" +
                        '/' + image);

                        $('#imagePreview').css('display','block');
                        $('#removeImageButton').css('display','block');
                    switch (position) {
                        case 'auto page top':
                        case 'home page top':
                        case 'about page top':
                        case 'auto details page top':
                        case 'faq page top':
                        case 'contact page top':
                        case 'terms condition page top':
                        case 'new cars search page top':
                        case 'cars for sale page top':
                        case 'home page bottom':
                            $('#image_size').html('Recommanded size 728*90');
                            break;
                        case 'auto page middle':
                            $('#image_size').html('Recommanded size 300*460');
                            break;
                    }


                    $('#BannerEdit').modal('show');

                });

                $('#bannerupdate').submit(function(e) {
                    e.preventDefault();
                    var formData = new FormData(this);
                    $('#banner_submit_btn').text('Loading...');
                    $.ajax({
                        url: $(this).attr("action"),
                        method: $(this).attr("method"),
                        data: new FormData(this),
                        processData: false,
                        datatype: JSON,
                        contentType: false,
                        success: function(res) {
                            console.log(res);
                            var errors = res.errors;
                            if (errors) {
                                $.each(errors, function(index, error) {

                                    $('.error-' + index).text(error);

                                });
                            } else if (res.status == 'success') {

                                $('#BannerEdit').modal('hide');
                                $('#bannerupdate')[0].reset();
                                $("#imagePreview").attr("src", "").hide();
                                toastr.success('Banner updated successfully');
                                $('.banners_table').DataTable().draw(false);

                            }
                            $('#banner_submit_btn').text('Submit');
                        },
                        error: function(error) {
                            console.log(error);
                        }

                    });
                });


            });

            $('#bannerAddForm').on('submit', function(event) {
                event.preventDefault(); // Prevent normal form submission

                // Collect form data
                var formData = new FormData($(this)[0]);

                // Send Ajax request
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        console.log(res);
                        var errors = res.errors;
                        if (errors) {
                            $.each(errors, function(index, error) {

                                $('.error-' + index).text(error);

                            });
                        } else if (res.status == 'success') {
                            $('#bannerAddForm')[0].reset();
                            $('#bannerAdd').modal('hide');
                            toastr.success('Banner create successfully');


                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error("Form submission error:", error);
                        // You can display an error message or handle the error as needed
                    }
                });
            });

            $(document).on('change', '#banner_activeInactive', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let status = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin.banner.change.status') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    success: function(res) {
                        console.log(res);
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            $('.banners_table').DataTable().draw(false);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }

                });


            });
        });
    </script>
@endpush
