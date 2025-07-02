@extends('backend.admin.layouts.master')
@section('content')
    <div class="row">
                {{--   add membership modal --}}
        <div class="modal fade" id="membershipAddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Create Membership</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('admin.membership.add') }}" method="post" id="membershipAdd"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="name">Name <span style="color: red; font-weight:bold">*</span></label>
                                <input type="text" class="form-control" name="name" id="name"
                                    placeholder="Enter Your Membership Name">
                                <span class="text-danger error-message" id="name-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="price">Price <span style="color: red; font-weight:bold">*</span></label>
                                <input type="text" class="form-control" name="price" id="price"
                                    placeholder="Enter Your Membership Price">
                                <span class="text-danger error-message" id="price-error"></span>
                            </div>


                            <div class="form-group">
                                <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddOne" name="status" value="1"
                                    checked>&nbsp;&nbsp;&nbsp;<label for="statusAddOne">Active</label>
                                &nbsp;&nbsp;&nbsp;<input type="radio" id="statusAddTwo" name="status"
                                    value="0">&nbsp;&nbsp;&nbsp;<label for="statusAddTwo">Inactive</label>
                                <span class="text-danger error-message" id="status-error"></span>
                            </div>
                            <button type="submit"
                                style="float:right; margin-bottom:8px; padding-left:25px; padding-right:25px; font-size:15px"
                                class="btn btn-success" id="membership_btn">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{--  End add membership modal --}}


        {{-- membership edit modal start --}}

        <div class="modal fade" id="MembershipEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Membership Edit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>

                    </div>
                    <div class="modal-body">
                        <form id="membershipEditFrom" action="{{ route('admin.membership.update') }}" method="POST"
                            class="form-horizontal mt-2 sales-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="membership_id" id="membership_id">
                            <div class="form-group">
                                <label for="name">Name <span style="color: red; font-weight:bold">*</span></label>
                                <input type="text" class="form-control" name="up_name" id="up_name"
                                    placeholder="Enter Your Membership Name">
                                <span class="text-danger error-message" id="up_name-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="price">Price <span style="color: red; font-weight:bold">*</span></label>
                                <input type="text" class="form-control" name="up_price" id="up_price"
                                    placeholder="Enter Your Membership Price">
                                <span class="text-danger error-message" id="up_price-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="status">Status : </label>
                                <input type="radio" id="statusAddOneEdit" name="status" value="1"> Active
                                <input type="radio" id="statusAddTwoEdit" name="status" value="0"> Inactive
                                <span class="text-danger error-message" id="status-error"></span>
                            </div>
                            <button type="submit" class="btn btn-primary float-right mt-4" id="update_button">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        {{-- membership edit modal close --}}
        <div class="col-md-12">
            <section class="content">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Membership List</h3>
                        <a href="" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                            data-target="#membershipAddModal"> <i class="fas fa-plus-circle"></i> Add Membership</a>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered table-striped membership_table">
                            <thead>
                                <tr>
                                    <th>SL</th>

                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Date</th>
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
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $(function() {
                var table = $('.membership_table').DataTable({
                    dom: "lBfrtip",
                    buttons: ["copy", "csv", "excel", "pdf", "print"],
                    pageLength: 25,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    "ajax": {
                        "url": "{{ route('admin.membership') }}",
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
                            sWidth: '27%'
                        },
                        {
                            data: 'membership_price',
                            name: 'membership_price',
                            sWidth: '25%'
                        },
                        {
                            data: 'date',
                            name: 'date',
                            sWidth: '15%'
                        },
                        {
                            data: 'status',
                            name: 'status',
                            sWidth: '10%'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            sWidth: "15%",
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
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
        $(document).on('submit', '#membershipAdd', function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            // Set the content of the editor to an empty string
            $('#membership_btn').text('Loading...');
            var errorFields = ['name', 'price', 'type'];
            errorFields.forEach(function (field) {
                $('#' + field + '-error').text('');
            });
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
                        $.each(res.errors, function(key, value) {
                            $('#' + key + '-error').html(value[0]);
                        });
                        $('#membership_btn').text('Submit');
                    }
                    if (res.status === 'success') {
                        $('.membership_table').DataTable().draw(false);
                        toastr.success(res.message);
                        $('#membershipAdd')[0].reset();
                        $('#membershipAddModal').modal('hide');
                        $('#membership_btn').text('Submit');
                    }
                },

                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key + '-error').text(value[0]);
                    });
                    $('#membership_btn').text('Submit');
                }
            });
        });
        $(document).on('click', '.editMeb', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let name = $(this).data('name');
            let price = $(this).data('price');
            let status = $(this).data('status');
            $('#membership_id').val(id);
            $('#up_name').val(name);
            $('#up_price').val(price);
            console.log(status);
            if (status == 1) {
            $('#statusAddOneEdit').prop('checked', true);  // Active
            $('#statusAddTwoEdit').prop('checked', false); // Inactive
        } else {
            $('#statusAddOneEdit').prop('checked', false); // Active
            $('#statusAddTwoEdit').prop('checked', true);  // Inactive
        }
            $('#MembershipEdit').modal('show');
        });
        // news edit code
        $(document).on('submit', '#membershipEditFrom', function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $('#update_button').text('Loading...');
    $.ajax({
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {
            console.log(res);
            if (res.status === 'success') {
                $('#MembershipEdit').modal('hide');
                $('.membership_table').DataTable().draw(false);
                toastr.success(res.message);
            } else if (res.errors) {
                var errors = res.errors;
                // Handle displaying the errors to the user, if any
                // For example, you could iterate through errors and display them
                $.each(errors, function(key, value) {
                    toastr.error(value);
                });
            }
            $('#update_button').text('Update');
        },
        error: function(error) {
            console.log(error);
            toastr.error('An error occurred while processing your request.');
            $('#update_button').text('Update');
        }
    });
});

$(document).on('click', "#membership_delete", function(e){
    e.preventDefault();
    let id = $(this).data('id');
    $.confirm({
        title: 'Delete Confirmation',
        content: 'Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-danger',
                action: function () {}
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function () {
                    $.ajax({
                        url: "{{ route('admin.membership.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.membership_table').DataTable().draw(false);
                            }
                        },
                        error: function (error) {
                            // Show Toastr error message
                            toastr.error(error.responseJSON.message);
                        }
                    });
                }
            }
        }
    });
});
$(document).on('change', '.action-select', function(e){
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    url:"{{route('admin.membership.status.change')}}",
                    method:"post",
                    data:{id:id},
                    success:function(res){
                        console.log(res)
                        toastr.success(res.message);
                        $('.membership_table').DataTable().draw(false);
                    }
                })
            });

    </script>
@endpush
