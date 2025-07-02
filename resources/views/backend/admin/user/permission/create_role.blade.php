@extends('backend.admin.layouts.master')

@section('content')
<div class="row">
    <div class="col-md-12">

{{--                role modal Start --}}
        <div class="modal fade " id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create New Role</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Default box -->
                    <div class="card">
                        {{-- @php
                        $messages = session()->get('errors', []);
                        @endphp
                         @if (session()->has('errors'))
                         @php
                            $messages = session()->get('errors',[]);
                            @endphp
                        @endif --}}


                        <div class="card-body">
                            <form id="roleForm" action="{{route('admin.roles.store')}}" method="post">
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="rolename">Role Name</label>
                                        <input type="text" class="form-control " name="rolename" id="rolename" placeholder="Enter Role" >
                                        <x-input-error  :errorId="'rolename_error'"/>
                                    </div>


                                    <div class="form-group">
                                        <label>Permissions</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkpermissionAll">
                                            <label class="form-check-label" for="checkpermissionAll">All</label>
                                        </div>
                                        <hr>
                                        @php $i =1;  @endphp
                                        @foreach($group_permissions as $group)

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="{{ $i }}management" value="{{$group->name}}" onclick="checkPermissionByGroup('role-{{ $i }}-management-checkbox',this)">
                                                        <label class="form-check-label" for="checkpermission">{{$group->name}}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 role-{{ $i }}-management-checkbox">
                                                    @php
                                                    $permissions = \App\Models\User::getpermissionByGroupName($group->name);
                                                    $j = 1;
                                                    @endphp
                                                    @foreach($permissions as $permission)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="permissions[]" id="permission-{{$permission->id}}" value="{{$permission->name}}">
                                                            <label class="form-check-label" for="permission-{{$permission->id}}">{{$permission->name}}</label>
                                                        </div>
                                                        @php $j++; @endphp
                                                    @endforeach

                                                </div>

                                            </div>

                                            @php $i++; @endphp

                                        @endforeach


                                    </div>



                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary" id="save_role_button">Save Role</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->


                </div>
            </div>
        </div>

        {{--                role modal End --}}

        {{--  Permission modal End --}}

        <div class="modal fade " id="permissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create Permission</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Default box -->
                    <div class="card">
                        {{-- @include('errors.message') --}}
                        <div class="card-body">
                            <form action="{{route('admin.permission.store')}}" method="post" id="permissionForm">
                                @csrf
                                <div class="card-body" id="formfield">
                                    <div class="form-group">
                                        <label for="rolename">Permission Group Name</label>
                                        <input type="text" class="form-control" name="permission_group_name" placeholder="Permission Group" >
                                        <x-input-error  :errorId="'permission_group_name_error'"/>
                                    </div>
                                    <div class="form-group">
                                        <label for="rolename">Permission Name</label>
                                        <input type="text" class="form-control" name="permission_name[]"  placeholder="Permission Name" >
                                        <x-input-error  :errorId="'permission_name_error'"/>
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit " style="margin-left: 20px" class="btn btn-primary" id="save_permission_button">Save Permission</button>
                                </div>
                            </form>
                            <div class="controls">
                                <button class="add float-left" onclick="add()"><i class="fa fa-plus"></i>Add</button>
                                <button class="remove float-right" onclick="remove()"><i class="fa fa-minus"></i>Remove</button>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->


                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User List</h3>
                    @if(session()->has('message'))
                        <span style="margin-left: 100px" class="text-success">{{ session()->get('message') }}</span>
                        @endif

                    <a href="#" class="btn btn-primary btn-sm float-right" style="margin-left: 10px" data-toggle="modal" data-target="#permissionModal"> <i class="fas fa-plus-circle" style="margin-right: 10px"></i> Add Permission</a>
                    <a href="#" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#exampleModal"> <i class="fas fa-plus-circle"></i> Add Role</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <table id="example1" class="table table-bordered table-striped permission_table">
                        <thead>
                        <tr>
                            <th>Sl</th>
                            <th>Role</th>
                            <th>Permission</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        {{-- @foreach($roles as $role)
                            <tr>
                                <td style="width: 5%">{{$loop->iteration}}</td>
                                <td style="width: 5%">{{$role->name}}</td>
                                <td align="center" style="width: 80%">
                                   <a href="#" style="font-weight: bold; font-size:20px;" class="mb-2 view_permission" data-role-id="{{$role->id}}"><i class="fa fa-eye"></i> </a>
                                    <div style="display: none" id="show_permission_{{$role->id}}">
                                        @foreach($role->permissions as $permission)
                                        <button class="btn btn-info btn-sm mb-1"> {{$permission->name}}</button>
                                     @endforeach
                                    </div>

                                </td>
                                <td class="float-right">
                                    <a href="{{route('admin.roles.edit',$role->id)}}" class="btn btn-info btn-sm mb-1 "><i class="fa fa-edit"></i></a>
                                    <a href="{{route('admin.roles.destroy',$role->id)}}" class="btn btn-danger btn-sm mb-1"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                        @endforeach --}}


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

@include('backend.admin.user.permission.partial.scripts')
<script>
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

// user yajra code start
$(document).ready(function() {
    $(function() {

        var table = $('.permission_table').DataTable({

            dom: "lBfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            // "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],
            // buttons: [{
            //     extend: 'pdf',
            //     text: '<i class="fa-thin fa-file-pdf fa-2x"></i><br>PDF',
            //     className: 'pdf btn text-white btn-sm ',
            //     exportOptions: {
            //         columns: [2, 4, 5, 6, 7, 8]
            //     }
            // }, {
            //     extend: 'excel',
            //     text: '<i class="fa-thin fa-file-excel fa-2x"></i><br>Excel',
            //     className: 'pdf btn text-white btn-sm ',
            //     exportOptions: {
            //         columns: [2, 4, 5, 6, 7, 8]
            //     }
            // }, {
            //     extend: 'print',
            //     text: '<i class="fa-thin fa-print fa-2x"></i><br>Print',
            //     className: 'pdf btn text-white btn-sm ',
            //     exportOptions: {
            //         columns: [2, 4, 5, 6, 7, 8]
            //     }
            // }, ],

            pageLength: 25,
            processing: true,
            serverSide: true,
            searchable: true,
            "ajax": {
                "url": "{{ route('admin.roles.index') }}",
                "datatype": "json",
                "dataSrc": "data",
                "data": function(data) {
                    //filter options
                    // data.hrm_department_id = $('#hrm_department_id').val();
                    // data.shift_id = $('#shift_id').val();
                    // data.grade_id = $('#grade_id').val();
                    // data.designation_id = $('#designation_id').val();
                    // data.date_range = $('.submitable_input').val();
                    // data.employment_status = $('#employment_status').val();
                }
            },

            drawCallback: function(settings) {
                // Get DataTables API instance
                // var api = new $.fn.dataTable.Api(settings);

                // // Iterate through each row and add class based on 'status'
                // api.rows().every(function(index, element) {
                //     var status = this.data().sta;
                //     if (status == 0) {
                //         // $(this.node()).addClass('bg-dark');
                //     }
                // });

                // Additional code as needed
                $('#is_check_all').prop('checked', false);

                // // $('#all_item').text('All (' + allRow + ')');
                // $('#is_check_all').prop('checked', false);
                // // $('#trashed_item').text('');
                // // $('#trash_separator').text('');
                // // $("#bulk_action_field option:selected").prop("selected", false);
            },

            columns: [
                {
                name: 'DT_RowIndex',
                data: 'DT_RowIndex',
                sWidth: '3%'
            },

            {
                data: 'role',
                name: 'role',
                width: "5%"
            },
            {
                data: 'permission',
                name: 'permission',
                width: "80%"
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
$(document).ready(function () {
        // Handle form submission
        $('#roleForm').submit(function (e) {
            e.preventDefault();

            // Serialize the form data
            var formData = $(this).serialize();
            $('#save_role_button').text('Loading...');

            // Make Ajax request
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                success: function (response) {

                    $('#exampleModal').modal('hide');
                    toastr.success(response.message);
                    window.location.reload();
                    $('#save_role_button').text('Save Role');

                },
                error: function (xhr) {
                    // Handle error response
                    var errors = xhr.responseJSON.errors;
                    // $('.input-error').text('');
                    // console.log(errors);
                    // return
                    // Display validation errors
                    $.each(errors, function (key, value) {
                        // Display the error messages
                        $('#' + key + '_error').text(value[0]);
                    });
                    $('#save_role_button').text('Save Role');

                }
            });
        });

        // permission add script

        $('#permissionForm').submit(function (e) {
            e.preventDefault();

            // Serialize the form data
            var formData = $(this).serialize();
            $('#save_permission_button').text('Loading...');


            // Make Ajax request
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                success: function (response) {
                    // Handle success response
                    // console.log(response);
                    // return
                    $('#permissionModal').modal('hide');
                    toastr.success(response.message);
                    window.location.reload();
                    $('#save_permission_button').text('Save Permission');
                },
                error: function (xhr) {
                    // Handle error response
                    var errors = xhr.responseJSON.errors;
                    // $('.input-error').text('');
                    // console.log(errors);
                    // return
                    // Display validation errors
                    $.each(errors, function (key, value) {
                        // Display the error messages
                        $('#' + key + '_error').text(value[0]);
                    });
                    $('#save_permission_button').text('Save Permission');
                }
            });
        });
    });


    // add permission field javascript
    var formfield = document.getElementById('formfield');

    function add(){
        var newField = document.createElement('input');
        newField.setAttribute('type','text');
        newField.setAttribute('name','permission_name[]');
        newField.setAttribute('class','form-control mb-4');
        newField.setAttribute('size',50);
        newField.setAttribute('placeholder','Permission Name');
        formfield.appendChild(newField);
    }

    function remove(){
        var input_tags = formfield.getElementsByTagName('input');
        if(input_tags.length > 2) {
            formfield.removeChild(input_tags[(input_tags.length) - 1]);
        }
    }

    // add permission field javascript end




        $(document).on('click','.view_permission', function(){
            var roleId = $(this).data("role-id");
            $("#show_permission_" + roleId).toggle();
        })
</script>


@endpush
