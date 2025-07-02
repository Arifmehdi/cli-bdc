@php
use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <!-- Main content -->
        <section class="content row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header" id="creatModal">
                        <h3 class="card-title" id="header-title">Add Vehicle Year</h3><br>
                        <hr>
                        <form action="{{route('admin.years.store')}}" method="post" id="userForm">
                            @csrf

                            <div class="form-group">
                                <label for="year">Select a Year:</label>
                                <select name="year" class="form-control" id="createYear">
                                    <option value="" selected>-- Choose Year --</option>
                                    <?php
                                    $currentYear = date('Y');
                                    for ( $currentYear; $currentYear >=1991 ; $currentYear--) {
                                        echo "<option value=\"$currentYear\">$currentYear</option>";
                                    }
                                    ?>
                                </select>
                                <span class="text-danger error-message" id="year-error"></span>
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
                        <h3 class="card-title">Vehicle Year List</h3>
                        {{--<a href="" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                            data-target="#vehicleModal"> <i class="fas fa-plus-circle"></i> Add Make</a>--}}
                    </div>
                    <div class="card">
                        {{--<div class="card-header">
                        <h3 class="card-title">DataTable with default features</h3>
                    </div>--}}
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table  class="table table-bordered table-striped year-table table-responsive">
                                <thead>
                                    <tr>
                                        <th>
                                            <div>
                                                <input type="checkbox" id="is_check_all">
                                            </div>
                                        </th>
                                        <th>SL.</th>
                                        <th>Year</th>
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
                                        <th>Year</th>
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
    
$.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

$(document).ready(function() {
    $(function() {

        var table = $('.year-table').DataTable({

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
                "url": "{{ route('admin.years.index') }}",
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
                    data: 'year',
                    name: 'year'
                },
                {
                    data: 'stat',
                    name: 'stat'
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
function dataStore() {

    var year = $('#createYear').val();
    var statusValue = $('input[name="status"]:checked').val();

    $.ajax({
        url: "{{ route('admin.years.store') }}",
        type: 'post',
        data: {
            year: year,
            status: statusValue,
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#userForm')[0].reset();
            $('.year-table').DataTable().draw(false);
            toastr.success(response.success);
            // console.log(response.success)
        },
        error: function(error) {
            // console.log(error.responseJSON.errors.make[0]);
            // Display error messages in the modal
            var errors = error.responseJSON.errors;
            toastr.error(errors.year[0]);
            if (errors && errors.make) {
                $('#make-error').text(errors.make[0]);
                $('#status-error').text(errors.status[0]);
            } else {
                // Handle other errors as needed
            }
        }
    })

}

function addform()
{
    var html = '<h3 class="card-title" id="header-title">Add Vehicle Year</h3><br><hr>' +
            '<form  method="post" id="userForm">@csrf' +
            '<div class="form-group">' +
            '<label for="year">Select a Year:</label>'+
            '<select name="year" class="form-control" id="createYear">'+
            '<option value="" selected>-- Choose Year --</option>';

            var currentYear = new Date().getFullYear();
            for (var year = currentYear; year >= 1991; year--) {
                html += '<option value="' + year + '">' + year + '</option>';
            }
            html +=  '</select>'+
            '<span class="text-danger error-message" id="year-error"></span>' +
            '</div>' +
            '<div class="form-group">' +
            '<label for="status">Status : </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
            '<input type="radio" name="status" class="editStatus" value="1" checked>&nbsp;&nbsp;&nbsp;Active&nbsp;&nbsp;&nbsp;' +
            '<input type="radio" name="status" class="editStatus" value="0">&nbsp;&nbsp;&nbsp;Inactive' +
            '<span class="text-danger error-message" id="status-error"></span>' +
            '</div>' +
            '<div class="modal-footer">' +
            '<button type="button" class="btn btn-secondary" id="editCloseBtn">Close</button>' +
            '<button type="submit" id="storeBtn" class="btn btn-primary">Submit</button>' +
            '</div>' +
            '</form>';
        $('#creatModal').html(html);
        $('#header-title').text('Add Vehicle Year')
}


$(document).ready(function() {
    $(document).on('click', '#storeBtn', function(e) {
        e.preventDefault();

        dataStore();
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
                    url: "{{ route('admin.years.destroy', '') }}" + '/' + id,
                    type: 'DELETE',
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        toastr.success(res.success);
                        $('.year-table').DataTable().draw(false);
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

    $(document).on('click', '.edit', function(e) {
        e.preventDefault();
        var id = $(this).data('edit');
        $('#userForm').hide();
        $.ajax({
                url: "{{ url('admin/years') }}/" + id + "/edit",
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    console.log('ksjfhjfhjh'+res.makeData)
                    // toastr.success(res.success);
                    $('.year-table').DataTable().draw(false);
                    $('#header-title').text('Edit Vehicle Make');

                    var html = '<h3 class="card-title" id="header-title">Edit Vehicle Make</h3><br><hr>' +
                        '<form  method="post" id="editForm">@csrf' +
                        '<div class="form-group">' +


                        '<label for="year">Select a Year:</label>'+
                        '<select name="year" class="form-control" id="editYear">'+
                        '<option value="" selected>-- Choose Year --</option>';
                        var currentYear = new Date().getFullYear();
                        for (var year = currentYear; year >= 1991; year--) {
                            html += '<option value="' + year + '" ' + (res.yearData == year ? 'selected' : '') + '>' + year + '</option>';
                        }
                        html += '</select>' +
                        '<span class="text-danger error-message" id="year-error"></span>' +
                        '</div>' +
                        '<input type="hidden" name="idData" id="editIdData" value="'+res.idData+'">' +
                        '<span class="text-danger error-message" id="make-error"></span>' +
                        '</div>' +
                        '<div class="form-group">' +
                        '<label for="status">Status : </label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' +
                        '<input type="radio" name="status" value="1" '+(res.statusData == 1 ? 'checked' : '')+'>&nbsp;&nbsp;&nbsp;Active&nbsp;&nbsp;&nbsp;' +
                        '<input type="radio" name="status" value="0" '+(res.statusData == 0 ? 'checked' : '')+'>&nbsp;&nbsp;&nbsp;Inactive' +
                        '<span class="text-danger error-message" id="status-error"></span>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                        '<button type="button" class="btn btn-secondary" id="editCloseBtn">Close</button>' +
                        '<button type="submit" id="editStoreBtn" class="btn btn-primary">Submit</button>' +
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

    $(document).on('click','#editStoreBtn', function(e){
        e.preventDefault()
        var id = $('#editIdData').val();
        var year = $('#editYear').val();
        var statusValue = $('input[name="status"]:checked').val();
        var updateRoute = '{{ route("admin.years.update", ["year" => ":id"]) }}';
        var url = updateRoute.replace(':id', id);

        // alert(id+ ' ' +year + ' '+statusValue)
        $.ajax({
            url: url,
           type: 'PUT',
           data: {id:id, year:year, status:statusValue},
           success:function(res){
                console.log('res');
                toastr.success(res.success);
                addform();
                $('.year-table').DataTable().draw(false);
           },
           error:function(error){

           }

        });
    });

});
</script>
@endpush