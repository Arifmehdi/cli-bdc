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
                        <button type="submit" class="btn btn-info mt-2 float-right send_button">Send Mail</button>
                    </form>

                </div>

            </div>
        </div>
    </div>
    {{-- send lead email modal end --}}
    {{-- send ADF lead email modal start --}}
    <!-- Modal -->
    <div class="modal fade" id="sendAdfLeadMail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Send ADF Mail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="adf_mail_send_form" action="{{ route('admin.dealer.adf.email.send') }}" method="post">
                        @csrf
                        <input type="hidden" name="send_adf_id" id="send_adf_id">
                        <input class="form-control" type="text" name="adf_mail" id="adf_mail"
                            placeholder="Enter ADF mail">
                        <button type="submit" class="btn btn-info mt-2 float-right send_adf_button">Send ADF Mail</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- send ADF lead email modal end --}}

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
                                    class="btn messageSendButton">Submit</button>
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


            <div class="card">
                <div class="card-header ">
                    <span class="mb-0 float-left">Leads</span>
                    <button type="button" class="btn btn-primary btn-sm float-right" data-toggle="modal" data-target="#staticBackdrop">
                        <i class="fas fa-plus-circle"></i> Add Lead
                    </button>
                </div>
            </div>


              <!-- Add Lead Modal -->
              <div class="modal fade" id="staticBackdrop" data-backdrop="static"
              data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                      <div class="modal-header">
                          <h5 class="modal-title" id="staticBackdropLabel">Add a New Lead</h5>
                          <button type="button" class="close" data-dismiss="modal"
                              aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                          </button>
                      </div>
                      <div class="card-body h-100">

                          <form action="{{route('admin.lead.store')}}" method="POST" id="Lead_submit">
                              @csrf
                              <div class="container">
                                  <h6 class="text-left">Add a customer to this lead : </h6>
                                  <div class="row">
                                      <div class="col-md-5 mb-3">
                                          <select name="customer_id" id="" class="form-control ">
                                              <option value="">Select a Customer</option>
                                              @foreach ($leads as $lead)
                                              <option value="{{ $lead->customer->id ?? '' }}">
                                              {{$lead->customer->name ?? ''}}</option>
                                              @endforeach

                                          </select>

                                      </div>
                                      <div class="col-md-7 text-left mb-3">
                                          <span style="margin-right:23px">or</span>
                                          <label for="create_new_customer"
                                              class="btn btn-primary"><i
                                                  class="fas fa-user-plus"></i>Create a
                                              Customer</label>
                                          <input type="checkbox" id="create_new_customer"
                                              style="display: none">
                                      </div>
                                  </div>
                                  <div class="row create_hidden_button" id="create_hidden_button"
                                      style="display: none">
                                      <div class="col-md-12">
                                          <div class="form-group ">

                                              <input placeholder="First Name*"
                                                  class="form-control fname" type="text"
                                                  name="first_name"
                                                  value="{{ old('first_name') }}">
                                              <span class="invalid-feedback1 text-danger"
                                                  role="alert"></span>

                                          </div>
                                      </div>
                                      <div class="col-md-12">
                                          <div class="form-group mb-3">

                                              <input placeholder="Last Name*"
                                                  class="form-control lname" type="text"
                                                  name="last_name" value="{{ old('last_name') }}">

                                              <span class="invalid-feedback2 text-danger"
                                                  role="alert"></span>

                                          </div>
                                      </div>
                                      <div class="col-md-12">
                                          <div class="form-group mb-3">

                                              <input placeholder="E-mail*"
                                                  class="form-control email" type="text"
                                                  name="email" value="{{ old('email') }}">

                                              <span class="invalid-feedback3 text-danger"
                                                  role="alert"></span>

                                          </div>
                                      </div>
                                      <div class="col-md-12">
                                          <div class="form-group mb-3">

                                              <input class="form-control phone telephoneInput"
                                                  type="text" name="phone"
                                                  value="{{ old('phone') }}">

                                              <span class="invalid-feedback4 text-danger"
                                                  role="alert"></span>

                                          </div>
                                      </div>
                                      <div class="col-md-12">
                                          <div class="form-group mb-3">
                                              <select name="phone_type" class="form-control"
                                                  id="">
                                                  <option value="">Phone Type</option>
                                                  <option value="Cell Phone">Cell Phone</option>
                                                  <option value="Work Phone">Work Phone</option>
                                                  <option value="Home Phone">Home Phone</option>
                                              </select>
                                              <span class="invalid-feedback5 text-danger"
                                                  role="alert"></span>
                                          </div>
                                      </div>
                                      <div class="col-md-12">
                                          <div class="form-group mb-3">
                                              <select name="contact_type" class="form-control"
                                                  id="">
                                                  <option value="">Contact Type</option>
                                                  <option value="Retail">Retail</option>
                                                  <option value="WholeSale">WholeSale</option>
                                              </select>
                                              <span class="invalid-feedback6 text-danger"
                                                  role="alert"></span>
                                          </div>
                                      </div>
                                      <div class="col-md-12">
                                          <div class="form-group mb-3">
                                              <select name="salespersion" class="form-control"
                                                  id="">
                                                  <option value="">SalesPerson</option>
                                                  <option value="Unassigned">Unassigned</option>
                                              </select>
                                              <span class="invalid-feedback7 text-danger"
                                                  role="alert"></span>
                                          </div>
                                      </div>
                                  </div>
                                  <div class="row">
                                      <h6 style="margin-left:10px " class="mt-4 mb-3">Add a
                                          Vehicle to this lead: (optional)</h6>
                                      <div class="col-md-6 text-left">
                                          <div class="form-group mb-3 selected_car">

                                              <span style="font-size: 10px">No Vechile
                                                  chosen</span>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <div class="form-group mb-3">
                                              <a href="javasceipt:void(0)" class="btn btn-primary choose_vechile"
                                                  id="choose_vechile"><i
                                                      class="fas fa-car"></i> Choose a
                                                  Vehicle</a>
                                          </div>
                                      </div>
                                  </div>

                                  <div class="row">

                                      <div class="col-md-6 ">
                                          <div class="form-group">
                                              <select name="lead_type" id="" class="form-control">
                                                  <option value="">Lead Type</option>
                                                  <option value="Walk-In">Walk-In</option>
                                                  <option value="E-mail">E-mail</option>
                                              </select>
                                              <span class="invalid-feedback8 text-danger"
                                                  role="alert"></span>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <select name="source" id="" class="form-control">
                                              <option value="">Lead Source</option>
                                              <option value="Other Customer">Other Customer
                                              </option>
                                              <option value="Other">Other</option>
                                              <option value="Migrated">Migrated</option>
                                          </select>
                                          <span class="invalid-feedback9 text-danger"
                                              role="alert"></span>
                                      </div>
                                      <div class="col-md-12">
                                          <div class="form-group">
                                              <textarea cols="50" rows="5" placeholder="note"
                                                  name="note"></textarea>

                                          </div>
                                          <button type="submit" class="btn btn-primary">Save
                                              Lead</button>
                                      </div>
                                  </div>

                              </div>

                          </form>
                      </div>

                  </div>
              </div>
          </div>



            {{-- choose vechile modal start --}}
            <div class="modal fade" id="chose_vechile_modal" data-bs-backdrop="static"
            data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header " style="background-color: #103a6a">
                        <span class="modal-title text-white" id="staticBackdropLabel">Select a
                            Vehicle</span>
                        <input type="search" name="search" class="search_query"
                            placeholder="Search Any Vehicle"
                            style="margin-left: 22%;border: none; padding-right: 11px;" />
                        <button type="button" class="close text-white" data-bs-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="card-body" style="height: 50vh; overflow-y: auto;">
                        <div class="container" id="carShow">
                            {{-- @foreach ($cars as $car)
                            @php

                            $image_obj = $car->local_img_url;
                            $image_splice = explode(',', $image_obj);
                            $image = trim(str_replace(['[', "'"], '', $image_splice[0]));
                            @endphp
                            <div class="row">
                                <div class="col-md-2 mb-3 p-0">
                                    <img src="{{ asset('frontend/') }}/{{ $image }}" alt="" style="width: 100%">
                                </div>
                                <div class="col-md-8 mb-3 text-left">
                                    <span class="text-left"
                                        style="font-weight:bold">{{ $car->title ?? '' }} <br/> <span
                                            style="color: #bdbaba"> #
                                            {{ $car->stock }}</span></span><br />
                                    <span class="text-left" style="font-weight:bold;color:red">$
                                        {{ $car->price }}</span>
                                </div>
                                <div class="col-md-2 mb-3 p-0 text-right">
                                    <button type="button" class="btn text-white select_car"
                                        style="background-color: #103a6a"
                                        value="{{ $car->id }}">select</button>
                                </div>
                            </div>
                            @endforeach --}}

                        </div>
                    </div>

                </div>
            </div>
        </div>


            <!-- Default box -->
            <div class="card">
                <form id="bulk_action_form" action="{{ route('admin.lead.bulk-action') }}" method="POST">
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

                        <table class="table table-bordered table-striped lead_table">
                            <thead>
                                <tr>
                                    <th class="text-start">
                                        <div>
                                            <input type="checkbox" id="is_check_all">
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

@include('backend.admin.lead.js.lead_page_js')

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

                var table = $('.lead_table').DataTable({
                    dom: "lBfrtip",
                    buttons: ["copy", "csv", "excel", "pdf", "print"],
                    pageLength: 25,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    "ajax": {
                        "url": "{{ route('admin.lead.show') }}",
                        "datatype": "json",
                        "dataSrc": "data",
                        "data": function(data) {
                            data.make_data = $('#makeData').val();
                            data.dealer_name = $('#dealerName').val();
                            data.dealerCity_data = $('#dealerCity').val();
                            data.dealer_state = $('#dealerState').val();
                            data.inventory_date = $('#date').val();
                            data.showTrashed = $('#trashed_item').attr('showtrash');
                        }
                    },
                    "drawCallback": function(data) {

                        var api = new $.fn.dataTable.Api(data);

                        // Iterate through each row and add class based on 'status'
                        api.rows().every(function(index, element) {
                            var status = this.data().status;
                            if (status == '<p>Inactive</p>') {
                                $(this.node()).addClass('bg-info');
                            }
                        });

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
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: request,
                        success: function(data) {
                            if(data.status=='success')
                            {
                                toastr.options.timeOut = 500;
                                toastr.success(data.message);
                                updateCartData();
                            }
                            if(data.status=='error')
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

                $(document).on('change', '.submitable', function() {
                    table.ajax.reload();
                });
            });

            $(document).on('click', '#trashed_item', function(e) {
                e.preventDefault();
                $(this).attr("showtrash", true);
                $('.check1').prop('checked', false)
                $(this).addClass("font-weight-bold");
                $('.lead_table').DataTable().draw(false);
                $('#is_check_all').prop('checked', false);
                $('#all_item').removeClass("font-weight-bold");
                $("#delete_option").css('display', 'block');
                $("#restore_option").css('display', 'block');
                $("#move_to_trash").css('display', 'none');
                $("#invoice").css('display', 'none');
            })

            $(document).on('click', '#all_item', function(e) {
                e.preventDefault();
                trashed_item = $('#trashed_item');
                $('#is_check_all').prop('checked', false);
                $('.check1').prop('checked', false);
                trashed_item.attr("showtrash", false);
                $(this).addClass("font-weight-bold");
                $('.lead_table').DataTable().draw(false);
                $('#trashed_item').removeClass("font-weight-bold")
                $("#delete_option").css('display', 'none');
                $("#restore_option").css('display', 'none');
                $("#move_to_trash").css('display', 'block');
            })

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
                                            $('.lead_table').DataTable().draw(
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
                                    var formattedTime = new Intl.DateTimeFormat(
                                    'en-US', {
                                        day: 'numeric',
                                        month: 'short',
                                        hour: 'numeric',
                                        minute: 'numeric',
                                        hour12: true
                                    }).format(new Date(message.created_at));

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
                    Swal.fire({
                    icon: 'warning',
                    title: 'Error',
                    text: 'Must type a message',
                });

                } else {
                    var formData = new FormData($(this)[0]);
                    $('.messageSendButton').text('Loading...');
                    $.ajax({
                        processData: false,
                        contentType: false,
                        url: $(this).attr('action'),
                        type: $(this).attr('method'),
                        data: formData,
                        success: function(res) {
                            $('.messageSendButton').text('Submit');
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
                                        $('#message_all').append(
                                            '<div class="first-message" style="padding-bottom:10px !important"><p><span class="date-america" style="float:right;margin-top:-25px;color:white;margin-bottom:6px;">' +
                                            formattedTime +
                                            '</span></p><p class="sender" style="color:black;background-color:#B0E0E6;padding:10px; border-radius:3px; margin-left:300px">' +
                                                res.data.message + '</p></div>')
                                }
                                toastr.success(res.message);
                            }
                        },
                        error: function(xhr) {
                            $('.messageSendButton').text('Submit');
                            var errors = xhr.responseJSON.errors;
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
            });
            $(document).on('click', '.send-adf-mail', function() {
                var id = $(this).data("id");
                $('#send_adf_id').val(id);
                $('#sendAdfLeadMail').modal('show');
            });

            $(document).on('submit', '#mail_send_form', function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                $('.send_button').text('Loading...')
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // console.log(res);


                        if (res.status == 'success') {
                            toastr.success(res.message);
                            $('#sendLeadMail').modal('hide');
                            $('.send_button').text('Send Mail');
                            $('#mail_send_form')[0].reset(); // Reset the form
                        }else
                        {
                            $('.send_button').text('Send Mail');
                            var message = res.errors.mail;
                           toastr.error(message);

                        }
                    },

                });
            });
            $(document).on('submit', '#adf_mail_send_form', function(e) {
                e.preventDefault();
                var formData = new FormData($(this)[0]);
                $('.send_adf_button').text('Loading...')
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        // console.log(res);


                        if (res.status == 'success') {
                            toastr.success(res.message);
                            $('#sendAdfLeadMail').modal('hide');
                            $('.send_adf_button').text('Send ADF Mail');
                            $('#adf_mail_send_form')[0].reset(); // Reset the form
                        }else
                        {
                            $('.send_adf_button').text('Send ADF Mail');
                            var message = res.errors.mail;
                           toastr.error(message);

                        }
                    },

                });
            });


            $(document).on('click','.common_read',function(){
                var id = $(this).data("id");
                $.ajax({
                    url: "{{ route('admin.lead.seen') }}",
                    type: "get",
                    data: {
                        id: id
                    },
                    success: function(res) {
                        if(res.status == 'success')
                        {
                            $('.lead_table').find('tr').each(function(){
                            if ($(this).find('input[name="lead_id[]"]').val() == id) {
                                $(this).removeClass('bg-info');
                            }
                        });
                        }
                    }

                });
            })
        });
    </script>
@endpush
