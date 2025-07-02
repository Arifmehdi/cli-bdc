@extends('backend.admin.layouts.master')

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css"rel="stylesheet">
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">

            {{--                Add slider  modal Start --}}
            <div class="modal fade " id="addSliderModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create New Slider</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <!-- Default box -->
                        <div class="card">

                            <div class="card-body">
                                <form id="sliderForm" action="{{ route('admin.frontend.slider.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control " name="title" id="title"
                                                placeholder="Enter Title">

                                        </div>
                                        <div class="form-group">
                                            <label for="sub_title">Sub Title</label>
                                            <input type="text" class="form-control " name="sub_title" id="sub_title"
                                                placeholder="Enter Sub Title">

                                        </div>
                                        <div class="form-group">
                                            <label for="image">Image <span style="color: red">*</span></label>
                                            <input type="file" class="form-control" name="image" id="image">
                                            <span class="image_error text-danger"></span>

                                            <div id="imagePreviewContainer">
                                                <img width="80px" height="50px" id="imagePreview"
                                                    style="display: none; margin-top:5px">
                                                <a id="removeImageButton"
                                                    style="display: none;
                                                    color:red; margin-top:5px; cursor:pointer">x</a>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddOne" name="status" value="1"
                                                checked>&nbsp;&nbsp;&nbsp;<label for="statusAddOne">Active</label>
                                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddTwo" name="status"
                                                value="0">&nbsp;&nbsp;&nbsp;<label for="statusAddTwo">Inactive</label>
                                            <span class="text-danger error-message" id="status-error"></span>
                                        </div>



                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->


                    </div>
                </div>
            </div>

            {{--                Add slider modal End --}}

            {{--                edit slider  modal Start --}}
            <div class="modal fade " id="editSliderModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit Slider</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <!-- Default box -->
                        <div class="card">

                            <div class="card-body">
                                <form id="updateSliderForm" action="{{ route('admin.frontend.slider.update') }}"
                                    method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control " name="title" id="up_title"
                                                placeholder="Enter Title">
                                            <input type="hidden" name="id" id="slider_id">

                                        </div>
                                        <div class="form-group">
                                            <label for="sub_title">Sub Title</label>
                                            <input type="text" class="form-control " name="sub_title"
                                                id="up_sub_title" placeholder="Enter Sub Title">

                                        </div>

                                        <div class="form-group">
                                            <label for="image">Image <span style="color: red">*</span></label>
                                            <input type="file" class="form-control" id="image_update" name="image">
                                            <img class="imagePreview" src="" alt="" id="up_image" height="200px"
                                                width="50%" class="mt-4">
                                                <div id="imagePreviewContainer">
                                                    <img width="80px" height="50px" id="upimagePreview"
                                                        style="display: none; margin-top:5px">
                                                    <a id="UpremoveImageButtonnews"
                                                        style="display: none;
                                                color:red; margin-top:5px; cursor:pointer">x</a>
                                                </div>
                                            <x-input-error :errorId="'image'" />
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusEditOne" name="status" value="1"
                                                checked>&nbsp;&nbsp;&nbsp;<label for="statusEditOne">Active</label>
                                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusEditTwo" name="status"
                                                value="0">&nbsp;&nbsp;&nbsp;<label for="statusEditTwo">Inactive</label>
                                            <span class="text-danger error-message" id="status-error"></span>
                                        </div>



                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                </form>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card -->


                    </div>
                </div>
            </div>

            {{--                Add slider modal End --}}



            <section class="content">

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Slider List</h3>
                        @if (session()->has('message'))
                            <span style="margin-left: 100px" class="text-success">{{ session()->get('message') }}</span>
                        @endif

                        <a href="#" class="btn btn-primary btn-sm float-right" style="margin-left: 10px"
                            data-toggle="modal" data-target="#addSliderModel"> <i class="fas fa-plus-circle"
                                style="margin-right: 10px"></i> Add Slider</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table id="example1" class="table table-bordered table-striped slider_table">
                            <thead>
                                <tr>
                                    <th>Sl</th>
                                    <th>Image</th>
                                    <th>Title</th>
                                    <th>Sub title</th>
                                    <th>Status</th>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/js/bootstrap5-toggle.jquery.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // user yajra code start
        $(document).ready(function() {
            $(function() {

                var table = $('.slider_table').DataTable({

                    dom: "lBfrtip",
                    buttons: ["copy", "csv", "excel", "pdf", "print"],

                    pageLength: 25,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    "ajax": {
                        "url": "{{ route('admin.frontend.slider.index') }}",
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
                            data: 'image',
                            name: 'image',
                        },
                        {
                            data: 'title',
                            name: 'title',
                        },
                        {
                            data: 'sub_title',
                            name: 'sub_title',
                        },
                        {
                            data: 'status_one',
                            name: 'status_one',
                        },
                        {
                            data: 'action',
                            name: 'action',
                            width: "10%",
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
        // user yajra code close


        // from submit ajax

        $(document).ready(function() {




            $(document).ready(function() {
                // When the file input changes
                $("#image").change(function() {
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




            $(document).ready(function() {
                // When the file input changes
                $("#image_update").change(function() {
                    readURL(this);
                });

                // Function to read the URL and display the image preview
                function readURL(input) {
                    if (input.files && input.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function(e) {
                            // Display the image preview
                            $("#upimagePreview").attr("src", e.target.result).show();

                            // Show the remove button
                            $("#UpremoveImageButtonnews").show();
                            $(".imagePreview").hide();

                        };

                        // Read the file as a data URL
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                // Click event for the remove button
                $("#UpremoveImageButtonnews").click(function() {
                    // Clear the file input and hide the image preview
                    $("#image_update").val("");
                    $("#upimagePreview").attr("src", "").hide();
                    // Hide the remove button
                    $(this).hide();
                });
            });









            $('#sliderForm').submit(function(e) {
                e.preventDefault();

                // Serialize the form data
                var formData = new FormData(this);

                // Make Ajax request
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle success response

                        if (response.status == 'success') {
                            $('#addSliderModel').modal('hide');
                            toastr.success(response.message);
                            $('#sliderForm')[0].reset();
                            $('.slider_table').DataTable().draw(false);
                        }

                    },
                    error: function(xhr) {
                        // Handle error response
                        var errors = xhr.responseJSON.errors;


                        $('.input-error').text('');
                        // console.log(errors);
                        // return
                        // Display validation errors
                        $.each(errors, function(key, value) {
                            // Display the error messages
                            $('.' + key + '_error').text(value[0]);
                        });
                    }
                });
            });

        });


        $(document).on('click', '.sliderEdit', function() {

            var id = $(this).data('id');
            var title = $(this).data('title');
            var sub_title = $(this).data('sub_title');
            var image = $(this).data('image');
            var status = $(this).data('status');
            var data = [id, title, sub_title, image];

            $('input[name="status"][value="' + status + '"]').prop('checked', true);
            $('#slider_id').val(id);
            $('#up_title').val(title);
            $('#up_sub_title').val(sub_title);
            $('#up_image').attr('src', '{{ asset('storage') }}/' + image);
            $('#editSliderModel').modal('show');
        });


        // slider update form submit

        $('#updateSliderForm').submit(function(e) {
            e.preventDefault();

            // Serialize the form data
            var formData = new FormData(this);

            // Make Ajax request
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Handle success response

                    // console.log(response);
                    // return
                    if (response.status == 'success') {
                        $('#editSliderModel').modal('hide');
                        toastr.success(response.message);
                        $('#updateSliderForm')[0].reset();
                        $('.slider_table').DataTable().draw(false);
                    }

                },
                error: function(xhr) {
                    // Handle error response
                    var errors = xhr.responseJSON.errors;
                    // $('.input-error').text('');
                    console.log(errors);
                    return
                    // Display validation errors
                    // $.each(errors, function (key, value) {
                    //     // Display the error messages
                    //     $('#' + key + '_error').text(value[0]);
                    // });
                }
            });
        });



        $(document).on('click', '.delete_slider', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.confirm({
                title: 'Delete Confirmation',
                content: 'Are you sure?',
                buttons: {
                    cancel: {
                        text: 'No',
                        btnClass: 'btn-primary',
                        action: function() {
                            // Do nothing on cancel
                        }
                    },
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                url: "{{ route('admin.frontend.slider.delete') }}",
                                type: 'post',
                                data: {
                                    id: id
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        toastr.success(response.message);
                                        $('.slider_table').DataTable().draw(false);

                                    }

                                },
                                error: function(error) {
                                    // Show Toastr error message
                                    toastr.error(error.responseJSON.message);
                                }
                            });
                        }
                    },

                }
            });

        });

           // status change
           $(document).on('change', '.action-select', function(e){
                e.preventDefault();
                let id = $(this).data('id');


                $.ajax({
                    url:"{{route('admin.slider.status.change')}}",
                    method:"post",
                    data:{id:id},
                    success:function(res){
                        console.log(res)
                        toastr.success(res.message);
                        $('.slider_table').DataTable().draw(false);
                    }
                })
            });
    </script>
@endpush
