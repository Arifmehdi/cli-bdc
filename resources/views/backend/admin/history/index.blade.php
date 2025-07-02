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



{{--   view user history modal--}}
<div class="modal fade" id="user_history_view" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">History View</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">

                <h5>Type: <span id="type"> </span></h5>
                <h5>Title: <span id="title"> </span></h5>
                <h5>Links: <span id="links"> </span></h5>
                <h5>IP: <span id="ip_address"> </span></h5>
                <h5>Image:  <img src="" id="image" style="height:200px;width:200px"/></h5>





               </div>



        </div>
    </div>
</div>

{{--  End add user modal--}}

    <div class="col-md-12">
        <section class="content">

            <!-- Default box -->
            <div class="card">
            <form id="bulk_action_form" action="{{ route('admin.inventory.bulk-action') }}" method="POST">
            @csrf

                <!-- /.card-header -->
                <div class="card-body">

                    <table class="table table-bordered table-striped history_table">
                        <thead>
                            <tr>
                                <th class="text-start">
                                    <div>
                                        <input type="checkbox" id="is_check_all">
                                    </div>
                                </th>
                                <th class="text-start">{{ __('SL') }}</th>
                                    <th>Type</th>
                                    <th>Title</th>
                                    <th>User</th>
                                    <th>IP</th>
                                    <th>Action</th>
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

                var table = $('.history_table').DataTable({
                    dom: "lBfrtip",
                    buttons: ["copy", "csv", "excel", "pdf", "print"],

                    pageLength: 25,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    "ajax": {
                        "url": "{{ route('admin.user.track.history') }}",
                        "datatype": "json",
                        "dataSrc": "data",
                        "data": function(data) {
                            // data.make_data = $('#makeData').val();
                            // data.dealer_date = $('#dealerName').val();
                            // data.dealerCity_data = $('#dealerCity').val();

                            // //send types of request for colums
                            // data.showTrashed = $('#trashed_item').attr('showtrash');
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
                            name: 'DT_RowIndex',
                            data: 'DT_RowIndex',
                            sWidth: '3%'
                        },
                        {
                            data: 'type',
                            name: 'type',
                            },
                            {
                                data: 'title',
                                name: 'title',
                            },
                            {
                                data: 'user_id',
                                name: 'user_id',
                            },
                            {
                                data: 'ip_address',
                                name: 'ip_address',
                            },

                        {
                            data: 'action',
                            name: 'action',
                            width: "15%",
                            orderable: false
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
                            toastr.success(data);
                            table.ajax.reload();
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
        })

            // restore method
            $(document).on('click', '.restore', function(e) {
            e.preventDefault();
            var url = $(this).attr('href');

            $.confirm({
                'title': 'Restore Confirmation',
                'message': 'Are you sure?',
                'buttons': {
                    'Yes': {
                        btnClass: 'btn-danger',
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
                    'No': {
                        'btnClass': 'btn-primary',
                        'action': function() {}
                    }
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
                    'Yes': {
                        'btnClass': 'yes btn-danger',
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
                    'No': {
                        'class': 'no btn-primary',
                        'action': function() {}
                    }
                }
            });
        });


            // delete history
            $(document).on('click', ".delete_history", function(e) {
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
                                    url: "{{ route('admin.user.track.history.delete') }}",
                                    type: 'post',
                                    data: {
                                        id: id
                                    },
                                    success: function(response) {
                                        if (response.status == "success") {
                                            toastr.success(response.message);
                                            $('.history_table').DataTable().draw(false);
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



            $(document).on('click', ".view_history", function(e) {
                e.preventDefault();
                var type = $(this).data('type');
                var title = $(this).data('title');
                var links = $(this).data('links');
                var image = $(this).data('image');
                var ip_address = $(this).data('ip_address');

                console.log(links);

                $('#type').text(type);
                $('#title').text(title);
                $('#links').text(links);
                $('#ip_address').text(ip_address);
                var imageUrl = "{{ asset('frontend/') }}"+'/'+ image;
                $('#image').attr('src', imageUrl);

                $('#user_history_view').modal('show');




            });
        });



    </script>

@endpush
