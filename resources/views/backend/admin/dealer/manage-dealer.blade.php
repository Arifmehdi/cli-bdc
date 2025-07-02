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
                            <h5 class="modal-title" id="exampleModalLabel">Create New Dealer</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{route('admin.dealer.store')}}" method="post" id="userForm">
                            @csrf
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="name">Name <span style="color: red;font-weight:bold">*</span></label>
                                <input type="text" class="form-control" name="name" id="name"
                                       placeholder="Enter Name" >
                                  <span class="text-danger error-message" id="name-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone <span style="color: red;font-weight:bold">*</span></label>
                                    <input  class="form-control telephoneInput" name="phone" id="Phone"
                                    placeholder="Enter Phone" >
                                    <span class=" text-danger error-message" id="phone-error"></span>
                                </div>
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" class="form-control" name="address" id="address"
                                placeholder="Enter Address" >
                                <span class=" text-danger error-message" id="address-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="city">City </label>
                                <input type="text" class="form-control" name="city" id="city"
                                placeholder="Enter city" >
                                <span class="text-danger error-message" id="city-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="state">State </label>
                                <input type="text" class="form-control" name="state" id="state"
                                placeholder="Enter state" >
                                <span class="text-danger error-message" id="state-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="zip_code">Zip Code </label>
                                <input type="text" class="form-control" name="zip_code" id="zip_code"
                                placeholder="Enter zip code" >
                                <span class="text-danger error-message" id="zip_code-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="status">Status </label>
                                <select name="status" id="status" class="form-control">
                                    <option value="1" checked>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                                  <span class=" text-danger error-message" id="status-error"></span>
                            </div>

                           </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
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
                            <h5 class="modal-title" id="exampleModalLabel">Edit Dealer</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{route('admin.dealer.update')}}" method="post" id="userUpdate">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="name">Name <span style="color: red;font-weight:bold">*</span></label>
                                    <input type="text" class="form-control" name="up_name" id="up_name"
                                           placeholder="Enter Name" >
                                      <span class="text-danger error-message" id="up_name-error"></span>

                                      <input type="hidden" name="id" id="dealer_id">
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone <span style="color: red;font-weight:bold">*</span></label>
                                        <input  class="form-control telephoneInput" name="up_phone" id="up_phone"
                                        placeholder="Enter Phone" >
                                        <span class=" text-danger error-message" id="up_phone-error"></span>
                                    </div>
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" name="up_address" id="up_address"
                                    placeholder="Enter Address" >
                                    <span class=" text-danger error-message" id="address-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="city">City </label>
                                    <input type="text" class="form-control" name="up_city" id="up_city"
                                    placeholder="Enter city" >
                                    <span class="text-danger error-message" id="city-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="state">State </label>
                                    <input type="text" class="form-control" name="up_state" id="up_state"
                                    placeholder="Enter state" >
                                    <span class="text-danger error-message" id="state-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="zip_code">Zip Code </label>
                                    <input type="text" class="form-control" name="up_zip_code" id="up_zip_code"
                                    placeholder="Enter zip code" >
                                    <span class="text-danger error-message" id="zip_code-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status </label>
                                    <select name="up_status" id="up_status" class="form-control">
                                        <option value="1" checked>Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                      <span class=" text-danger error-message" id="status-error"></span>
                                </div>

                               </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{--   End Edit user modal--}}

            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Dealer List</h3>
                        <a href="" class="float-right btn btn-primary btn-sm" data-toggle="modal" data-target="#exampleModal"> <i class="fas fa-plus-circle"></i> Add Dealer</a>
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
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>City</th>
                                <th>State</th>
                                <th>Zip</th>
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
                "url": "{{ route('admin.dealer.manage') }}",
                "datatype": "json",
                "dataSrc": "data",
                "data": function(data) {

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
                data: 'name',
                name: 'name'
            },
            {
                data: 'phone',
                name: 'phone'
            },
            {
                data: 'address',
                name: 'address'
            },
            {
                data: 'city',
                name: 'city'
            },
            {
                data: 'state',
                name: 'state'
            },
            {
                data: 'zip_code',
                name: 'zip_code'
            },
            {
                data: 'status',
                name: 'status'
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

            // Make Ajax request
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                success: function (response) {
                    // Handle success response
                    $('#exampleModal').modal('hide');
                    toastr.success(response.message);
                    $('.user_table').DataTable().draw(false);
                },
                error: function (xhr) {
                    // Handle error response
                    var errors = xhr.responseJSON.errors;
                    // Display validation errors
                    $.each(errors, function (key, value) {
                        // Display the error messages
                        $('#' + key + '-error').text(value[0]);
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
        let city = $(this).data('city');
        let address = $(this).data('address');
        let phone = $(this).data('phone');
        let state = $(this).data('state');
        let status = $(this).data('status');
        let zip_code = $(this).data('zip_code');



        $('#dealer_id').val(id);
        $('#up_name').val(name);
        $('#up_city').val(city);
        $('#up_address').val(address);
        $('#up_phone').val(phone);
        $('#up_zip_code').val(zip_code);
        $('#up_status').val(status);
        $('#up_state').val(state);
       });
      });


    $('#userUpdate').submit(function(e) {
    e.preventDefault();
    var formData = $(this).serialize();
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
    });
    }
});
});





$(document).on('click', '#user_delete', function(e) {
      e.preventDefault();

      let id = $(this).data('id');
    $.confirm({
        title: 'Delete Confirmation',
        content: 'Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-primary',
                action: function () {
                    // Do nothing on cancel
                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-danger',
                action: function () {
                    $.ajax({
                        url:"{{ route('admin.dealer.delete') }}",
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

       // status change
       $(document).on('change', '.action-select', function(e){
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    url:"{{route('admin.dealer.change-status')}}",
                    method:"post",
                    data:{id:id},
                    success:function(res){
                        console.log(res)
                        toastr.success(res.message);
                        $('.user_table').DataTable().draw(false);
                    }
                })
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

</script>

  @endpush
