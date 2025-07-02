@extends('backend.admin.layouts.master')

@section('content')
  <style>


#trashed_item {
    /* Add your styles for the trashed item text here */
    font-weight: bold;
    margin-right: 7px;
}
.dataTables_filter input[type="search"] {

    margin-top:15px !important;

}
  </style>
    {{-- send lead email modal start --}}
    <!-- Modal -->
    <div class="modal fade" id="sendLeadMail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Send Mail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="mail_send_form" action="{{ route('admin.dealer.email.send') }}" method="post">
                        @csrf
                        <input type="hidden" name="send_id" id="send_id">
                        <input class="form-control" type="text" name="mail" id="mail"
                            placeholder="Enter dealer mail">
                            <span class="email_error text-danger" id="mail-error"></span>
                        <button type="submit" class="btn btn-info mt-2 float-right" id="send_email_button">Send Mail</button>
                    </form>

                </div>

            </div>
        </div>
    </div>
    {{-- send lead email modal end --}}

    {{-- message modal start --}}


    <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Message With Buyer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-0">
                    <div style="background-image: url('/frontend/assets/images/w.jpg');"
                        class="col-md-12 col-lg-12 clearfix col-sm-12 col-xs-12 p-4">

                        <div class="message-details">
                            <div id="message_all" style="width: 100%; height: 500px; overflow-y: auto; padding-top:7px">
                            </div>

                        </div>


                        <div style="margin-top:70px">
                            <form id="messageSend" action="{{ route('admin.message.send') }}" method="POST">
                                @csrf
                                <input type="hidden" name="lead_id" id="lead_id" value="" />
                                <input type="hidden" name="receiver_id" id="receiver_id" value="" />

                                <input class="form-control" style="width:85%; border-radius:6px; height:50px" id="message"
                                    name="message" placeholder="Type Message here">

                                <button type="submit"
                                    style="background:rgb(3, 131, 103); color:white; float:right; margin-top:-50px; padding-top:12px; padding-bottom:12px; padding-left:25px; padding-right:25px"
                                    class="btn">Submit</button>
                            </form>
                        </div>
                    </div>



                </div>

            </div>
        </div>
    </div>
    {{-- message modal end --}}

    <div class="col-md-12">
        <section class="content">

            <!-- Default box -->
            <div class="card">
            <form id="bulk_action_form" action="{{ route('admin.inventory.bulk-action') }}" method="POST">
            @csrf
                <div class="card-header">
                    <div class="row mb-4">
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="make">Inventory make : </label>
                            <select class="form-control mb-3 submitable" id="makeData">
                                <option value="">Choose Make</option>
                                @foreach ($inventory_make as $makeData => $index)
                                    <option value="{{ $makeData }}">{{ $makeData }}</option>
                                @endforeach
                                <!-- Add your options here -->
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="dealerName">Dealer name : </label>
                            <select class="form-control mb-3 submitable" id="dealerName">
                                <option value="">Choose Dealer</option>
                                @foreach ($inventory_dealer_name as $dealerName => $index)
                                    <option value="{{ $dealerName }}">{{ $dealerName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="dealerCity">Dealer City : </label>
                            <select class="form-control mb-3 submitable" id="dealerCity">
                                <option value="">Choose City</option>
                                @foreach ($inventory_dealer_city as $cityData => $index)
                                    <option value="{{ $cityData }}">{{ $cityData }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="dealerState">Dealer State : </label>
                            <select class="form-control submitable" id="dealerState">
                                <option value="">Choose State</option>
                                @foreach ($inventory_dealer_state as $stateData => $index)
                                    <option value="{{ $stateData }}">{{ $stateData }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="date">Inventory date : </label>
                            <input class="form-control submitable date_range"  id="inventory_date" placeholder="Date" />
                        </div> -->
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="date">Inventory date : </label>
                            <input class="form-control submitable" type="date"  id="inventory_date" placeholder="Date" />
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="imgCount">Image No : </label>
                            <select class="form-control submitable" id="imgCount">
                                <option value="">Choose Image No</option>
                                <option value="0">0 Image</option>
                                <option value="1">1 Image</option>
                                <option value="2">2 Images</option>
                                <option value="3">3 Images</option>
                                <option value="4">4 Images</option>
                                <option value="5">5 Images</option>
                            </select>
                        </div>
                    </div>

                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <table class="table table-bordered table-striped inventory_table">
                        <thead>
                            <tr>
                                <th class="text-start">
                                    <div>
                                        <input type="checkbox" id="is_check_all">
                                    </div>
                                </th>
                                <th class="text-start">{{ __('All Img (Active)') }}</th>
                                <th>Source_url</th>
                                <th>Titles</th>
                                <th>Vin</th>
                                <th>All Image</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card-body -->

            <!-- /.card -->

        </section>
    </div>

    <!-- Delete Form -->
    <form id="delete_form" action="" method="post">
        @method('DELETE')
        @csrf
    </form>
@endsection


@push('js')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // user yajra code start
        $(document).ready(function() {
            $(function() {
                var allRow = '';
                var trashedRow = '';

                var table = $('.inventory_table').DataTable({
                    dom: "lBfrtip",
                    buttons: ["copy", "csv", "excel", "pdf", "print"],

                    pageLength: 25,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    "ajax": {
                        "url": "{{ route('admin.noimage.inventory.list') }}",
                        "datatype": "json",
                        "dataSrc": "data",
                        "data": function(data) {
                            data.make_data = $('#makeData').val();
                            data.dealer_name = $('#dealerName').val();
                            data.dealer_city = $('#dealerCity').val();
                            data.dealer_state = $('#dealerState').val();
                            data.img_count = $('#imgCount').val();
                            data.inventory_date = $('#inventory_date').val();

                            //send types of request for colums
                            data.showTrashed = $('#trashed_item').attr('showtrash');
                        }
                    },
                    "drawCallback": function(data) {
                        allRow = data.json.allRow;
                        trashedRow = data.json.trashedRow;
                        $('#all_item').text('All (' + allRow + ')');
                        $('#is_check_all').prop('checked', false);
                        $('#trashed_item').text('');
                        $('#trash_separator').text('');
                        $("#bulk_action_field option:selected").prop("selected", false);
                        if (trashedRow > 0) {
                            $('#trash_separator').text('|');
                            $('#trashed_item').text('Trash (' + trashedRow + ')');
                        }
                        if (trashedRow < 1) {
                            $('#all_item').addClass("font-weight-bold");
                        }
                    },
                    initComplete: function() {

                        var toolbar = `<div style="" class="d-flex">
                                        <div class="me-2 mt-3">
                                                <a href="#" style="color:#2688cd;" class="font-weight-bold" id="all_item">All</a>
                                                <span style="color:#2688cd; margin-right:3px;" id="trash_separator"></span><a style="color:#2688cd" href="#" id="trashed_item"></a>
                                        </div>
                                        <div class="form-group row  mt-2">
                                            <div class="col-8" >
                                                <select name="action_type" id="bulk_action_field" class="form-control submit_able form-select" required>
                                                    <option value="" selected>Bulk Actions</option>
                                                    <option value="restore_from_trash" id="restore_option">Restore From Trash</option>
                                                    <option value="move_to_trash" id="move_to_trash">Move To Trash</option>
                                                    <option value="listingInvoice" id="listingInvoice">Move To Invoice</option>
                                                    <option value="active" id="active">Visibility active</option>
                                                    <option value="inactive" id="inactive">Visibility inactive</option>
                                                    <option value="delete_permanently" id="delete_option">Delete Permanently</option>
                                                </select>
                                            </div>

                                            <div class="col-4 me-5">
                                                <button style="padding-left:28px; padding-right:28px" type="submit" id="filter_button" class="btn btn-md btn-info">Apply</button>
                                            </div>
                                        </div>
                                    </div>`;

                        $("div.dataTables_filter").prepend(toolbar);
                        $("div.dataTables_filter").addClass('d-flex justify-content-between');
                        $("#restore_option").css('display', 'none');
                        $("#delete_option").css('display', 'none');
                        $("#move_to_trash").css('display', 'block');
                        $('#all_item').text('All (' + allRow + ')');
                        $('#is_check_all').prop('checked', false);
                        $('#trashed_item').text('');
                        $('#trash_separator').text('');
                        $("#bulk_action_field option:selected").prop("selected", false);
                        if (trashedRow > 0) {
                            $('#trash_separator').text('|');
                            $('#trashed_item').text('Trash (' + trashedRow + ')');
                        }

                    },

                    columns: [
                        {
                            name: 'check',
                            data: 'check',
                            sWidth: '5%',
                            orderable: false,
                            targets: 0
                        },

                        {
                            name: 'local_image_num',
                            data: 'local_image_num',
                            sWidth: '3%'
                        },

                        {
                            data: 'detail_url',
                            name: 'detail_url',
                            searchable: true,
                        },
                        {
                            data: 'title',
                            name: 'title',
                            searchable: true,
                        },
                        {
                            data: 'vin',
                            name: 'vin',
                            searchable: true,
                        },
                        {
                            data: 'img_from_url',
                            name: 'img_from_url',
                        }

                    ],
                    lengthMenu: [
                        [10, 25, 50, 100, 500, 1000, 5000, 10000, 20000, 50000, 100000, -1],
                        [10, 25, 50, 100, 500, 1000, 5000, 10000, 20000, 50000, 100000, "All"]
                    ],

                });
                table.buttons().container().appendTo('#exportButtonsContainer');



                                //Bulk Action
                $('#bulk_action_form').on('submit', function(e) {
                    e.preventDefault();
                    var url = $(this).attr('action');
                    var request = $(this).serialize();


                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: request,
                        success: function(response) {
                            if(response.status == 'success')
                            {
                                toastr.options.timeOut = 500;
                                toastr.success(response.message);
                                updateCartData();
                            }else
                            {
                                toastr.options.timeOut = 500;
                                toastr.success(response);
                            }
                            $('.inventory_table').DataTable().draw(false);
                            },
                        error: function(error) {
                            toastr.error(error.responseJSON.message);
                        }
                    });
                });

                $(document.body).on('click', '#is_check_all', function(event) {
                    // alert('Checkbox clicked!');
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

        //trashed item
        $(document).on('click', '#trashed_item', function(e) {
            e.preventDefault();
            $(this).attr("showtrash", true);
            $('.check1').prop('checked', false)
            $(this).addClass("font-weight-bold");
            $('.inventory_table').DataTable().draw(false);
            $('#is_check_all').prop('checked', false);
            $('#all_item').removeClass("font-weight-bold");
            $("#delete_option").css('display', 'block');
            $("#restore_option").css('display', 'block');
            $("#move_to_trash").css('display', 'none');
            $("#listingInvoice").css('display', 'none');
        })

        //all item
        $(document).on('click', '#all_item', function(e) {
            e.preventDefault();
            trashed_item = $('#trashed_item');
            $('#is_check_all').prop('checked', false);
            $('.check1').prop('checked', false);
            trashed_item.attr("showtrash", false);
            $(this).addClass("font-weight-bold");
            $('.inventory_table').DataTable().draw(false);
            $('#trashed_item').removeClass("font-weight-bold")
            $("#delete_option").css('display', 'none');
            $("#restore_option").css('display', 'none');
            $("#move_to_trash").css('display', 'block');
            $("#listingInvoice").css('display', 'block')
        })

            // restore method
            $(document).on('click', '.restore', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            $.confirm({
                'title': 'Restore Confirmation',
                'message': 'Are you sure?',
                'buttons': {
                    'No': {
                        'btnClass': 'btn-danger',
                        'action': function() {}
                    },
                    'Yes': {
                        btnClass: 'btn-primary',
                        'action': function() {
                            $('.data_preloader').show();
                            $.ajax({
                                url: url,
                                type: 'get',
                                success: function(data) {
                                    $('.inventory_table').DataTable().draw(false);
                                    $('.data_preloader').hide();
                                    toastr.success(data);
                                },
                                error: function(err) {
                                    $('.data_preloader').hide();
                                    if (err.status == 0) {
                                        toastr.error(
                                            'Net Connetion Error. Reload This Page.');
                                    } else {
                                        toastr.error(
                                            'Server Error. Please contact to the support team.'
                                            );
                                    }
                                }
                            });
                        }
                    },

                }
            });
        });

        $(document).on('click', '.c-delete', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $('#delete_form').attr('action', url);

            $.confirm({
                'title': 'Delete Confirmation',
                'message': 'Are you sure?',
                'buttons': {
                    'No': {
                        'btnClass': 'no btn-danger',
                        'action': function() {}
                    },
                    'Yes': {
                        'btnClass': 'yes btn-primary',
                        'action': function() {
                            // $('#delete_form').submit();
                            $.ajax({
                                url: url,
                                type: 'DELETE',
                                success: function(data) {
                                    toastr.success(data);
                                    $('.loading_button').hide();
                                    $('.lead_table').DataTable().draw(false);
                                },
                                error: function(error) {
                                    $('.loading_button').hide();
                                    toastr.error(error.responseJSON.message);
                                }
                            });
                        }
                    },

                }
            });
        });
            // delete lead
            $(document).on('click', ".inventory_delete", function(e) {
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
                                // Use the 'id' from the outer scope
                                $.ajax({
                                    url: "{{ route('admin.inventory.delete') }}",
                                    type: 'post',
                                    data: {
                                        id: id
                                    },
                                    success: function(response) {
                                        if (response.status == "success") {
                                            toastr.success(response.message);
                                            $('.inventory_table').DataTable().draw(false);
                                        }
                                    },
                                    error: function(error) {
                                        // Show Toastr error message
                                        toastr.error(error.responseJSON.message);
                                    }
                                });
                            }
                        }
                    }
                });
            });
        });

        // message related code
        $(document).ready(function() {
            $(document).on('click', '.message_view', function() {
                let id = $(this).data('id');


                $.ajax({
                    url: "{{ route('admin.message.view') }}",
                    type: "get",
                    data: {
                        id: id
                    },
                    success: function(res) {
                        console.log(res.lead_info.user_id);
                        if (res.status ===
                            'success') { // Accessing status from the response object
                            $('#messageModal').modal('show');
                            $('#message_all').empty();
                            $('#lead_id').val(res.lead_info.id);
                            $('#receiver_id').val(res.lead_info.user_id);
                            if (res.status == 'success' && res.data.length > 0) {
                                res.data.forEach(function(message) {
                                    console.log(message);

                                    var formattedTime = new Intl.DateTimeFormat(
                                        'en-US', {
                                            day: 'numeric',
                                            month: 'short',
                                            hour: 'numeric',
                                            minute: 'numeric',
                                            hour12: true
                                        }).format(new Date(message.created_at));

                                    console.log(message);

                                    if (message.sender_id == {{ Auth::id() }}) {
                                        $('#message_all').append(
                                            '<div class="first-message" style="padding-bottom:10px !important"><p><span class="date-america" style="float:right;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                            formattedTime +
                                            '</span></p><p class="sender" style="color:black;background-color:#B0E0E6;padding:10px; border-radius:3px; margin-left:300px">' +
                                            message.message + '</p></div>');

                                    } else {
                                        $('#message_all').append(
                                            '<div style="padding-bottom:10px !important"><p><span class="date-date" style="margin-left:5px;float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                            formattedTime +
                                            '</span></p><p class="receive" style="color:black;background-color:#F0FFF0;padding:10px;border-radius:3px;margin-right:260px;">' +
                                            message.message + '</p></div>');
                                    }
                                });
                            }
                        }
                    }
                });
            });

            $(document).on('submit', '#messageSend', function(e) {
                e.preventDefault();


                let message = $('#message').val();

                if (message === '') {
                    alert('Must be type a message');
                } else {
                    var formData = new FormData($(this)[0]);

                    $.ajax({
                        processData: false,
                        contentType: false,
                        url: $(this).attr('action'),
                        type: $(this).attr('method'),
                        data: formData,
                        success: function(res) {
                            console.log(res);

                            if (res.status === 'success' && res.data.created_at) {
                                // Check if res.data.created_at is available before using it
                                var formattedTime = new Intl.DateTimeFormat('en-US', {
                                    day: 'numeric',
                                    month: 'short',
                                    hour: 'numeric',
                                    minute: 'numeric',
                                    hour12: true
                                }).format(new Date(res.data.created_at));

                                $('#message').val('');

                                if (res.data.sender_id === {{ Auth::id() }}) {

                                    $('#message_all').append(
                                        '<div class="first-message" style="padding-bottom:10px !important"><p><span class="date-america" style="float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                        formattedTime +
                                        '</span></p><p class="sender" style="color:black;background-color:#B0E0E6;padding:10px; border-radius:3px; margin-right:260px">' +
                                        res.data.message + '</p></div>');
                                }

                                // Show toastr success message
                                toastr.success(res.message);
                            }
                        },
                        error: function(xhr) {
                            // Handle error response
                            var errors = xhr.responseJSON.errors;

                            // Display validation errors dynamically
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').text(value[0]);
                            });
                        }
                    });
                };
            });

            $(document).on('click', '.send-mail', function() {
                var id = $(this).data("id");
                $('#send_id').val(id);
                $('#sendLeadMail').modal('show');

                // $.ajax({
                //     url: "{{ route('admin.dealer.email.send') }}",
                //     type: 'post',
                //     data: {
                //         id: id
                //     },
                //     dataType: 'json',
                //     success: function(res) {

                //         console.log(res);
                //         if (res.status == 'success') {
                //             toastr.success(res.message);
                //         }
                //     },
                //     error: function(xhr, status, error) {

                //         console.error("AJAX Error:", status, error);

                //     }
                // });
            });

            // Bind submit event handler to the form with ID 'mail_send_form'
            $(document).on('submit', '#mail_send_form', function(e) {
                // Prevent the default form submission behavior
                e.preventDefault();

                // Create a new FormData object from the form
                var formData = new FormData($(this)[0]);
                $('#send_email_button').text('Loading..');
                // Perform an AJAX request
                $.ajax({
                    url: $(this).attr('action'), // URL to submit the form to
                    type: $(this).attr('method'), // HTTP method (e.g., POST)
                    data: formData, // Form data to submit
                    processData: false, // Don't process the data (required for FormData)
                    contentType: false, // Don't set content type (required for FormData)
                    success: function(res) { // Callback function for successful response
                        console.log(res); // Log the response to the console
                        if (res.status == 'success') {
                            toastr.success(res.message); // Show success message using toastr
                            $('#sendLeadMail').modal(
                            'hide'); // Hide the modal with ID 'sendLeadMail'
                            $('#send_email_button').text('Send Mail');
                        }
                        if (res.errors) {
                            $.each(res.errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                        $('#send_email_button').text('Send Mail');
                        }
                    },
                    error: function(xhr) { // Callback function for error response
                        // Handle error response
                        var errors = xhr.responseJSON.errors;
                        // Display validation errors dynamically

                    }
                });
            });
        })
    </script>

<!-- Include Moment.js -->
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

<!-- Include Date Range Picker CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<!-- Include jQuery UI CSS and JS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize the date range picker
        $('.date_range').daterangepicker({
            autoUpdateInput: false, // Prevent auto updating of input on selection
            locale: {
                format: 'YYYY-MM-DD' // Date format
            },
            opens: 'left', // Position the calendar dropdown
        });

        // Handle the selection of a date range
        $('.date_range').on('apply.daterangepicker', function(ev, picker) {
            // Update the input field with the selected date range
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' : ' + picker.endDate.format('YYYY-MM-DD'));

            // Optional: Process start and end dates separately
            console.log('Start Date:', picker.startDate.format('YYYY-MM-DD'));
            console.log('End Date:', picker.endDate.format('YYYY-MM-DD'));
        });
    });

</script>

@endpush
