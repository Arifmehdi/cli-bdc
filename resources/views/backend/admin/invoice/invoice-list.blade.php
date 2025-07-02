@extends('backend.admin.layouts.master')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <section class="content">

                <!-- Default box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All Invoice</h3>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">

                        <table class="table table-bordered table-striped invoice_table">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Invoice_no</th>
                                    <th>Dealer</th>
                                    <th>Type</th>
                                    <th>Total</th>
                                    <th>Payment method</th>
                                    <th>Create date</th>
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

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(function() {

var table = $('.invoice_table').DataTable({

    dom: "lBfrtip",
    buttons: ["copy", "csv", "excel", "pdf", "print"],

    pageLength: 25,
    processing: true,
    serverSide: true,
    searchable: true,
    "ajax": {
        "url": "{{ route('admin.invoice.list') }}",
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
            data: 'generated_id',
            name: 'generated_id',
            sWidth: '10%'
        },
        {
            data: 'dealer_name',
            name: 'dealer_name',

        }, {
            data: 'type',
            name: 'type',

        },
        {
            data: 'total',
            name: 'total',

        },
        {
            data: 'Payment method',
            name: 'Payment method',

        },
        {
            data: 'Payment date',
            name: 'Payment date',

        },{
            data: 'Status',
            name: 'Status',

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


        });

    $(document).on('click', ".invoice-delete", function(e){
    e.preventDefault();
    let id = $(this).data('id');


    $.confirm({
        title: 'Delete Confirmation',
        content: 'Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-danger',
                action: function () {

                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function () {

                    $.ajax({
                        url: "{{ route('admin.invoice.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.invoice_table').DataTable().draw(false);
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


// change status code
//     $(document).on('change', ".change_status", function(e){
//     e.preventDefault();
//     let id = $(this).data('id');
//     let inventory_id = $(this).data('inventory_id');

//     $.ajax({
//         url: "{{ route('admin.invoice.change_status') }}",
//         type: 'post',
//         data: {
//             id: id,
//             inventory_id: inventory_id,
//         },
//         success: function (response) {
//             if (response.status == "success") {
//                 toastr.success(response.message);
//                 $('.invoice_table').DataTable().draw(false);
//             }
//         },
//         error: function (error) {
//             // Show Toastr error message
//             toastr.error(error.responseJSON.message);
//         }
//     });

// });

$(document).on('change', ".change_status", function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let change_status = $(this).val();
            $.confirm({
                title: 'Status Confirmation',
                content: 'Are you sure?',
                buttons: {
                    cancel: {
                        text: 'No',
                        btnClass: 'btn-danger',
                        action: function() {}
                    },
                    confirm: {
                        text: 'Yes',
                        btnClass: 'btn-primary',
                        action: function() {
                            $.ajax({
                                url: "{{ route('admin.single.invoice.change.status') }}",
                                type: 'post',
                                data: {
                                    id: id,
                                    status: change_status
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        toastr.success(response.message);
                                        $('.invoice_table').DataTable().draw(false);
                                    }
                                },
                                error: function(error) {
                                    toastr.error(error.responseJSON.message);
                                }
                            });
                        }
                    }
                }
            });
        });


    </script>
@endpush
