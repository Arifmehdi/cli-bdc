@extends('backend.admin.layouts.master')

@section('content')
<div class="row">
    {{--   add terms condition modal--}}
    <div class="modal fade" id="termsConditionAddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Terms Condition</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.terms-condition.add')}}" method="post" id="termsConditionAdd" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="title">Title <span style="color: red; font-weight:bold">*</span></label>
                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Your Title">
                            <span class="text-danger error-message" id="title-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span style="color: red; font-weight:bold">*</span></label>
                            <textarea name="description" id="description" rows="12" class="rounded form-control" placeholder=""></textarea>
                            <span class="text-danger error-message" id="description-error"></span>
                        </div>
                        <button type="submit"  style="float:right; margin-bottom:8px; padding-left:25px; padding-right:25px; font-size:15px"  class="btn btn-success" id="terms_button">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--  End add user modal--}}


    {{-- news edit modal start --}}

    <!-- Modal -->
    <div class="modal fade" id="faqEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Terms Condition Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <form id="faqEditFrom" action="{{ route('admin.terms-condition.update') }}"
                     method="POST"
                     class="mt-2 form-horizontal sales-form" enctype="multipart/form-data">
                     @csrf
                        <input type="hidden" name="faq_id" id="faq_id">
                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Title  <span style="color:red;font-weight:bold">*</span></label>
                                <input id="up_title" name="up_title" class="rounded form-control" placeholder="car title"
                                    type="text">
                                    <div class="text-danger error-up_title"></div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Description </small>  <span style="color:red;font-weight:bold">*</span></label>
                                <textarea name="up_description" id="faq_edit"  rows="12" class="rounded form-control up_description"
                                    placeholder=""></textarea>
                                <div class="text-danger error-up_description"></div>
                            </div>
                        </div>
                        <button type="submit" class="float-right mt-4 btn btn-primary" id="edit_button">Update</button>
                    </form>
                </div>

            </div>
        </div>
    </div>



    <div class="col-md-12">
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Terms Condition List</h3>
                    <a href="#" class="float-right btn btn-primary btn-sm" data-toggle="modal" data-target="#termsConditionAddModal"> <i class="fas fa-plus-circle"></i> Add Terms & Condition</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <table  class="table table-bordered table-striped terms_condition_table">
                        <thead>
                        <tr>
                            <th>SL</th>
                            <th>Title</th>
                            <th>Description</th>
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
// description editor js start
let editorInstance;
ClassicEditor
    .create(document.querySelector('#description'))
    .then(editor => {
        editorInstance = editor;
    })
    .catch(error => {
        console.error(error);
    });

    // description editor js close


    // terms and condition add data

    $(document).ready(function(){
    $(function() {
    var table = $('.terms_condition_table').DataTable({

    dom: "lBfrtip",
    buttons: ["copy", "csv", "excel", "pdf", "print"],

    pageLength: 25,
    processing: true,
    serverSide: true,
    searchable: true,
    "ajax": {
        "url": "{{ route('admin.terms.condition') }}",
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
        data: 'title',
        name: 'title',
        sWidth: '27%'
    },
    {
        data: 'description',
        name: 'description',
        sWidth: '55%'
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


        $.ajaxSetup({
         headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
       });

    $(document).on('submit','#termsConditionAdd', function (e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);
    $('#terms_button').text('Loading...');
    var form = this;
    $.ajax({
        processData: false,
        contentType: false,
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: formData,
        success: function (res) {
            console.log(res);
            $('.error-message').html('');
            if (res.errors) {
                $.each(res.errors, function (key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
                $('#terms_button').text('Submit');
            }

            if (res.status === 'success') {
                $('#termsConditionAdd')[0].reset();
                editorInstance.setData(''); // Clear the CKEditor content
                $('.terms_condition_table').DataTable().draw(false);
                toastr.success(res.message);
                // Reset CKEditor content
                if (editorInstance) {
                editorInstance.setData('');
                }
                form.reset();
                $('#termsConditionAddModal').modal('hide');
                $('#terms_button').text('Submit');

            }
        },
        error: function (xhr) {
            var errors = xhr.responseJSON.errors;
            $.each(errors, function (key, value) {
                $('#' + key + '-error').text(value[0]);
            });
            $('#terms_button').text('Submit');
        }
    });
});


// terms condition delete
$(document).on('click', "#termsCondition_delete", function(e){
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

                }
            },
            confirm: {
                text: 'Yes',
                btnClass: 'btn-primary',
                action: function () {

                    $.ajax({
                        url: "{{ route('admin.terms-condition.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.terms_condition_table').DataTable().draw(false);
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


        let editor;
        $(document).on('click', '.editTermsCondition', function(e) {
                e.preventDefault();
                let id = $(this).data('id');
                let title = $(this).data('title');
                let description = $(this).data('description');
                var tempElement = document.createElement('div');
                tempElement.innerHTML = description;
                var textOnly = tempElement.textContent || tempElement.innerText || '';
                $('#faq_id').val(id);
                $('#up_title').val(title);
                $('#faq_edit').text(textOnly);
                $('#faqEdit').modal('show');
                if (editor) {
                    editor.destroy();
                }
                ClassicEditor.create(document.querySelector('#faq_edit'))
                    .then(newEditor => {
                        editor = newEditor;
                        editor.setData(textOnly);
                    })
                    .catch(error => {
                        console.error(error);
                    });
            });
            // terms condition edit code
            $(document).on('submit', '#faqEditFrom', function(e) {
                e.preventDefault();

                var formData = new FormData($(this)[0]);
                    $('#edit_button').text('Loading...');
                $.ajax({
                    url: $(this).attr('action'),
                    type: $(this).attr('method'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        console.log(res);
                        toastr.success(res.message);
                        $('#faqEdit').modal('hide');
                        $('.terms_condition_table').DataTable().draw(false);
                        $('#edit_button').text('Update');
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

    })


</script>

@endpush
