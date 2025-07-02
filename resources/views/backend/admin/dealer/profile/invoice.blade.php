@php
    use Illuminate\Support\Facades\Session;
@endphp
@extends('backend.admin.layouts.master')
@push('css')
<style>


    #dealer_trashed_item {
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
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.dealer.profile',$id)}}">Listing</a></li>
                                <li class="nav-item"><a class="nav-link lead" href="{{ route('admin.dealer.lead.show',$id)}}">Leads</a></li>
                                <li class="nav-item"><a class="nav-link active" href="#">Invoice</a></li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">
                                <div class="active" id="invoice">
                                    <form id="dealer_bulk_action_form" action="{{ route('admin.invoice.bulk-action') }}" method="POST">
                                        @csrf

                                    <table class="table table-bordered table-striped invoice_table">
                                        <thead>
                                            <tr>
                                                <th class="text-start">
                                                    <div>
                                                        <input type="checkbox" id="invoice_check_all">
                                                    </div>
                                                </th>
                                                <th class="text-start">{{ __('SL') }}</th>
                                                <th>Invoice no.</th>
                                                <th>Type</th>
                                                <th>Create Date</th>
                                                <th>Payment Date</th>
                                                <th>Payment Method</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                </tbody>

                                        </table>
                                    </form>
                                </div>
                                <!-- /.tab-pane -->
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

        <form id="delete_form" action="" method="post">
            @method('DELETE')
            @csrf
        </form>

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

    $(document).ready(function(){

        $(function() {
            var allRow = '';
            var trashedRow = '';
            var url = window.location.href;
            var id = url.substring(url.lastIndexOf('/') + 1);
            var table = $('.invoice_table').DataTable({
                dom: "lBfrtip",
                buttons: ["copy", "csv", "excel", "pdf", "print"],
                pageLength: 25,
                processing: true,
                serverSide: true,
                searchable: true,
                "ajax": {
                    "url": "/admin/dealer/invoice/show/" + id,
                    "datatype": "json",
                    "dataSrc": "data",
                    "data": function(data) {
                        data.showTrashed = $('#trashed_item').attr('showtrash');
                    }
                },
                "drawCallback": function(data) {
                    allRow = data.json.allRow;
                    trashedRow = data.json.trashedRow;
                    $('#all_item').text('All (' + allRow + ')');
                    $('#invoice_check_all').prop('checked', false);
                    $('#trashed_item').text('');
                    $('#trash_separator').text('');
                    $("#invoice_bulk_action_field option:selected").prop("selected", false);
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
                                            <select name="action_type" id="invoice_bulk_action_field" class="form-control submit_able form-select" required>
                                                <option value="" selected>Bulk Actions</option>
                                                <option value="restore_from_trash" id="invoice_restore_option">Restore From Trash</option>
                                                <option value="move_to_trash" id="invoice_move_to_trash">Move To Trash</option>
                                                <option value="delete_permanently" id="invoice_delete_option">Delete Permanently</option>
                                            </select>
                                        </div>
                                        <div class="col-4 me-5">
                                            <button style="padding-left:28px; padding-right:28px" type="submit" id="lead_filter_button" class="btn btn-md btn-info">Apply</button>
                                        </div>
                                    </div>
                                </div>`;
                    $("div.dataTables_filter").prepend(toolbar);
                    $("div.dataTables_filter").addClass('d-flex justify-content-between');
                    $("#invoice_restore_option").css('display', 'none');
                    $("#invoice_delete_option").css('display', 'none');
                    $("#invoice_move_to_trash").css('display', 'block');
                    $('#all_item').text('All (' + allRow + ')');
                    $('#invoice_check_all').prop('checked', false);
                    $('#trashed_item').text('');
                    $('#trash_separator').text('');
                    $("#invoice_bulk_action_field option:selected").prop("selected", false);
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
                        data: 'generated_id',
                        name: 'generated_id',
                    },
                    {
                        data: 'type',
                        name: 'type',
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                    },
                    {
                        data: 'payment_date',
                        name: 'payment_date',
                    },
                    {
                        data: 'payment_method',
                        name: 'payment_method',
                    },
                    {
                        data: 'status',
                        name: 'status',
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

        });



});


$(document.body).on('click', '#invoice_check_all', function(event) {
                // alert('Checkbox clicked!');
                var checked = event.target.checked;
                if (true == checked) {
                    $('.check1').prop('checked', true);
                }
                if (false == checked) {
                    $('.check1').prop('checked', false);
                }
            });

            $('#invoice_check_all').parent().addClass('text-center');

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
                    $('#invoice_check_all').prop('checked', true);
                } else {
                    $('#invoice_check_all').prop('checked', false);
                }
            });

            //Submit filter form by select input changing
            $(document).on('change', '.submitable', function() {
                table.ajax.reload();
            });




    // user yajra code start


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
                            toastr.success(data.message);
                        }
                        if(data.status == 'error')
                        {
                            toastr.error(data.message);
                        }

                        $('.invoice_table').DataTable().draw(false);
                    },
                    error: function(error) {
                        toastr.error(error.responseJSON.message);
                    }
                });
            });


                 //trashed item
    $(document).on('click', '#trashed_item', function(e) {
        e.preventDefault();
        $(this).attr("showtrash", true);
        $('.check1').prop('checked', false)
        $(this).addClass("font-weight-bold");
        $('.invoice_table').DataTable().draw(false);
        $('#invoice_check_all').prop('checked', false);
        $('#all_item').removeClass("font-weight-bold");
        $("#invoice_delete_option").css('display', 'block');
        $("#invoice_restore_option").css('display', 'block');
        $("#invoice_move_to_trash").css('display', 'none');

    })

    //all item
    $(document).on('click', '#all_item', function(e) {
        e.preventDefault();
        trashed_item = $('#trashed_item');
        $('#invoice_check_all').prop('checked', false);
        $('.check1').prop('checked', false);
        trashed_item.attr("showtrash", false);
        $(this).addClass("font-weight-bold");
        $('.invoice_table').DataTable().draw(false);
        $('#trashed_item').removeClass("font-weight-bold")
        $("#invoice_delete_option").css('display', 'none');
        $("#invoice_restore_option").css('display', 'none');
        $("#invoice_move_to_trash").css('display', 'block');

    })




        $(document).on('click', ".invoice_delete", function(e) {
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
                                url: "{{ route('admin.single.invoice.delete') }}",
                                type: 'post',
                                data: {
                                    id: id
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
                                type: 'post',
                                success: function(data) {
                                    $('.invoice_table').DataTable().draw(false);
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
                                $('.invoice_table').DataTable().draw(false);
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
                        action: function() {
                            $('.invoice_table').DataTable().draw(false);
                        }
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
