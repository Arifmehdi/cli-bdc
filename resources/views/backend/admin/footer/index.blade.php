@php
    use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')
@push('css')
@endpush
@section('content')
    {{-- Footer edit Start --}}
    <div class="modal fade " id="FootereditModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Edit Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Default box -->
                <div class="card">

                    <div class="card-body">
                        <form id="updateFooterForm" action="{{ route('admin.frontend.footer.update') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">Name <span style="color:red;font-weight:bold">*</span></label>
                                    <input type="text" class="form-control " name="name" id="up_name"
                                        placeholder="Enter Menu Name">
                                        <span class="text-danger error-message" id="title-error"></span>
                                </div>
                                <input type="hidden" name="footer_id" id="footer_id">

                                <div class="form-group">
                                    <label for="title">Pages</label>
                                    <select name="slug" id="up_slug" class="form-control">
                                        <option value="">~Select Page~</option>
                                        @foreach ($pages as $page)
                                            <option value="{{ $page->slug }}">{{ $page->slug }}</option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="form-group">
                                    <label for="title">Route</label>
                                    <select class="form-control" id="updateParentSelect">
                                        <option value="static" selected>Static</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>

                                <div class="form-group" id="UpdatecustomRouteField" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="text" id="route_url" class="form-control" name="route"
                                                placeholder="Enter Route Name">
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" id="param" class="form-control" name="param"
                                                placeholder="if any param. enter route param">
                                        </div>
                                    </div>


                                </div>

                                <div class="form-group">
                                    <label for="title">Column Position <span
                                            style="color:red;font-weight:bold">*</span></label>
                                    <select name="footer_col_up" id="footer_col_up" class="form-control" id="selectFooter">
                                        <option value=""> ~ Select Column ~ </option>
                                        <option value="1">1 col</option>
                                        <option value="2">2 col</option>
                                        <option value="3">3 col</option>
                                        <option value="4">4 col</option>
                                    </select>
                                    <span class="text-danger error-message" id="col-error"></span>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddOne" name="status"
                                                value="1">&nbsp;&nbsp;&nbsp;<label for="statusAddOne">Active</label>
                                            &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddTwo" name="status"
                                                value="0">&nbsp;&nbsp;&nbsp;<label for="statusAddTwo">Inactive</label>
                                            <span class="text-danger error-message" id="status-error"></span>
                                        </div>
                                    </div>

                                </div>

                            </div>
                            <!-- /.card-body -->

                            <div class="card-footer">
                                <button type="submit" id="FootermenuSubmit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->


            </div>
        </div>
    </div>

    {{--                Footer Edit modal End --}}













    <div class="row">
        <div class="col-md-12">
            <div class="card card-primary card-tabs">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                aria-selected="true">Content</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile"
                                aria-selected="false">Menu</a>
                        </li>

                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-one-tabContent">
                        <div class="tab-pane fade show active" id="custom-tabs-one-home" role="tabpanel"
                            aria-labelledby="custom-tabs-one-home-tab">
                            <form action="{{ route('admin.frontend.footer.content.store') }}" method="POST"
                                id="contentSavedForm">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Title </label>
                                            <input type="text" class="form-control"
                                                value="{{ isset($footer_content) ? $footer_content->title : old('title ') }}"
                                                name="title" id="title" required placeholder="Enter Footer Title ">
                                            <input type="hidden" name="id"
                                                value="{{ isset($footer_content) ? $footer_content->id : '' }}">
                                        </div>

                                        <div class="form-group">
                                            <label for="title">Description </label>
                                            <textarea name="description" id="description" cols="30" class="form-control" rows="10"
                                                placeholder="Enter footer description ">{{ isset($footer_content) ? $footer_content->description : old('description') }}</textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="title">Copyright</label>
                                            <textarea name="copyright" id="copyright" cols="30" class="form-control" rows="10"
                                                placeholder="Copyright © 2024 Dream Best Car All rights reserved.">{{ isset($footer_content) ? $footer_content->copyright : old('copyright ') }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="title"></label>
                                            <input type="submit" value="Update" id="contentSubmit"
                                                class="btn btn-success px-5 float-right " style="margin-top: 46px">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane fade" id="custom-tabs-one-profile" role="tabpanel"
                            aria-labelledby="custom-tabs-one-profile-tab">
                            <form id="MenuForm" action="{{ route('admin.frontend.footer.menu-store') }}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="title">Name <span
                                                style="color:red;font-weight:bold">*</span></label>
                                        <input type="text" class="form-control " name="name" id="title"
                                            placeholder="Enter Menu Name">
                                        <span class="text-danger error-message input-error name_error"
                                            id="title-error"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="title">Pages</label>
                                        <select name="slug" class="form-control">
                                            <option value="">~Select Page~</option>
                                            @foreach ($pages as $page)
                                                <option value="{{ $page->slug }}">{{ $page->slug }}</option>
                                            @endforeach

                                        </select>

                                    </div>


                                    <div class="form-group">
                                        <label for="title">Route</label>
                                        <select class="form-control" id="parentSelect">
                                            <option value="static" selected>Static</option>
                                            <option value="custom">Custom</option>
                                        </select>
                                    </div>

                                    <div class="form-group" id="customRouteField" style="display: none;">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="route_url"
                                                    id="title" placeholder="Enter Route Name">
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="param" id="param"
                                                    placeholder="if any param. enter route param">
                                            </div>

                                        </div>

                                    </div>

                                    <div class="form-group">
                                        <label for="title">Column Position <span
                                                style="color:red;font-weight:bold">*</span></label>
                                        <select name="footer_col" class="form-control" id="selectFooter">
                                            <option value=""> ~ Select Column ~ </option>
                                            <option value="1">1 col</option>
                                            <option value="2">2 col</option>
                                            <option value="3">3 col</option>
                                            <option value="4">4 col</option>
                                        </select>
                                        <span class="text-danger error-message input-error footer_col_error"
                                            id="title-error"></span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddOne" name="status"
                                                    value="1" checked>&nbsp;&nbsp;&nbsp;<label
                                                    for="statusAddOne">Active</label>
                                                &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddTwo" name="status"
                                                    value="0">&nbsp;&nbsp;&nbsp;<label
                                                    for="statusAddTwo">Inactive</label>
                                                <span class="text-danger error-message" id="status-error"></span>
                                            </div>
                                        </div>

                                    </div>



                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary float-right px-5"
                                        id="menuSubmit">Save</button>
                                </div>
                            </form>


                            <h3 class="card-title">Menu List</h3>
                            <table id="example1" class="table table-bordered table-striped footer_menu_table"
                                style="width: 100% !important">
                                <thead>
                                    <tr>
                                        <th>Sl</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Route</th>
                                        <th>Param</th>
                                        <th>Priority</th>
                                        <th>Column</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>



                                </tbody>

                            </table>
                        </div>

                    </div>
                    <!-- /.card -->


                </div>
            </div>

        </div>
    </div>
@endsection
@push('js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // description editor js start

        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });

        ClassicEditor
            .create(document.querySelector('#copyright'))
            .catch(error => {
                console.error(error);
            });

        // description editor js close


        // this code use for submit footer content

        $(document).ready(function() {
            $('#contentSavedForm').submit(function(e) {
                e.preventDefault(); // Prevent normal form submission
                var formData = $(this).serialize(); // Serialize form data
                $('#contentSubmit').val('Loading....');
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'), // URL to submit the form data
                    data: formData,
                    success: function(response) {
                        // Handle success response
                        if (response.status == 'success') {
                            toastr.success(response.message);
                            $('#contentSubmit').val('Update');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle error
                        console.error(xhr.responseText);
                    }
                });
            });
        });

        // this code use for submit footer Menu

        $(document).ready(function() {
            $('#MenuForm').submit(function(e) {
                e.preventDefault(); // Prevent normal form submission
                var formData = $(this).serialize(); // Serialize form data
                $('#menuSubmit').text('Loading....');
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'), // URL to submit the form data
                    data: formData,
                    success: function(response) {
                        console.log(response);
                        // Handle success response
                        if (response.status == 'success') {
                            toastr.success(response.message);
                            $('#menuSubmit').text('Save');
                            $('#MenuForm')[0].reset();
                            $('.footer_menu_table').DataTable().draw(false);
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
                            $('#menuSubmit').text('Save');
                            $('.' + key + '_error').text(value[0]);
                        });
                    }
                });
            });
        });


        // custom route selected

        $(document).ready(function() {
            $('#parentSelect').change(function() {
                if ($(this).val() == 'custom') {
                    $('#customRouteField').show();

                } else {
                    $('#customRouteField').hide();
                }
            });
        });

        $(document).ready(function() {
            $('#updateParentSelect').change(function() {
                if ($(this).val() == 'custom') {
                    $('#UpdatecustomRouteField').show();
                } else {
                    $('#UpdatecustomRouteField').hide();
                }
            });
        });

        // user yajra code start
        $(document).ready(function() {
            $(function() {

                var table = $('.footer_menu_table').DataTable({

                    dom: "lBfrtip",
                    buttons: ["copy", "csv", "excel", "pdf", "print"],

                    pageLength: 25,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    "ajax": {
                        "url": "{{ route('admin.frontend.footer.index') }}",
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
                        },
                        {
                            data: 'slug',
                            name: 'slug',
                        },
                        {
                            data: 'route_url',
                            name: 'route_url',
                        },
                        {
                            data: 'param',
                            name: 'param',
                        },
                        {
                            data: 'menu_priority',
                            name: 'menu_priority',
                        },
                        {
                            data: 'column_position',
                            name: 'column_position',
                        },
                        {
                            data: 'status',
                            name: 'status',
                            width: "10%",
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




        // footer edit code


        $(document).on('click', '.editFooter', function() {

            var id = $(this).data('id');
            var name = $(this).data('name');
            var slug = $(this).data('slug');
            var status = $(this).data('status');
            var column_position = $(this).data('column_position');
            var param = $(this).data('param');
            var route_url = $(this).data('route_url');


            if (param != null || route_url != null) {
                var custom = 'custom';
                $('#UpdatecustomRouteField').show();
                $('#updateParentSelect').val(custom);
            }
            $('input[name="status"][value="' + status + '"]').prop('checked', true);
            $('#footer_id').val(id);
            $('#up_name').val(name);
            $('#up_slug').val(slug);
            $('#param').val(param);
            $('#route_url').val(route_url);
            $('#footer_col_up').val(column_position);
            $('#FootereditModel').modal('show');
        });



        $('#updateFooterForm').submit(function(e) {
            e.preventDefault();

            // Serialize the form data
            var formData = new FormData(this);
            $('#FootermenuSubmit').text('Loading...');
            // Make Ajax request
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    // Handle success response
                    console.log(response);
                    if(response.errors){
                           $('#title-error').html(response.errors.name);
                    $('#col-error').html(response.errors.footer_col_up);
                    }
                    if (response.status == 'success') {
                        $('#FootereditModel').modal('hide');
                        toastr.success(response.message);
                        $('#updateFooterForm')[0].reset();
                        $('.footer_menu_table').DataTable().draw(false);
                    }

                    $('#FootermenuSubmit').text('Update');

                },
                error: function(response) {
                        // Handle error response
                        console.log(response);
                        // console.log(errors);
                        // return
                        // Display validation errors
                        $.each(errors, function(key, value) {
                            // Display the error messages
                            $('.' + key + '_error').text(value[0]);
                        });
                        $('#FootermenuSubmit').text('Update');
                    }
            });
        });


        // delete menu item

        $(document).on('click', '#menu_delete', function(e) {
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
                                url: "{{ route('admin.frontend.footer.menu-delete') }}",
                                type: 'post',
                                data: {
                                    id: id
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        toastr.success(response.message);
                                        $('.footer_menu_table').DataTable().draw(false);

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
        $(document).on('change', '.action-select', function(e) {
            e.preventDefault();
            let id = $(this).data('id');


            $.ajax({
                url: "{{ route('admin.frontend.footer.change-status') }}",
                method: "post",
                data: {
                    id: id
                },
                success: function(res) {
                    console.log(res)
                    toastr.success(res.message);
                    $('.footer_menu_table').DataTable().draw(false);
                }
            })
        });
    </script>
@endpush
