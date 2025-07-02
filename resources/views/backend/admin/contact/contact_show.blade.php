@extends('backend.admin.layouts.master')
@section('content')
<div class="row">
{{--   add user modal--}}
    <div class="modal fade" id="single_contact_show" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Contact View</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 id="contact-name">User Name: </h5>
                    <h5 id="contact-email"></h5>
                    <p id="contact-message"></p>
                   </div>
            </div>
        </div>
    </div>

    {{--  End add user modal--}}
    <div class="col-md-12 col-sm-12">
        <section class="content">
            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Contact</h3>
                    <form id="bulk_action_form" action="{{ route('admin.contact.delete') }}" method="POST">
                        @csrf
                        <div class="card-header">
                        </div>
                           <!-- /.card-header -->
                        <div class="card-body">
                            <table  class="table table-bordered table-striped contact_table">
                                <thead>
                                <tr>
                                    <th class="text-start">
                                        <div>
                                            <input type="checkbox" id="is_check_all">
                                        </div>
                                    </th>
                                    <th class="text-start">{{ __('SL') }}</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                <!-- /.card-body -->
                    </form>
                </div>
            </div>
                <!-- /.card-body -->
            <!-- /.card -->
        </section>

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

// user yajra code start
$(document).ready(function() {
    $(function() {
        var allRow = '';
        var trashedRow = '';
        var table = $('.contact_table').DataTable({
            dom: "lBfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            pageLength: 25,
            processing: true,
            serverSide: true,
            searchable: true,
            "ajax": {
                "url": "{{ route('admin.contact.show') }}",
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
                data: 'name',
                name: 'name',
            },
            {
                data: 'email',
                name: 'email',
            },
            {
                data: 'message',
                name: 'message',
                width: "30%",
            },
            {
                    data: 'action',
                    name: 'action',
                    width: "10%",
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


    // view contact
    $(document).on('click', ".view-contact", function(e){
    e.preventDefault();
    let id = $(this).data('id');
    $.ajax({
        url: "{{ route('admin.single.contact.view') }}",
        method: 'get',
        data: { id: id },
        success: function(res){
            $('#single_contact_show').modal('show');


            $("#contact-name").html(res.singleContact.name ? "User Name : " + res.singleContact.name : "User Name : Not Available"  );
            $("#contact-email").html(res.singleContact.email ? "User Email : " + res.singleContact.email : "User Email : Not Available"  );
            $("#contact-message").html(res.singleContact.message ? "User Message : " + res.singleContact.message : "User message : Not Available"  );

        },
        error: function(err) {
            console.error(err);
        }
    });
});
    // delete contact
$(document).on('click', ".delete-contact", function(e){
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
                    // Do nothing on cancel
                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function () {
                    // Use the 'id' from the outer scope
                    $.ajax({
                        url: "{{ route('admin.single.contact.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.contact_table').DataTable().draw(false);
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

  //Bulk Action
  $('#bulk_action_form').on('submit', function(e) {
                    e.preventDefault();
                    var url = $(this).attr('action');
                    var request = $(this).serialize();
                    $('#filter_button').text('Loading...');
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: request,
                        success: function(data) {
                            if(data.status=='success')
                            {
                                toastr.success(data.message);
                                $('#filter_button').text('Apply');

                            }
                            if(data.status=='error')
                            {
                                toastr.error(data.message);
                                $('#filter_button').text('Apply');

                            }

                            $('.contact_table').DataTable().draw(false);
                            $('#filter_button').text('Apply');


                        },
                        error: function(error) {

                            toastr.error(error.responseJSON.message);
                            $('#filter_button').text('Apply');
                        }
                    });
                });


                $(document).on('click', '#trashed_item', function(e) {
                e.preventDefault();
                $(this).attr("showtrash", true);
                $('.check1').prop('checked', false)
                $(this).addClass("font-weight-bold");
                $('.contact_table').DataTable().draw(false);
                $('#is_check_all').prop('checked', false);
                $('#all_item').removeClass("font-weight-bold");
                $("#delete_option").css('display', 'block');
                $("#restore_option").css('display', 'block');
                $("#move_to_trash").css('display', 'none');
            });

            $(document).on('click', '#all_item', function(e) {
                e.preventDefault();
                trashed_item = $('#trashed_item');
                $('#is_check_all').prop('checked', false);
                $('.check1').prop('checked', false);
                trashed_item.attr("showtrash", false);
                $(this).addClass("font-weight-bold");
                $('.contact_table').DataTable().draw(false);
                $('#trashed_item').removeClass("font-weight-bold")
                $("#delete_option").css('display', 'none');
                $("#restore_option").css('display', 'none');
                $("#move_to_trash").css('display', 'block');
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
                                    $('.contact_table').DataTable().draw(false);
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
                                    $('.contact_table').DataTable().draw(false);
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
</script>
@endpush

