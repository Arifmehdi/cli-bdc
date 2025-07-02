@extends('backend.admin.layouts.master')

@push('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap5-toggle@5.0.4/css/bootstrap5-toggle.min.css"rel="stylesheet">
@endpush
@section('content')
<div class="row">
    <div class="col-md-12">

        {{--                Add slider  modal Start --}}
        <div class="modal fade " id="addMenuModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create New Menu</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- Default box -->
                <div class="card">

                    <div class="card-body">
                        <form id="MenuForm" action="{{ route('admin.frontend.menu.store') }}" method="post">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">Name <span style="color:red;font-weight:bold">*</span></label>
                                <input type="text" class="form-control " name="name" id="title"
                                placeholder="Enter Menu Name">
                                <span class="text-danger error-message input-error name_error" id="title-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="title">Parent</label>
                               <select name="parent" class="form-control">
                                <option value="">~Select Parent~</option>
                                @foreach ($parents as $parent)
                                <option value="{{$parent->id}}">{{ $parent->name}}</option>
                                @endforeach

                               </select>

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
                                <select name="route" class="form-control" id="parentSelect">
                                    <option value="static" selected>Static</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>

                            <div class="form-group" id="customRouteField" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="route" id="title" placeholder="Enter Route Name">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="param" id="param" placeholder="if any param. enter route param">
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddOne" name="status" value="1"
                                        checked>&nbsp;&nbsp;&nbsp;<label for="statusAddOne">Active</label>
                                        &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddTwo" name="status"
                                        value="0">&nbsp;&nbsp;&nbsp;<label for="statusAddTwo">Inactive</label>
                                        <span class="text-danger error-message" id="status-error"></span>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- /.card-body -->

                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary float-right px-5" id="menu_button">Save</button>
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
<div class="modal fade " id="editMenuModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
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
                <form id="updateMenuForm" action="{{ route('admin.frontend.menu.update') }}"
                method="post" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Name <span style="color:red;font-weight:bold">*</span></label>
                        <input type="text" class="form-control " name="name" id="up_name"
                        placeholder="Enter Menu Name">
                        <span class="text-danger error-message up_input-error up_name_error" id="title-error"></span>
                    </div>
                    <input type="hidden" name="menu_id" id="menu_id">
                    <div class="form-group">
                        <label for="title">Parent</label>
                       <select name="parent"  class="form-control" id="up_parent">
                        <option value="">~Select Parent~</option>
                        @foreach ($parents as $parent)
                        <option value="{{$parent->id}}">{{ $parent->name}}</option>
                        @endforeach

                       </select>

                    </div>

                    <div class="form-group">
                        <label for="title">Pages</label>
                       <select name="slug" class="form-control up_slug">
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
                                <input type="text" class="form-control route_url" name="route" placeholder="Enter Route Name">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control param" name="param" placeholder="if any param. enter route param">
                            </div>
                        </div>


                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddOne" name="status" value="1"
                                >&nbsp;&nbsp;&nbsp;<label for="statusAddOne">Active</label>
                                &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddTwo" name="status"
                                value="0">&nbsp;&nbsp;&nbsp;<label for="statusAddTwo">Inactive</label>
                                <span class="text-danger error-message" id="status-error"></span>
                            </div>
                        </div>

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
            <h3 class="card-title">Menu List</h3>
            @if (session()->has('message'))
            <span style="margin-left: 100px" class="text-success">{{ session()->get('message') }}</span>
            @endif
            <a href="#" class="btn btn-primary btn-sm float-right" style="margin-left: 10px"
            data-toggle="modal" data-target="#addMenuModel"> <i class="fas fa-plus-circle"
            style="margin-right: 10px"></i> Add Menu</a>

        </div>
        <!-- /.card-header -->
        <div class="card-body">
            <table id="example1" class="table table-bordered table-striped menu_table">
                <thead>
                    <tr>
                        <th>Sl</th>
                        <th>Name</th>
                        <th>Parent</th>
                        <th>Slug</th>
                        <th>Route</th>
                        <th>Param</th>
                        <th>Priority</th>
                        <th>created_by</th>
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

    // custom route selected

    $(document).ready(function(){
        $('#parentSelect').change(function(){
            if($(this).val() == 'custom'){
                $('#customRouteField').show();

            } else {
                $('#customRouteField').hide();
            }
        });
    });

    $(document).ready(function(){
        $('#updateParentSelect').change(function(){
            if($(this).val() == 'custom'){
                $('#UpdatecustomRouteField').show();
            } else {
                $('#UpdatecustomRouteField').hide();
            }
        });
    });


    // user yajra code start
    $(document).ready(function() {
        $(function() {

            var table = $('.menu_table').DataTable({

                dom: "lBfrtip",
                buttons: ["copy", "csv", "excel", "pdf", "print"],

                pageLength: 25,
                processing: true,
                serverSide: true,
                searchable: true,
                "ajax": {
                    "url": "{{ route('admin.frontend.menu.index') }}",
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
                    data: 'parent',
                    name: 'parent',
                },
                {
                    data: 'slug',
                    name: 'slug',
                },
                {
                    data: 'route',
                    name: 'route',
                },
                {
                    data: 'param',
                    name: 'param',
                },
                {
                    data: 'priority',
                    name: 'priority',
                },
                {
                    data: 'created_by',
                    name: 'created_by',
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


    // from submit ajax

    $(document).ready(function() {

        $('#MenuForm').submit(function(e) {
            e.preventDefault();

            // Serialize the form data
            var formData = new FormData(this);
            $('#menu_button').text('Loading...');
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
                    // return;
                    if (response.status == 'success') {
                        $('#addMenuModel').modal('hide');
                        toastr.success(response.message);
                        $('#MenuForm')[0].reset();
                        $('.menu_table').DataTable().draw(false);
                        $('#menu_button').text('Save');
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
                        $('#menu_button').text('Save');
                    });
                }
            });
        });

    });


    $(document).on('click', '.editMenu', function() {

        var id = $(this).data('id');
        var name = $(this).data('name');
        var parent = $(this).data('parent');
        var slug = $(this).data('slug');
        var status = $(this).data('status');
        var param = $(this).data('param');
        var route_url = $(this).data('route_url');

        if(param != null || route_url != null)
        {
            var custom = 'custom';
            $('#UpdatecustomRouteField').show();
            $('#updateParentSelect').val(custom);
        }
        $('input[name="status"][value="' + status + '"]').prop('checked', true);
        $('#menu_id').val(id);
        $('#up_name').val(name);
        $('#up_parent').val(parent);
        $('.up_slug').val(slug);
        $('.param').val(param);
        $('.route_url').val(route_url);
        $('#editMenuModel').modal('show');
    });


    // slider update form submit

    $('#updateMenuForm').submit(function(e) {
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
                    $('#editMenuModel').modal('hide');
                    toastr.success(response.message);
                    $('#updateMenuForm')[0].reset();
                    $('.menu_table').DataTable().draw(false);
                }

            },
            error: function(xhr) {
                // Handle error response
                var errors = xhr.responseJSON.errors;
                $('.up_input-error').text('');
                console.log(errors);
                return
                // Display validation errors
                $.each(errors, function (key, value) {
                        // Display the error messages
                        $('#' + key + '_error').text(value[0]);
                    });
                }
            });
        });



        $(document).on('click', '#menu_delete', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.confirm({
                title: 'Delete Confirmation',
                content: 'Are you sure?',
                buttons: {
                    cancel: {
                        text: 'No',
                        btnClass: 'btn-danger',
                        action: function() {
                            // Do nothing on cancel
                        }
                    },
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                url: "{{ route('admin.frontend.menu.delete') }}",
                                type: 'post',
                                data: {
                                    id: id
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        toastr.success(response.message);
                                        $('.menu_table').DataTable().draw(false);

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
                url:"{{route('admin.menu.status.change')}}",
                method:"post",
                data:{id:id},
                success:function(res){
                    console.log(res)
                    toastr.success(res.message);
                    $('.menu_table').DataTable().draw(false);
                }
            })
        });

        // status change
        $(document).on('change', '.menu_priority', function(e){
            e.preventDefault();
            let id = $(this).data('id');
            let priority = $(this).val();


            $.ajax({
                url:"{{route('admin.menu.priority-change')}}",
                method:"post",
                data:{id:id,priority:priority},
                success:function(res){
                    //  console.log(res)
                    //  return
                    if(res.status== 'success')
                    {
                        toastr.success(res.message);
                        $('.menu_table').DataTable().draw(false);
                    }else
                    {
                        toastr.info(res.message);
                        $('.menu_table').DataTable().draw(false);
                    }

                }
            })
        });
    </script>
    @endpush
