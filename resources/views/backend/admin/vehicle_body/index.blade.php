@php
use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')
@push('css')


@endpush
@section('content')

<div class="row">
    <div class="col-md-12">
        <!-- Main content -->
        <section class="content row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header" id="creatModal">
                        <h3 class="card-title" id="header-title">Add Vehicle Body</h3><br>
                        <hr>
                        <form action="{{route('admin.body.store')}}" method="post" id="userForm" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="make">Body Name</label>
                                <input type="text" class="form-control" name="body_name" id="createBody"
                                    placeholder="Enter Body">
                                <span class="text-danger error-message" id="body-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="make">image</label>
                                <input type="file" class="dropify" name="body_image">
                                <span class="text-danger error-message" id="image-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                                &nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="1"
                                    checked>&nbsp;&nbsp;&nbsp;Active
                                &nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="0">&nbsp;&nbsp;&nbsp;Inactive
                                <span class="text-danger error-message" id="status-error"></span>
                            </div>


                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="submit" id="storeBtn" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Vehicle Body List</h3>
                        {{--<a href="" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                            data-target="#vehicleModal"> <i class="fas fa-plus-circle"></i> Add Make</a>--}}
                    </div>
                    <div class="card">
                        {{--<div class="card-header">
                        <h3 class="card-title">DataTable with default features</h3>
                    </div>--}}
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table  class="table table-bordered table-striped body-table table-responsive">
                                <thead>
                                    <tr>
                                        <th>
                                            <div>
                                                <input type="checkbox" id="is_check_all">
                                            </div>
                                        </th>
                                        <th>SL.</th>
                                        <th>Vehicle Body</th>
                                        <th>Vehicle Slug</th>
                                        <th>Vehicle Image</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th>
                                            <div>
                                                <input type="checkbox" id="is_check_all">
                                            </div>
                                        </th>
                                        <th>SL.</th>
                                        <th>Vehicle Body</th>
                                        <th>Vehicle Slug</th>
                                        <th>Vehicle Image</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.card-body -->
            </div>

            <!-- /.card -->

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

        var table = $('.body-table').DataTable({

            dom: "lBfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],

            pageLength: 25,
            processing: true,
            serverSide: true,
            searchable: true,
            "ajax": {
                "url": "{{ route('admin.body.index') }}",
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

                // // $('#all_item').text('All (' + allRow + ')');
                // $('#is_check_all').prop('checked', false);
                // // $('#trashed_item').text('');
                // // $('#trash_separator').text('');
                // // $("#bulk_action_field option:selected").prop("selected", false);
            },

            columns: [{
                    name: 'check',
                    data: 'check',
                    sWidth: '5%',
                    orderable: false,
                    targets: 0
                },
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
                    data: 'slug',
                    name: 'slug'
                },
                {
                    data: 'image',
                    name: 'image'
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

// submit data form

function dataStore() {
    var formData = new FormData($('#userForm')[0]);

    $.ajax({
        url: "{{ route('admin.body.store') }}",
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#userForm')[0].reset();
            $('.body-table').DataTable().draw(false);
            toastr.success(response.message);
        },
        error: function(error) {
            var errors = error.responseJSON.errors;

            if (errors && errors.body_name) {
                $('#body-error').text(errors.body_name[0]);
            } else {
                $('#body-error').text(''); // Clear any previous error messages
            }

            if (errors && errors.body_image) {
                $('#image-error').text(errors.body_image[0]);
            } else {
                $('#image-error').text('');
            }


        }
    });
}


function addform()
{
    var url_info =  "{{route('admin.body.store')}}";
    var html = '<h3 class="card-title" id="header-title">Add Vehicle Body</h3><br><hr>' +
    '<form action='+url_info+'method="post" id="userForm" enctype="multipart/form-data"> @csrf' +

                           ' <div class="form-group">' +
                                '<label for="make">Body Name</label>' +
                               ' <input type="text" class="form-control" name="body_name" id="createBody" placeholder="Enter Body">' +
                                '<span class="text-danger error-message" id="body-error"></span>' +
                            ' </div>' +

                           ' <div class="form-group">' +
                               ' <label for="make">image</label>' +
                               ' <input type="file" class="dropify" name="body_image">' +
                               ' <span class="text-danger error-message" id="image-error"></span>' +
                           ' </div>' +
                            '<div class="form-group">' +
                            '<label for="status">Status : </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                            '<input type="radio" name="status" class="editStatus" value="1" checked>&nbsp;&nbsp;&nbsp;Active&nbsp;&nbsp;&nbsp;' +
                            '<input type="radio" name="status" class="editStatus" value="0">&nbsp;&nbsp;&nbsp;Inactive' +
                            '<span class="text-danger error-message" id="status-error"></span>' +
                            '</div>'


                           ' <div class="modal-footer">' +
                               ' <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>' +
                               ' <button type="submit" id="storeBtn" class="btn btn-primary">Submit</button>' +
                            '</div>' +
                        '</form>';


        $('#creatModal').html(html);
        $('#header-title').text('Add Vehicle Body')
}


$(document).ready(function() {
    $(document).on('click', '#storeBtn', function(e) {
        e.preventDefault();

        dataStore();
    });



    $(document).on('click', '.edit', function(e) {
        e.preventDefault();
        var id = $(this).data('edit');
        $('#userForm').hide();
        $.ajax({
                url: "{{ url('admin/body') }}/" + id + "/edit",
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    // console.log('ksjfhjfhjh'+res.makeData)
                    // toastr.success(res.success);
                    $('.body-table').DataTable().draw(false);
                    $('#header-title').text('Edit Vehicle body');

                    var html = '<h3 class="card-title" id="header-title">Edit Vehicle Make</h3><br><hr>' +
                        '<form  method="post" id="editForm" enctype="multipart/form-data">@csrf' +
                        '<div class="form-group">' +
                        '<label for="make"> Name</label>' +
                        '<input type="text" class="form-control" name="name"  placeholder="Edit body" value="'+res.bodyData+'">' +
                        '<input type="hidden" name="idData" id="editIdData" value="'+res.idData+'">' +
                        '<span class="text-danger error-message" id="make-error"></span>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label for="make">Image</label>' +
                        ' <input type="file" class="dropify" name="body_image" >' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label for="make">Image</label>' +
                        '<img src="' + "{{ asset('storage/') }}/" + res.imageData + '" height="200px" width="50%"/>'+
                        '</div>' +
                        '<div class="form-group">' +
                        '<label for="status">Status : </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                        '<input type="radio" name="status" value="1" '+(res.statusData == 1 ? 'checked' : '')+'>&nbsp;&nbsp;&nbsp;Active&nbsp;&nbsp;&nbsp;' +
                        '<input type="radio" name="status" value="0" '+(res.statusData == 0 ? 'checked' : '')+'>&nbsp;&nbsp;&nbsp;Inactive' +
                        '<span class="text-danger error-message" id="status-error"></span>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                        '<button type="button" class="btn btn-secondary">Close</button>' +
                        '<button type="submit" class="btn btn-primary">Submit</button>' +
                        '</div>' +
                        '</form>';
                    $('#creatModal').html(html);
                },
                error: function(error) {
                    // Show Toastr error message
                    toastr.error(error.responseJSON.message);

                }
            });
    })
    $(document).on('click', '#editCloseBtn', function(e) {
        e.preventDefault();
        addform();
    })
    // $(document).on('mouseout', '.edit', function(e) {
    //     e.preventDefault();
    //     $('#userForm').show();
    //     $('#header-title').text('Add Vehicle Make')
    // })

    // $(document).on('click','#editMake', function(e){
    //     e.preventDefault()
    //     editmake()
    // });

    $(document).on('submit','#editForm', function(e){
        e.preventDefault()

        var formData = new FormData($(this)[0]);

        $.ajax({
            url: '{{ route("admin.body.update") }}',
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            success: function(res) {

                console.log(res);

                toastr.success(res.message);
                addform();
                $('.body-table').DataTable().draw(false);
            },
                error: function(error) {
                    toastr.error(error.responseJSON.errors.make_name[0]);
                }
            });


    })

});



$(document).on('click', '.delete', function(e) {
        e.preventDefault()
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            customClass: {
                confirmButton: 'order-2', // Reversed order
                cancelButton: 'order-1', // Reversed order
            },
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                // Swal.fire({
                // title: "Deleted!",
                // text: "Your file has been deleted.",
                // icon: "success"
                // });
                var id = $(this).data('id');

                $.ajax({
                    url: "{{ route('admin.body.destroy', '') }}" + '/' + id,
                    type: 'DELETE',
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        toastr.success(res.success);
                        $('.body-table').DataTable().draw(false);
                    },
                    error: function(error) {
                        // Show Toastr error message
                        toastr.error(error.responseJSON.message);
                    }
                });
            } else {
                swal.fire({
                    title: "Cancelled",
                    text: "Your imaginary file is safe :)",
                    icon: "error",
                    showConfirmButton: false,
                    timer: 1000
                });
            }
        });
    });
</script>
@endpush
