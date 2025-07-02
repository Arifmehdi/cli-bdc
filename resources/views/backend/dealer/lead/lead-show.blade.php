@extends('backend.admin.layouts.master')

@section('content')
    <style>
        #trashed_item {
            /* Add your styles for the trashed item text here */
            font-weight: bold;
            margin-right: 7px;
        }

        .dataTables_filter input[type="search"] {

            margin-top: 15px !important;

        }
    </style>




    <!-- Large modal -->

    <div class="modal fade bd-example-modal-lg" id="purchase_modal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="col-md-12 p-4">
                    <div class="row">
                        <form action="{{ route('dealer.add.tocart')}}" method="POST" id="submitForm">
                        @csrf
                        <input type="hidden" name="membership_id" id="membership_id">
                        <input type="hidden" name="user_click" id="user_click">
                        <input type="hidden" name="lead_id[]" id="lead_id">
                        </form>
                        @foreach ($memberships as $membership)
                        @if ($membership->type === 'lead' || $membership->type === 'listing')
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title text-bold">
                                            {{ $membership->type === 'lead' ? 'Purchases Lead' : 'Upgrade Listing' }}
                                        </h5>
                                        <br />
                                        <br />
                                        <h4>${{ $membership->membership_price }} /<sub>month</sub></h4>
                                        <br />
                                        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                                    </div>
                                    <div class="card-footer">
                                        <button
                                            class="btn btn-primary"
                                            style="margin-left: 25%"
                                            id="{{ $membership->type }}"
                                            data-membership_id="{{ $membership->id }}"
                                        >
                                            Buy Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                        {{-- <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-bold">Purchases Lead</h5>
                                    <br />
                                    <br />
                                    <h4>$4.99 /<sub>month</sub></h4>

                                    <br />
                                    <p class="card-text">With supporting text below as a natural lead-in to additional
                                        content.</p>

                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary " style="margin-left: 25%" id="lead">Buy Now</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-bold">Upgrade Listing</h5>
                                    <br />
                                    <br />
                                    <h4>$4.99 /<sub>month</sub></h4>

                                    <br />
                                    <p class="card-text">With supporting text below as a natural lead-in to additional
                                        content.</p>
                                </div>
                                <div class="card-footer">
                                    <button class="btn btn-primary " style="margin-left: 25%" id="listing">Buy Now</button>
                                </div>
                            </div>
                        </div> --}}

                        <div class="col-md-12">
                            <h5 class="text-bold text-center">Upgrade Membership</h5>
                            <hr/>
                            @php

                            @endphp
                            <div class="row">
                                @foreach ($memberships as $membership)
                                @if (!in_array($membership->type, ['lead', 'listing']))
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title text-bold">{{ $membership->name }}</h5>
                                            <br />
                                            <br />
                                            <h4>${{ $membership->membership_price }} /<sub>month</sub></h4>
                                            <br />
                                            <p class="card-text">With supporting text below as a natural lead-in to additional
                                                content.</p>
                                        </div>
                                        <div class="card-footer">
                                            <button
                                                class="btn btn-primary membership"
                                                style="margin-left: 25%"
                                                data-id="{{ $membership->id }}"
                                            >
                                                Buy Now
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                                {{-- <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title text-bold">{{ $membership->name }}</h5>
                                            <br />
                                            <br />
                                            <h4>${{ $membership->membership_price }} /<sub>month</sub></h4>
                                            <br />
                                            <p class="card-text">With supporting text below as a natural lead-in to additional
                                                content.</p>
                                        </div>
                                        <div class="card-footer">
                                            <button
                                            class="btn btn-primary membership"
                                            style="margin-left: 25%"
                                            data-id="{{ $membership->id }}"
                                        >
                                            Buy Now
                                        </button>
                                        </div>
                                    </div>
                                </div> --}}
                                @endforeach


                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="col-md-12">
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <form id="bulk_action_form" action="{{ route('dealer.lead.bulk-action') }}" method="POST">

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
                            <label for="dealerState">Dealer City : </label>
                            <select class="form-control mb-3 submitable" id="dealerCity">
                                <option value="">Choose City</option>
                                @foreach ($inventory_dealer_city as $cityData => $index)
                                    <option value="{{ $cityData }}">{{ $cityData }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="dealerState">Dealer State : </label>
                            <select class="form-control" id="dealerState">
                                <option value="">Choose City</option>
                                @foreach ($inventory_dealer_state as $stateData => $index)
                                    <option value="{{ $stateData }}">{{ $stateData }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <label for="date">Inventory date : </label>
                            <input class="form-control" type="date" id="date" placeholder="Date" />
                        </div>
                    </div>




                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <table class="table table-bordered table-striped dealer_lead_table">
                        <thead>
                            <tr>
                                <th class="text-start">
                                    <div>
                                        <input type="checkbox" id="is_check_all">
                                    </div>
                                </th>
                                <th class="text-start">{{ __('SL No.') }}</th>
                                <th>Title</th>
                                <th>Stock</th>
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
                <!-- /.card-body -->
            </div>
            <!-- /.card-body -->

            <!-- /.card -->

        </section>

    </div>
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

                var table = $('.dealer_lead_table').DataTable({

                    dom: "lBfrtip",
                    buttons: ["copy", "csv", "excel", "pdf", "print"],

                    pageLength: 25,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    "ajax": {
                        "url": "{{ route('dealer.lead.show') }}",
                        "datatype": "json",
                        "dataSrc": "data",
                        "data": function(data) {
                            data.make_data = $('#makeData').val();
                            data.dealer_name = $('#dealerName').val();
                            data.dealerCity_data = $('#dealerCity').val();
                            data.dealer_state = $('#dealerState').val();
                            data.inventory_date = $('#date').val();
                            data.showTrashed = $('#trashed_item').attr('showtrash');

                            // data.make_data = $('#makeData').val();
                            // data.dealer_date = $('#dealerName').val();
                            // data.dealerCity_data = $('#dealerCity').val();
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
                                                <option value="invoice" id="invoice">Go To Invoice</option>
                                                <option value="move_to_trash" id="move_to_trash">Move To Trash</option>
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
                            data: 'stock',
                            name: 'stock',
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
                 $('#bulk_action_form').on('submit', function(e) {
                    e.preventDefault();
                    var url = $(this).attr('action');
                    var request = $(this).serialize();

                    // console.log(request);
                    // return;
                    var actionField = $('#bulk_action_field').val();

                    if(actionField == 'invoice')
                    {
                         var selectedLeadIds = [];
                        $('#purchase_modal').modal('show');
                        $('input[name="lead_id[]"]:checked').each(function() {
                            selectedLeadIds.push($(this).val());
                        });
                        $('#lead_id').val(selectedLeadIds);
                        return;


                    }
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: request,
                        success: function(data) {
                            if(data.status === 'success'){
                                toastr.options.timeOut = 500;
                                toastr.success(data.message);

                            }

                            $('.dealer_lead_table').DataTable().draw(false);

                        },
                        error: function(error) {

                            toastr.error(error.responseJSON.message);
                        }
                    });
                });


                $(document.body).on('click', '#is_check_all', function(event) {
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



            $(document).on('click', '#trashed_item', function(e) {
                e.preventDefault();
                $(this).attr("showtrash", true);
                $('.check1').prop('checked', false)
                $(this).addClass("font-weight-bold");
                $('.dealer_lead_table').DataTable().draw(false);
                $('#is_check_all').prop('checked', false);
                $('#all_item').removeClass("font-weight-bold");
                $("#delete_option").css('display', 'block');
                $("#restore_option").css('display', 'block');
                $("#move_to_trash").css('display', 'none');
                $("#invoice").css('display', 'none');
            });


            $(document).on('click', '#all_item', function(e) {
                e.preventDefault();
                trashed_item = $('#trashed_item');
                $('#is_check_all').prop('checked', false);
                $('.check1').prop('checked', false);
                trashed_item.attr("showtrash", false);
                $(this).addClass("font-weight-bold");
                $('.dealer_lead_table').DataTable().draw(false);
                $('#trashed_item').removeClass("font-weight-bold")
                $("#delete_option").css('display', 'none');
                $("#restore_option").css('display', 'none');
                $("#move_to_trash").css('display', 'block');
                $("#invoice").css('display', 'block');
            });

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
                                            $('.dealer_lead_table').DataTable().draw(
                                            false);
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
                                    $('.dealer_lead_table').DataTable().draw(false);
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
                        btnClass: 'btn-primary',
                        'action': function() {
                            // $('#delete_form').submit();
                            $.ajax({
                                url: url,
                                type: 'DELETE',
                                success: function(data) {
                                    toastr.success(data);
                                    $('.loading_button').hide();
                                    $('.dealer_lead_table').DataTable().draw(false);
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



        });



        // Purchase related code
        $(document).on('click', '.purchase', function(e) {
            e.preventDefault()

            var id = $(this).data('row_id');
            $('#lead_id').val(id);
            $('#purchase_modal').modal('show');


            // $.ajax({
            //     url: url,
            //    type: 'PUT',
            //    data: {id:id, model_name:model,makeid:makeid, status:statusValue},
            //    success:function(res){
            //         console.log('res');
            //         toastr.success(res.success);
            //         addform();
            //         $('.model-table').DataTable().draw(false);
            //    },
            //    error:function(error){
            //     toastr.error(error.responseJSON.message);
            //    }

            // });
        });






        $('.membership').on('click',function(){
            $('#user_click').val('Membership');
            var membershipId = $(this).data('id');
            $('#membership_id').val(membershipId);
            $('#submitForm').submit();
        });

        $('#listing').on('click',function(){
           $('#user_click').val('Listing');
           var membershipId = $(this).data('membership_id');
           $('#membership_id').val(membershipId);
           $('#submitForm').submit();
        });

        $('#lead').on('click',function(){
            $('#user_click').val('Lead');
            var membershipId = $(this).data('membership_id');
            $('#membership_id').val(membershipId);
            $('#submitForm').submit();
        });
    </script>
@endpush
