@extends('backend.admin.layouts.master')

@section('content')
    <div class="row">
        
        



        <div class="col-md-12 pt-5 m-auto rounded">
            <div class="card">
                <div class="card-header">
                    <h6>Request Inventories</h6>
                    {{-- <a href="" class="float-right btn btn-primary btn-sm" data-toggle="modal"
                        data-target="#bannerAdd"> <i class="fas fa-plus-circle"></i> Add Banner</a> --}}
                </div>
                <div class="card-block">
                    @if (session()->has('message'))
                        <h3 class="text-success">{{ session()->get('message') }}</h3>
                    @endif
                    <div class="table-responsive dt-responsive">
                        <table id="dom-jqry" class="table table-striped table-bordered req_table nowrap"
                            style="width: 100%">
                            <thead>
                                <tr>
                                    <th style="width:5%">S.L</th>
                                    <th style="">Image</th>
                                    <th style="">Vin</th>
                                    <th style="">Make</th>
                                    <th style="">Model</th>
                                    <th style="">Year</th>
                                    <th style="">Price</th>
                                    <th style="">Mileage</th>
                                    <th style="">fuel</th>
                                    <th style="">status</th>
                                    <th style="">Action</th>
                                </tr>
                            </thead>
                            <tbody>


                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $.ajaxSetup({
            beforeSend: function(xhr, type) {
                if (!type.crossDomain) {
                    xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                }
            },
        });



        $(document).ready(function() {
            $(function() {
                var table = $('.req_table').DataTable({
                    dom: "lBfrtip",
                    buttons: ["copy", "csv", "excel", "pdf", "print"],
                    pageLength: 25,
                    processing: true,
                    serverSide: true,
                    searchable: true,
                    "ajax": {
                        "url": "{{ route('admin.req.inventory') }}",
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
                            data: 'Image',
                            name: 'Image',
                            sWidth: '10%'
                        },
                        {
                            data: 'vin',
                            name: 'vin',
                            
                        },
                        
                        {
                            data: 'make',
                            name: 'make',
                            
                        },
                        {
                            data: 'model',
                            name: 'model',
                            
                        },
                        {
                            data: 'year',
                            name: 'year',
                            
                        },
                        {
                            data: 'price',
                            name: 'price',
                            
                        },
                        {
                            data: 'miles',
                            name: 'miles',
                            
                        },
                        {
                            data: 'fuel',
                            name: 'fuel',
                            
                        },

                        {
                            data: 'status',
                            name: 'status',
                            sWidth: '7%'
                        },

                        {
                            data: 'action',
                            name: 'action',
                            sWidth: "12%",
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

                $(document).on('change', '#banner_activeInactive', function(e) {
                e.preventDefault();
                
                let id = $(this).data('id');
                let status = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: "{{ route('admin.req.change.status') }}",
                    data: {
                        id: id,
                        status: status
                    },
                    success: function(res) {
                        console.log(res);
                        if (res.status == 'success') {
                            toastr.success(res.message);
                            $('.req_table').DataTable().draw(false);
                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }

                });


            });


            });

            
        });
    </script>
@endpush
