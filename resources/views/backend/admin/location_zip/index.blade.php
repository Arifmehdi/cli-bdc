@php
use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')

@section('content')

<div class="row">
    <div class="col-md-12">
        <!-- Main content -->
        <section class="content row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header" id="creatModal">
                        <h3 class="card-title" id="header-title">Add Location City</h3><br>
                        <hr>
                        <form action="{{route('admin.zips.store')}}" method="post" id="userForm">
                            @csrf

                            <div class="form-group">
                                <label for="state_name">State Name</label>
                                <select name="state_name" id="state_id" class="form-control" >
                                    <option value="">Select State</option>
                                    @foreach($states as $key => $state)
                                    <option value="{{ $key }}">{{ $state }}</option>
                                    @endforeach
                                </select>
                                <span class="text-danger error-message" id="state_id-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="city_name">City Name</label>
                                <select name="city_name" id="city_data" class="form-control" >
                                    <option value="">Select City</option>
                                </select>
                                <span class="text-danger error-message" id="city_id-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="zip_code">Zip Code</label>
                                <input type="number" class="form-control" name="zip_code" placeholder="Enter Zip Code" id="zip_data">
                                <span class="text-danger error-message" id="zip_data-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="latitude_data">Latitude</label>
                                <input type="number" class="form-control" name="latitude_data" placeholder="Enter Latitude" id="latitude_data">
                                <span class="text-danger error-message" id="latitude_data-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="longitude_data">Longitude</label>
                                <input type="number" class="form-control" name="longitude_data" placeholder="Enter Longitude" id="longitude_data">
                                <span class="text-danger error-message" id="longitude_data-error"></span>
                            </div>
                            <div class="form-group">
                                <label for="sales_tax">Sales Tax</label>
                                <input type="number" class="form-control" name="sales_tax" placeholder="Enter Sales TAX" id="sales_tax">
                                <span class="text-danger error-message" id="sales_tax-error"></span>
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
            <div class="col-md-8">
                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Location State List</h3>
                        {{--<a href="" class="btn btn-primary btn-sm float-right" data-toggle="modal"
                            data-target="#vehicleModal"> <i class="fas fa-plus-circle"></i> Add Make</a>--}}
                    </div>
                    <div class="card">
                        {{--<div class="card-header">
                        <h3 class="card-title">DataTable with default features</h3>
                    </div>--}}
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table  class="table table-bordered table-striped state-table table-responsive">
                                <thead>
                                    <tr>
                                        <th>
                                            <div>
                                                <input type="checkbox" id="is_check_all">
                                            </div>
                                        </th>
                                        <th>SL.</th>
                                        <th>State Name</th>
                                        <th>City Name</th>
                                        <th>Zip Code</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Sales Tax (%)</th>
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
                                        <th>State Name</th>
                                        <th>City Name</th>
                                        <th>Zip Code</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Sales Tax (%)</th>
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

        var table = $('.state-table').DataTable({

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
                "url": "{{ route('admin.zips.index') }}",
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
                    data: 'location_state_name',
                    name: 'location_state_name'
                },
                {
                    data: 'city_name',
                    name: 'city_name'
                },
                {
                    data: 'zip_code',
                    name: 'zip_code'
                },
                {
                    data: 'latitude',
                    name: 'latitude'
                },
                {
                    data: 'longitude',
                    name: 'longitude'
                },
                {
                    data: 'sales_tax',
                    name: 'sales_tax'
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

    var state_id = $('#state_id').val();
    var city_id = $('#city_data').val();
    var zip_data = $('#zip_data').val();
    var latitude_data = $('#latitude_data').val();
    var longitude_data = $('#longitude_data').val();
    var sales_tax = $('#sales_tax').val();
    var statusValue = $('input[name="status"]:checked').val();

    $.ajax({
        url: "{{ route('admin.zips.store') }}",
        type: 'post',
        data: {
            state_id: state_id,
            city_id: city_id,
            zip_data: zip_data,
            latitude_data: latitude_data,
            longitude_data: longitude_data,
            sales_tax: sales_tax,
            status: statusValue,
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('#userForm')[0].reset();
            $('#vehicleModal').modal('hide')
            $('.state-table').DataTable().draw(false);
            $('#state_id-error').text('');
            $('#city_id-error').text('');
            $('#zip_data-error').text('');
            $('#sales_tax-error').text('');
            $('#status-error').text('');
            toastr.success(response.success);
            // console.log(response.success)
        },
        error: function(error) {
            var errors = error.responseJSON.errors;

            // Clear previous error messages
            $('#state_id-error').text('');
            $('#city_id-error').text('');
            $('#zip_data-error').text('');
            $('#sales_tax-error').text('');
            $('#status-error').text('');

            // Display error messages if they exist
            if (errors.state_id) {
                $('#state_id-error').text(errors.state_id[0]);
                toastr.error(errors.state_id[0]);
            }
            if (errors.city_id) {
                $('#city_id-error').text(errors.city_id[0]);
                toastr.error(errors.city_id[0]);
            }
            if (errors.zip_data) {
                $('#zip_data-error').text(errors.zip_data[0]);
                toastr.error(errors.zip_data[0]);
            }
            if (errors.sales_tax) {
                $('#sales_tax-error').text(errors.sales_tax[0]);
                toastr.error(errors.sales_tax[0]);
            }
            if (errors.status) {
                $('#status-error').text(errors.status[0]);
                toastr.error(errors.status[0]);
            }
        }
    })

}

function addform()
{
    var html = '<h3 class="card-title" id="header-title">Add Location State</h3><br><hr>' +
                `
                <form action="{{route('admin.zips.store')}}" method="post" id="userForm">
                    @csrf
                    <div class="form-group">
                        <label for="state_name">State Name</label>
                        <select name="state_name" id="state_id" class="form-control">
                            <option value="">Select State</option>
                            @foreach($states as $key => $state)
                            <option value="{{ $key }}">{{ $state }}</option>
                            @endforeach
                        </select>
                        <span class="text-danger error-message" id="state_id-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="city_name">City Name</label>
                        <select name="city_name" id="city_data" class="form-control">
                            <option value="">Select City</option>
                        </select>
                        <span class="text-danger error-message" id="city_id-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="zip_code">Zip Code</label>
                        <input type="number" class="form-control" name="zip_code" placeholder="Enter Your Zip Code" id="zip_data">
                        <span class="text-danger error-message" id="zip_data-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="latitude_data">Latitude</label>
                        <input type="number" class="form-control" name="latitude_data" placeholder="Enter Your Latitude" id="latitude_data">
                        <span class="text-danger error-message" id="latitude_data-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="longitude_data">Longitude</label>
                        <input type="number" class="form-control" name="longitude_data" placeholder="Enter Your Longitude" id="longitude_data">
                        <span class="text-danger error-message" id="longitude_data-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="sales_tax">Sales Tax</label>
                        <input type="number" class="form-control" name="sales_tax" placeholder="Enter Sales TAX" id="sales_tax">
                        <span class="text-danger error-message" id="sales_tax-error"></span>
                    </div>
                    <div class="form-group">
                        <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="1" checked>&nbsp;&nbsp;&nbsp;Active
                        &nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="0">&nbsp;&nbsp;&nbsp;Inactive
                        <span class="text-danger error-message" id="status-error"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="storeBtn" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            `
        $('#creatModal').html(html);
        $('#header-title').text('Add Location State')
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
                    url: "{{ route('admin.zips.destroy', '') }}" + '/' + id,
                    type: 'DELETE',
                    data: {
                        id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        toastr.success(res.success);
                        $('.state-table').DataTable().draw(false);
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
                url: "{{ url('admin/zips') }}/" + id + "/edit",
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    $('html, body').animate({ scrollTop: 0 }, 'slow');
                    console.log(res)
                    // toastr.success(res.success);
                    $('.state-table').DataTable().draw(false);
                    $('#header-title').text('Edit Location State');

                    var html = `
                            <h3 class="card-title" id="header-title">Edit Location State</h3><br><hr>
                            <form method="post" id="editForm">
                                @csrf
                                <div class="form-group">
                                    <label for="state_name">State Name</label>
                                    <select name="state_id" id="state_id" class="form-control">
                                        <option value="">Select State</option>
                                        @foreach($states as $key => $state)
                                        <option value="{{ $key }}" ${res.stateData === "{{ $state }}" ? 'selected' : ''}>{{ $state }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger error-message" id="state-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="city">City Name</label>
                                    <select name="city_name" id="editCity" class="form-control edit_city_id">
                                        <option value="">Select City</option>`;
                                        
                        // Populate city dropdown options dynamically
                        for (var key in res.cities) {
                            if (res.cities.hasOwnProperty(key)) {
                                html += `<option value="${key}" ${res.cityData === res.cities[key] ? 'selected' : ''}>${res.cities[key]}</option>`;
                            }
                        }

                        html += `
                                    </select>
                                    <span class="text-danger error-message" id="city-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="zip_code">Zip Code</label>
                                    <input type="number" class="form-control" name="zip_code" id="editZipCode" placeholder="Enter Your Zip Code" value="${res.zip_code}">
                                    <span class="text-danger error-message" id="zip_code-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="latitude_data">Latitude</label>
                                    <input type="text" class="form-control" name="latitude_data" placeholder="Enter Your Latitude" id="latitude_data" value="${res.latitude_data}">
                                    <span class="text-danger error-message" id="latitude_data-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="longitude_data">Longitude</label>
                                    <input type="text" class="form-control" name="longitude_data" placeholder="Enter Your Longitude" id="longitude_data" value="${res.longitude_data}">
                                    <span class="text-danger error-message" id="longitude_data-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="sales_tax">Sales Tax</label>
                                    <input type="text" class="form-control" name="sales_tax" placeholder="Enter Sales TAX" id="sales_tax" value="${res.sales_tax}">
                                    <span class="text-danger error-message" id="sales_tax-error"></span>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status : </label>&nbsp;&nbsp;&nbsp;
                                    &nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="1" ${res.status == 1 ? 'checked' : ''}>&nbsp;&nbsp;&nbsp;Active
                                    &nbsp;&nbsp;&nbsp;<input type="radio" name="status" value="0" ${res.status == 0 ? 'checked' : ''}>&nbsp;&nbsp;&nbsp;Inactive
                                    <span class="text-danger error-message" id="status-error"></span>
                                </div>
                                <input type="hidden" name="idData" id="editIdData" value="${res.idData}">
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="editCloseBtn">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        `;
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

    $(document).on('submit', '#editForm', function(e) {
    e.preventDefault();

    var formData = new FormData($(this)[0]);

    $.ajax({
        url: '{{ route("admin.zip.update")}}',
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(res) {

            toastr.success('States updated successfully');
            addform();
            $('.state-table').DataTable().draw(false);
        },
        error: function(error) {
            console.log(error);
            toastr.error(error.responseJSON.errors.make_name[0]);
        }
    });
});

    $(document).on('change', '#state_id,.edit_state_id', function(){
        var stateId = $(this).val();
    $.ajax({
        url: '{{ route("admin.make.search", ":stateId") }}'.replace(':stateId', stateId),
        type: 'post',
        success: function(res){
            var $cityData = $('#city_data, .edit_city_id');
            $cityData.empty(); // Clear any existing options

            // Append a default option
            $cityData.append('<option value="">Select a city</option>');

            // Iterate over the response and create new options
            $.each(res, function(id, city_name) {
                $cityData.append('<option value="' + id + '">' + city_name + '</option>');
            });
        },
        error: function(error){
            console.error(error); // Handle the error
        }
    })
    });

});
</script>
@endpush
