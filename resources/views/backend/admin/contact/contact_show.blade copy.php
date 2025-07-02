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

                    <form id="bulk_action_form" action="{{ route('admin.lead.bulk-action') }}" method="POST">
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

                }
            },

            drawCallback: function(settings) {

                $('#is_check_all').prop('checked', false);

            },

            columns: [
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



});
    // user yajra code close

</script>
@endpush

