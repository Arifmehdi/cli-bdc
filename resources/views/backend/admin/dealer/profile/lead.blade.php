@php
    use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')
@push('css')
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
@endpush
@section('content')

   {{-- send lead email modal start --}}
    <!-- Modal -->
    <div class="modal fade" id="DealersendLeadMail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Send Mail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="dealer_mail_send_form" action="{{ route('admin.dealer.email.send') }}" method="post">
                        @csrf
                        <input type="hidden" name="send_id" id="dealer_send_id">
                        <input class="form-control" type="text" name="mail" id="mail"
                            placeholder="Enter dealer mail">
                        <button type="submit" class="btn btn-info mt-2 float-right send_email_button">Send Mail</button>
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
                        <button type="submit" class="btn btn-info mt-2 float-right">Send Mail</button>
                    </form>

                </div>

            </div>
        </div>
    </div>
    {{-- send lead email modal end --}}

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">

                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" src="{{($user->image) ? asset('frontend/assets/images/').'/'.$user->image : asset('frontend/assets/images/profile.png')}}"
                                    alt="User profile picture">
                                {{-- <a href="#" title="Edit Account" class="float-right"><i class="fa fa-edit"></i></a> --}}
                            </div>

                            <h3 class="profile-username text-center">{{$user->name}}</h3>

                            <p class="text-muted text-center">{{$user->email}}</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Total Inventory</b> <a class="float-right">{{ $data['total_inventory'] ?? '0' }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Lead</b> <a class="float-right">{{ $data['total_lead'] ?? '0' }}</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Total Sold</b> <a class="float-right">0</a>
                                </li>
                                <li class="list-group-item">
                                     <b>Total Invoice</b> <a class="float-right">{{ $data['total_invoice'] ?? '0' }}</a>
                                </li>
                            </ul>


                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                    <!-- About Me Box -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">About Dealer</h3>

                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            @include('backend.admin.dealer.profile.profile_edit')
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">
                                <li class="nav-item"><a class="nav-link " href="{{ route('admin.dealer.profile',$id)}}">Listing</a></li>
                                <li class="nav-item"><a class="nav-link active " href="#">Leads</a></li>
                                <li class="nav-item"><a class="nav-link invoice" href="{{ route('admin.dealer.invoice.show',$id)}}">Invoice</a></li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active" id="leads">
                                    <form id="dealer_bulk_action_form" action="{{ route('admin.lead.bulk-action') }}" method="POST">
                                        @csrf

                                            <table class="table table-bordered table-striped lead_table">
                                                <thead>
                                                    <tr>
                                                        <th class="text-start">
                                                            <div>
                                                                <input type="checkbox" id="lead_check_all">
                                                            </div>
                                                        </th>
                                                        <th class="text-start">{{ __('SL') }}</th>
                                                        <th>Title</th>
                                                        <th>Make</th>
                                                        <th>Dealer</th>
                                                        <th>State</th>
                                                        <th>City</th>
                                                        <th>Customer Name</th>
                                                        <th>Customer Email</th>
                                                        <th>Customer Phone</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>

                                            </table>
                                        </form>

                                </div>

                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->

         <!-- Delete Form -->
    <form id="delete_form" action="" method="post">
        @method('DELETE')
        @csrf
    </form>

    {{-- <form id="lead_tech" action="{{ route('admin.dealer.lead.show') }}" method="get">

        @csrf
    </form> --}}
    </section>
@endsection

@push('js')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('.telephoneInput').inputmask('(999) 999-9999');

    $(function() {
            var allRow = '';
            var trashedRow = '';
            var url = window.location.href;
            var id = url.substring(url.lastIndexOf('/') + 1);
            var table = $('.lead_table').DataTable({
                dom: "lBfrtip",
                buttons: ["copy", "csv", "excel", "pdf", "print"],
                pageLength: 25,
                processing: true,
                serverSide: true,
                searchable: true,
                "ajax": {
                    "url": "/admin/dealer/lead/show/" + id,
                    "datatype": "json",
                    "dataSrc": "data",
                    "data": function(data) {

                        //send types of request for colums
                        data.showTrashed = $('#trashed_item').attr('showtrash');
                    }
                },
                "drawCallback": function(data) {
                    allRow = data.json.allRow;
                    trashedRow = data.json.trashedRow;
                    $('#all_item').text('All (' + allRow + ')');
                    $('#lead_check_all').prop('checked', false);
                    $('#trashed_item').text('');
                    $('#trash_separator').text('');
                    $("#lead_bulk_action_field option:selected").prop("selected", false);
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
                                            <select name="action_type" id="lead_bulk_action_field" class="form-control submit_able form-select" required>
                                                <option value="" selected>Bulk Actions</option>
                                                <option value="restore_from_trash" id="lead_restore_option">Restore From Trash</option>
                                                <option value="invoice" id="invoice">Move To Invoice</option>
                                                <option value="move_to_trash" id="lead_move_to_trash">Move To Trash</option>
                                                <option value="delete_permanently" id="lead_delete_option">Delete Permanently</option>
                                            </select>
                                        </div>
                                        <div class="col-4 me-5">
                                            <button style="padding-left:28px; padding-right:28px" type="submit" id="lead_filter_button" class="btn btn-md btn-info">Apply</button>
                                        </div>
                                    </div>
                                </div>`;
                    $("div.dataTables_filter").prepend(toolbar);
                    $("div.dataTables_filter").addClass('d-flex justify-content-between');
                    $("#lead_restore_option").css('display', 'none');
                    $("#lead_delete_option").css('display', 'none');
                    $("#lead_move_to_trash").css('display', 'block');
                    $('#all_item').text('All (' + allRow + ')');
                    $('#lead_check_all').prop('checked', false);
                    $('#trashed_item').text('');
                    $('#trash_separator').text('');
                    $("#lead_bulk_action_field option:selected").prop("selected", false);
                    if (trashedRow > 0) {
                        $('#trash_separator').text('|');
                        $('#trashed_item').text('Trash (' + trashedRow + ')');
                    }
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
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'make',
                        name: 'make',
                    },
                    {
                        data: 'dealer_name',
                        name: 'dealer_name',
                    },
                    {
                        data: 'state',
                        name: 'state',
                    },
                    {
                        data: 'city',
                        name: 'city',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'email',
                        name: 'email',
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                    },
                    {
                        data: 'action',
                        name: 'action',
                        width: "15%",
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


            //Bulk Action
            $('#dealer_bulk_action_form').on('submit', function(e) {
                e.preventDefault();
                var url = $(this).attr('action');
                var request = $(this).serialize();
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: request,
                    success: function(data) {

                        if(data.status == 'success')
                        {
                            toastr.options.timeOut = 500;
                            toastr.success(data.message);
                            updateCartData();
                        }
                        if(data.status == 'error')
                        {
                            toastr.options.timeOut = 500;
                            toastr.error(data.message);
                        }

                        $('.lead_table').DataTable().draw(false);
                    },
                    error: function(error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });

            $(document.body).on('click', '#lead_check_all', function(event) {
                var checked = event.target.checked;
                if (true == checked) {
                    $('.check1').prop('checked', true);
                }
                if (false == checked) {
                    $('.check1').prop('checked', false);
                }
            });

            $('#lead_check_all').parent().addClass('text-center');

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
                    $('#lead_check_all').prop('checked', true);
                } else {
                    $('#lead_check_all').prop('checked', false);
                }
            });

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
        $('.lead_table').DataTable().draw(false);
        $('#is_check_all').prop('checked', false);
        $('#all_item').removeClass("font-weight-bold");
        $("#lead_delete_option").css('display', 'block');
        $("#lead_restore_option").css('display', 'block');
        $("#lead_move_to_trash").css('display', 'none');
        $("#invoice").css('display', 'none');
    })

    //all item
    $(document).on('click', '#all_item', function(e) {
        e.preventDefault();
        trashed_item = $('#trashed_item');
        $('#is_check_all').prop('checked', false);
        $('.check1').prop('checked', false);
        trashed_item.attr("showtrash", false);
        $(this).addClass("font-weight-bold");
        $('.lead_table').DataTable().draw(false);
        $('#trashed_item').removeClass("font-weight-bold")
        $("#lead_delete_option").css('display', 'none');
        $("#lead_restore_option").css('display', 'none');
        $("#lead_move_to_trash").css('display', 'block');
        $("#invoice").css('display', 'block');
    })





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
        // end lead code here




    // message related code
    $(document).ready(function() {

        $(document).on('click', '.send-mail', function() {
            var id = $(this).data("id");
            $('#send_id').val(id);
            $('#sendLeadMail').modal('show');



        });

        // Bind submit event handler to the form with ID 'mail_send_form'
        $(document).on('submit', '#mail_send_form', function(e) {
            // Prevent the default form submission behavior
            e.preventDefault();

            // Create a new FormData object from the form
            var formData = new FormData($(this)[0]);

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
                    }
                },
                error: function(xhr) { // Callback function for error response
                    // Handle error response
                    var errors = xhr.responseJSON.errors;

                    // Display validation errors dynamically
                    $.each(errors, function(key, value) {
                        $('#' + key + '-error').text(value[0]);
                    });
                }
            });
        });
    })
</script>


{{--working for Lead table here  --}}

<script>



        $(document).on('click', ".lead_delete", function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            $.confirm({
                title: 'Delete Confirmation',
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
                                url: "{{ route('admin.single.lead.delete') }}",
                                type: 'post',
                                data: {
                                    id: id
                                },
                                success: function(response) {
                                    if (response.status == "success") {
                                        toastr.success(response.message);
                                        $('.lead_table').DataTable().draw(false);
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
                                    $('.lead_table').DataTable().draw(false);
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
                    if (res.status === 'success') {
                        $('#messageModal').modal('show');
                        $('#message_all').empty();
                        $('#lead_id').val(res.lead_info.id);
                        $('#receiver_id').val(res.lead_info.user_id);
                        if (res.status == 'success' && res.data.length > 0) {
                            res.data.forEach(function(message) {
                                var formattedTime = new Intl.DateTimeFormat('en-US', {
                                    day: 'numeric',
                                    month: 'short',
                                    hour: 'numeric',
                                    minute: 'numeric',
                                    hour12: true
                                }).format(new Date(message.created_at));

                                if (message.sender_id == {{ Auth::id() }}) {
                                    $('#message_all').append('<div class="first-message" style="padding-bottom:10px !important"><p><span class="date-america" style="float:right;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                        formattedTime + '</span></p><p class="sender" style="color:black;background-color:#B0E0E6;padding:10px; border-radius:3px; margin-left:300px">' +
                                        message.message + '</p></div>');
                                } else {
                                    $('#message_all').append('<div style="padding-bottom:10px !important"><p><span class="date-date" style="margin-left:5px;float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                        formattedTime + '</span></p><p class="receive" style="color:black;background-color:#F0FFF0;padding:10px;border-radius:3px;margin-right:260px;">' +
                                        message.message + '</p></div>');
                                }
                            });
                        }
                    }
                }
            });
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
                        if (res.status === 'success' && res.data.created_at) {
                            var formattedTime = new Intl.DateTimeFormat('en-US', {
                                day: 'numeric',
                                month: 'short',
                                hour: 'numeric',
                                minute: 'numeric',
                                hour12: true
                            }).format(new Date(res.data.created_at));
                            $('#message').val('');
                            if (res.data.sender_id === {{ Auth::id() }}) {
                                $('#message_all').append('<div class="first-message" style="padding-bottom:10px !important"><p><span class="date-america" style="float:left;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                    formattedTime + '</span></p><p class="sender" style="color:black;background-color:#B0E0E6;padding:10px; border-radius:3px; margin-right:260px">' +
                                    res.data.message + '</p></div>');
                            }
                            toastr.success(res.message);
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        $.each(errors, function(key, value) {
                            $('#' + key + '-error').text(value[0]);
                        });
                    }
                });
            };
        });

        $(document).on('click', '.lead_send_mail', function() {
            var id = $(this).data("id");
            $('#dealer_send_id').val(id);
            $('#DealersendLeadMail').modal('show');
        });

        $(document).on('submit', '#dealer_mail_send_form', function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            $('.send_email_button').text('Loading...');
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.status == 'success') {
                        toastr.success(res.message);
                        $('#DealersendLeadMail').modal('hide');
                        $('.send_email_button').text('Send Mail');
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(key, value) {
                        $('#' + key + '-error').text(value[0]);
                    });
                }
            });
        });

        $(document).on('change', "#lead_check_all", function() {
            var atLeastOneChecked = $(".check-row:checked").length > 0;
            $(".check-row").prop('checked', $(this).prop("checked"));
            $('#go_invoice').prop('disabled', (atLeastOneChecked) ? true : false);
        });

        $(document).on('change', ".check-row", function() {
            var atLeastOneChecked = $(".check-row:checked").length > 0;
            if (!$(this).prop("checked")) {
                $("#checkAll").prop("checked", false);
            }
            $('#go_invoice').prop('disabled', (atLeastOneChecked) ? false : true);
        });

        $('#submit_action').on('click', function(){
        var packagePlan = $('#selectPlan').val();

        var listingCheckedRows = $(".check-row:checked");
        var ListingSelectedData = [];
        listingCheckedRows.each(function() {
        var id = $(this).data("id");
        ListingSelectedData.push(id);
        console.log(ListingSelectedData);
        });
        });

</script>


<script>



// change  pdf paid or pending script

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

@include('backend.admin.dealer.profile.profile_js')
@endpush
