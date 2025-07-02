@extends('backend.admin.layouts.master')
@section('content')

            <div class="col-md-12">
                {{--                Edit user  modal--}}
                <div class="modal fade" id="editPermissionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Edit Permission</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('admin.permission.update') }}" method="post">
                                @csrf
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label for="username">Group Name</label>
                                        <input type="text" class="form-control" name="permission_group_name" id="permission_group_name"
                                               placeholder="permission group name">
                                        <input type="hidden" name="permission_group_id" id="permission_group_id">
                                    </div>
                                    <div class="form-group">
                                        <label for="username">Permission Name</label>
                                        <input type="text" class="form-control" name="permission_name" id="permission_name"
                                               placeholder="permission name">
                                    </div>


                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Update Permission</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            {{--               End Edit user modal--}}

            {{--  Add Permission Model  start--}}

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

                                <div class="card-body">
                                    <form action="{{route('admin.permission.store')}}" method="post"  id="permissionForm">
                                        @csrf
                                        <div class="card-body" id="formfield">
                                            <div class="form-group">
                                                <label for="rolename">Permission Group Name</label>
                                                <input type="text" class="form-control" name="permission_group_name" placeholder="Permission Group" >
                                                <x-input-error  :errorId="'permission_group_name_error'"/>
                                            </div>
                                            <div class="form-group">
                                                <label for="rolename">Permission Name</label>
                                                <input type="text" class="form-control" name="permission_name[]"  placeholder=" Permission Name" >
                                                <x-input-error  :errorId="'permission_name_error'"/>
                                            </div>
                                        </div>
                                        <!-- /.card-body -->

                                        <div class="card-footer">
                                            <button type="submit " style="margin-left: 20px" class="btn btn-primary">Save Permission</button>
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

            {{--  Add Permission Model  End--}}

            <!-- Main content -->
                <section class="content">

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Permission List</h3>
                            @if(session()->has('message'))
                                <span style="margin-left: 100px" class="text-success">{{ session()->get('message') }}</span>
                            @endif
                            <a href="#" class="btn btn-primary btn-sm float-right" style="margin-left: 10px" data-toggle="modal" data-target="#permissionModal"> <i class="fas fa-plus-circle" style="margin-right: 10px"></i> Add Permission</a>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example1" class="table table-bordered table-striped permission_table ">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Group Name</th>
                                    <th>Permission</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                {{-- @foreach($permissions as $permission)
                                    <tr>
                                        <td>{{$loop->iteration}}</td>
                                        <td>{{$permission->group_name}}</td>
                                        <td>{{$permission->name}} </td>
                                        <td class="btn btn-group float-right">
                                            <a href="javascript:void(0);" data-id ="{{$permission->id}} " class="btn btn-info btn-sm editPermissionBtn"><i class="fa fa-edit"></i></a>
                                            <a href="{{route('admin.permission.edit',$permission->id)}}" class="btn btn-info btn-sm mb-1 "><i class="fa fa-edit"></i></a>--}}
{{--
                                            <form id="btndelete{{$permission->id}}" action="{{route('admin.permission.destroy',$permission->id)}}" method="POST"
                                                  style="display:inline">
                                                @csrf
                                                <a href="javascript:void(0);" class="btn btn-danger btn-sm" id ="{{ $permission->id }}" onclick="btnPermissionDelete(this.id)"><i class="fa fa-trash"></i></a>
                                            </form>

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



@endsection
@push('js')
<script>

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
                "url": "{{ route('admin.permission.index') }}",
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
                data: 'group_name',
                name: 'group_name',
            },
            {
                data: 'permission',
                name: 'permission',
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


        // Edit user jquery
        $(document).ready(function (){
            $('.editBtn').on('click',function (){
                $("#editUserModal").modal('show');

                $tr = $(this).closest('tr');

                var data = $tr.children('td').map(function (){
                    return $(this).text();
                }).get();

                console.log(data);
                var us_id = $(this).attr("data-id");

                $('#us_id').val(us_id);
                $('#user_name').val(data[1]);
                $('#user_email').val(data[2]);
                $('#user_phone').val(data[3]);
                $('#user_role').val(data[4]);
            });


        });
        // End Edit user jquery
        function btnDelete(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                    $('#btndelete'+id).submit();
                };
            });
        }

        function btnPermissionDelete(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                    )
                    $('#btndelete'+id).submit();
                };
            });
        }

        // Edit Permission jquery
        $(document).ready(function (){
            $(document).on('click','.editPermissionBtn',function (){

               $("#editPermissionModal").modal('show');

                $tr = $(this).closest('tr');

                var data = $tr.children('td').map(function (){
                    return $(this).text();
                }).get();

                console.log(data);
                var permission_id = $(this).attr("data-id");

                $('#permission_group_id').val(permission_id);
                $('#permission_group_name').val(data[1]);
                $('#permission_name').val(data[2]);
             });


        });
        // End Edit Permission jquery

        // permission add
        $('#permissionForm').submit(function (e) {
            e.preventDefault();

            // Serialize the form data
            var formData = $(this).serialize();

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
                }
            });
        });
</script>

@endpush


