@php
use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')

@section('content')

    <div class="row">
        <div class="col-md-12">


{{--   add user modal--}}
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Create New User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{route('admin.users.store')}}" method="post" id="userForm">
                            @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="username">Name <span style="color: red;font-weight:bold">*</span></label>
                                <input type="text" class="form-control" name="user_name" id="username"
                                       placeholder="Enter User Name" >
                                  <span class="text-danger error-message" id="user_name-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="user_email">Email <span style="color: red;font-weight:bold">*</span></label>
                                <input type="email" class="form-control" name="user_email" id="userEmail"
                                       placeholder="Enter User E-mail" >
                                  <span class=" text-danger error-message" id="user_email-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="user_email">Address</label>
                                <input type="text" class="form-control" name="user_address" id="userAddress"
                                       placeholder="Enter User Address" >
                                  <span class=" text-danger error-message" id="user_email-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="user_phone">Phone</label>
                                <input  class="form-control telephoneInput" name="user_phone" id="userPhone"
                                       placeholder="Enter User Phone" >

                            </div>
                            <div class="form-group">
                                <label for="user_password">Password <span style="color: red;font-weight:bold">*</span></label>
                                <input type="password" class="form-control" name="user_password" id="userPassword"
                                       placeholder="Enter User Password" >
                                  <span class="text-danger error-message" id="user_password-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="user_password">User Role <span style="color: red;font-weight:bold">*</span></label>
                                <select name="user_role" id="userRole" class="form-control" >
                                    <option value="">~Select Role~</option>
                                    @foreach($roles as $role)
                                        <option value="{{$role->id}}">{{$role->name}}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-message" id="user_role-error"></span>
                            </div>

                           </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" id="create_button">Save changes</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            {{--  End add user modal--}}


            {{--  Edit user  modal--}}
            <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="false">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Edit User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{route('admin.user.update')}}" method="post" id="userUpdate">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="username">Name <span style="color: red;font-weight:bold">*</span></label>
                                    <input type="text" class="form-control" name="up_user_name" id="up_user_name"
                                           placeholder="Enter User Name">
                                    <input type="hidden" name="up_user_id" id="up_us_id">
                                    <span class="text-danger error-message" id="up_user_name-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="user_email">Email <span style="color: red;font-weight:bold">*</span></label>
                                    <input type="email" class="form-control" name="up_user_email" id="up_user_email"
                                           placeholder="Enter User E-mail">
                                           <span class="text-danger error-message" id="up_user_email-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="user_email">Address</label>
                                    <input type="text" class="form-control" name="up_user_address" id="up_user_address"
                                           placeholder="Enter User Address" >

                                </div>
                                <div class="form-group">
                                    <label for="user_phone">Phone</label>
                                    <input  class="form-control telephoneInput" name="up_user_phone" id="up_user_phone"
                                           placeholder="Enter User Phone">
                                </div>
                                <div class="form-group">
                                    <label for="user_password">Password</label>
                                    <input type="text" class="form-control" name="up_user_password" id="up_user_password"
                                           placeholder="Enter User Password">
                                </div>
                                <div class="form-group">
                                    <label for="user_password">User Role <span style="color: red;font-weight:bold">*</span></label>
                                    <select name="up_user_role" id="up_user_role" class="form-control">
                                        <option value="">~Select Role~</option>
                                        @foreach($roles as $role)
                                            <option value="{{$role->name}}">{{$role->name}}</option>
                                        @endforeach

                                    </select>
                                    <span class="text-danger error-message" id="up_user_role-error"></span>
                                </div>

                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button  type="submit" class="btn btn-primary" id="update_user_button">Update User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{--   End Edit user modal--}}
            <!-- Main content -->
            <section class="content">
                <div class="card">
                <div class="card-header">
                        <div class="row mb-4">
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="userRoleFilter">Role : </label>
                                <select class="form-control mb-3 submitable" id="userRoleFilter">
                                    <option value="">Choose Role</option>
                                    @foreach ($roles as $index => $role_info)
                                        <option value="{{ $role_info->id }}">{{ ucfirst($role_info->name) }}</option>
                                    @endforeach
                                    <!-- Add your options here -->
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="userMembership">Membership </label>
                                <select class="form-control mb-3 submitable" id="userMembership">
                                    <option value="">Choose Membership</option>
                                    @foreach ($memberships as $index => $membership)
                                        <option value="{{ $membership->id }}">{{ $membership->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="userStatusFilter">Status: </label>
                                <select class="form-control mb-3 submitable" id="userStatusFilter">
                                        <option value="">Choose Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="dealerState">Dealer State : </label>
                                <select class="form-control" id="dealerState">
                                    <option value="">Choose City</option>
                                    {{--@foreach ($inventory_dealer_state as $stateData => $index)
                                        <option value="{{ $stateData }}">{{ $stateData }}</option>
                                    @endforeach--}}
                                </select>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <label for="date">Inventory date : </label>
                                <input class="form-control" type="date" id="date" placeholder="Date" />
                            </div>
                        </div>




                    </div>
                </div>
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">User List</h3>
                        <a href="" class="float-right btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal"> <i class="fas fa-plus-circle"></i> Add User</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        @if(Session::has('message'))
                            <span class="text-success">{{ session::get('message') }}</span>
                        @endif
                        <table  class="table table-bordered table-striped user_table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Cell Number</th>
                                <th>Role</th>
                                <th>Package</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                    <!-- /.card-body -->

                <!-- /.card -->

            </section>
            <!-- /.content -->
        </div>
    </div>


  @endsection

  @push('js')
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="https://cdnj s.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" ></script>

  @if(Session::has("message"))
      <script>
          toastr.info({{ Session::get('message') }});
      </script>
  @endif
  <script>
      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
  </script>
  <script>
    // user yajra code start
$(document).ready(function() {
    $('.telephoneInput').inputmask('(999) 999-9999');
    $(function() {

        var table = $('.user_table').DataTable({

            dom: "lBfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],

            pageLength: 25,
            processing: true,
            serverSide: true,
            searchable: true,
            "ajax": {
                "url": "{{ route('admin.users') }}",
                "datatype": "json",
                "dataSrc": "data",
                "data": function(data) {
                    data.userRoleFilter = $('#userRoleFilter').val();
                    data.userMembership = $('#userMembership').val();
                    data.userStatusFilter = $('#userStatusFilter').val();
                }
            },

            drawCallback: function(settings) {
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
                data: 'dealer_id',
                name: 'dealer_id'
            },
            {
                data: 'name',
                name: 'users.name'
            },

            {
                data: 'email',
                name: 'email'
            },
            {
                data: 'phone',
                name: 'users.phone'
            },
            {
                data: 'role',
                name: 'role'
            },
            {
                data: 'package',
                name: 'package'
            },
            {
                data: 'status',
                name: 'status'
            },
            {
                data: 'created_at',
                name: 'created_at'
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
    // user yajra code close

$(document).ready(function () {
        // Handle form submission
        $('#userForm').submit(function (e) {
            e.preventDefault();

            // Serialize the form data
            var formData = $(this).serialize();
            $('#create_button').text('Loading...');
            // Clear previous validation errors
        var errorFields = ['user_name', 'user_role', 'user_email', 'user_password'];
        errorFields.forEach(function (field) {
            $('#' + field + '-error').text('');
        });
            // Make Ajax request
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                success: function (response) {
                    // Handle success response
                    console.log(response);
                    $('#exampleModal').modal('hide');
                    $('#userForm')[0].reset();
                    toastr.success(response.message);
                    $('.user_table').DataTable().draw(false);
                    $('#create_button').text('Save changes');
                },
                error: function (xhr) {
                    // Handle error response
                    var errors = xhr.responseJSON.errors;
                    // Display validation errors
                    $.each(errors, function (key, value) {
                        // Display the error messages
                        $('#' + key + '-error').text(value[0]);
                        $('#create_button').text('Save changes');
                    });
                }
            });
        });
    });


    // Edit user jquery
    $(document).ready(function () {
    $(document).on('click', '.editBtn', function () {

        $("#editUserModal").modal('show');

        let id = $(this).data('id');
        let name = $(this).data('name');
        let email = $(this).data('email');
        let address = $(this).data('address');
        let phone = $(this).data('phone');
        let role = $(this).data('role');

        console.log(role);

        $('#up_us_id').val(id);
        $('#up_user_name').val(name);
        $('#up_user_email').val(email);
        $('#up_user_address').val(address);
        $('#up_user_phone').val(phone);
        $('#up_user_role').val(role);
       });
      });


    $('#userUpdate').submit(function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
    $('#update_user_button').text('Loading...')
    $.ajax({
    url: $(this).attr("action"),
    method: $(this).attr("method"),
    data: formData,
    success: function(response) {

        console.log(response);
        if (response.status == "success") {
            $("#editUserModal").modal('hide');
            $('.user_table').DataTable().draw(false);
            toastr.success(response.message);
            $('#update_user_button').text('Update User')
            $('#up_user_password').val('')
        }

    },
    error: function (xhr) {
    // Handle error response

    var errors = xhr.responseJSON.errors;
    console.log(errors);
    // Display validation errors
    $.each(errors, function (key, value) {
        // Display the error messages
        $('#' + key + '-error').text(value[0]);
        $('#update_user_button').text('Update User');
    });
    }
});
});
$(document).on('click', '#user_delete', function(e) {
      e.preventDefault();

      let id = $(this).data('id');
    $.confirm({
        title: 'Archive Confirmation',
        content: 'Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-danger',
                action: function () {
                    // Do nothing on cancel
                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function () {
                    $.ajax({
                        url:"{{ route('admin.user.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.user_table').DataTable().draw(false);

                            }

                        },
                        error: function (error) {
                            // Show Toastr error message
                            toastr.error(error.responseJSON.message);
                        }
                    });
                }
            },

        }
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
        $('.editPermissionBtn').on('click',function (){

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


      // package updated
    //   $(document).on('change','.packages',function(e){
    //         e.preventDefault();
    //        var package = $(this).val();
    //        var id = $(this).data('id');
    //        $.confirm({
    //         title: 'Membership Update confirm',
    //         content: 'Are you sure?',
    //         buttons: {
    //             cancel: {
    //                 text: 'No',
    //                 btnClass: 'btn-danger',
    //                 action: function () {
    //                     // Do nothing on cancel
    //                 }
    //             },
    //             confirm: {
    //                 text: 'Yes',
    //                 btnClass: 'btn-success',
    //                 action: function () {
    //                 var link = document.createElement('a');
    //                 link.href = "{{ route('admin.dealer.management.ajax') }}?package=" + package + "&id=" + id;

    //                 if (window.navigator && window.navigator.msSaveOrOpenBlob) {
    //                     // For IE
    //                     window.navigator.msSaveOrOpenBlob(link.href);
    //                 } else {
    //                     // For other browsers

    //                     link.target = '_blank';
    //                     document.body.appendChild(link);
    //                     link.click();
    //                     document.body.removeChild(link);
    //                 }
    //             }
    //             },

    //         }
    //     });

    //  });

    //  new invoice  in membership code
  $(document).on('change','.packages',function(e){
            e.preventDefault();
           var package = $(this).val();
           var id = $(this).data('id');
           var price = $(this).find(':selected').data('price');
           var packageName = $(this).find(':selected').text();
           $.confirm({
            title: 'Confirm Membership Update',
            //content: 'Are you sure you want to update to this membership? <br><strong>Selected Price: $' + price.toFixed(2) + '</strong>',
            content: `
            <div style="text-align: left;">
                <p>You are about to update the membership for this dealer.</p>
                <p><strong>Selected Membership:</strong> ${packageName}</p>
                <p><strong>Price:</strong> $${price.toFixed(2)}</p>
                <p>Do you want to proceed?</p>
            </div>
        `,
            buttons: {
                cancel: {
                    text: 'No',
                    btnClass: 'btn-danger',
                    action: function () {
                        // Do nothing on cancel
                        $('.user_table').DataTable().draw(false);
                    }
                },
                confirm: {
                    text: 'Yes',
                    btnClass: 'btn-success',
                    action: function () {
                        $.ajax({
                        url:"{{ route('admin.dealer.membership.add.cart') }}",
                        type: 'post',
                        data: {
                            id: id,
                            package:package,
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                toastr.options.timeOut = 500;
                                updateCartData();

                            }if (response.status == "error") {
                                toastr.error(response.message);

                            }

                        },
                        error: function (error) {
                            // Show Toastr error message
                            toastr.error(error.responseJSON.message);
                        }
                    });
                }
                },

            }
        });

     });


     //  change user status
  $(document).on('change','.status',function(e){
            e.preventDefault();

           var id = $(this).data('id');
           var status =$(this).val();
           $.confirm({
            title: 'Status Change confirm',
            content: 'Are you sure?',
            buttons: {
                cancel: {
                    text: 'No',
                    btnClass: 'btn-danger',
                    action: function () {
                        // Do nothing on cancel
                        $('.user_table').DataTable().draw(false);
                    }
                },
                confirm: {
                    text: 'Yes',
                    btnClass: 'btn-success',
                    action: function () {
                        $.ajax({
                        url:"{{ route('admin.change.user.status') }}",
                        type: 'post',
                        data: {
                            id: id,
                            status:status
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                updateCartData();

                            }if (response.status == "error") {
                                toastr.error(response.message);

                            }

                        },
                        error: function (error) {
                            // Show Toastr error message
                            toastr.error(error.responseJSON.message);
                        }
                    });
                }
                },

            }
        });

     });

</script>

  @endpush
