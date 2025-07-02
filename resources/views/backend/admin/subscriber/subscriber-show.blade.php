@extends('backend.admin.layouts.master')

@section('content')
<div class="row">









    <div class="col-md-12">
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Subscriber List</h3>

                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <table  class="table table-bordered table-striped subs_table">
                        <thead>
                        <tr>
                            <th>SL</th>

                            <th>Email</th>

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
        var table = $('.subs_table').DataTable({
            dom: "lBfrtip",
            buttons: ["copy", "csv", "excel", "pdf", "print"],
            pageLength: 25,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.subscriber.show') }}",
                dataType: "json",
                dataSrc: "data",
                data: function(data) {
                    // You can customize data sent to server here if needed
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
                    data: 'email',
                    name: 'email',
                    sWidth: '27%'
                },
                {
                    data: 'status',
                    name: 'status',
                    sWidth: '10%'
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
            ]
        });

        // Append export buttons container
        table.buttons().container().appendTo('#exportButtonsContainer');

        // Check all checkboxes functionality
        $(document.body).on('click', '#is_check_all', function(event) {
            var checked = event.target.checked;
            $('.check1').prop('checked', checked);
        });

        // Update "Check all" checkbox state based on individual checkboxes
        $(document.body).on('click', '.check1', function(event) {
            var allItem = $('.check1');
            var allChecked = allItem.toArray().every(function(el) {
                return el.checked === allItem[0].checked;
            });
            $('#is_check_all').prop('checked', allChecked);
        });

        // Submit filter form by select input changing
        $(document).on('change', '.submitable', function() {
            table.ajax.reload();
        });

        // Set CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // links delete
$(document).on('click', "#sub_delete", function(e){
    e.preventDefault();
    let id = $(this).data('id');


    $.confirm({
        title: 'Delete Confirmation',
        content: 'Are you sure?',
        buttons: {
            cancel: {
                text: 'No',
                btnClass: 'btn-primary',
                action: function () {
                    // Do nothing on cancel
                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-danger',
                action: function () {
                    // Use the 'id' from the outer scope
                    $.ajax({
                        url: "{{ route('admin.subscriber.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.subs_table').DataTable().draw(false);
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

$(document).on('change', '.action-select', function(e){
                e.preventDefault();
                let id = $(this).data('id');
                $.ajax({
                    url:"{{route('admin.sub.status.change')}}",
                    method:"post",
                    data:{id:id},
                    success:function(res){
                        console.log(res)
                        toastr.success(res.message);
                        $('.subs_table').DataTable().draw(false);
                    }
                })
            });

    });
</script>


@endpush
