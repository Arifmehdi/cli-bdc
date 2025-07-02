@extends('backend.admin.layouts.master')

@section('content')
<div class="row">
    {{-- create new modal start here --}}
    <div class="modal fade" id="newsAddModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Create Blog</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('admin.blog.add') }}" method="post" id="newsAdd"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="category_id">Category <span style="color: red; font-weight:bold">*</span></label>
                                <select name="category_id" id="category_id" class="form-control">
                                   <option value="">Choose Category</option> 
                                   <option value="1">Reseacrh</option> 
                                   <option value="2">Beyond Cars</option> 
                                </select>
                            <span class="text-danger error-message" id="category-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="sub_category_id">Sub Category <span style="color: red; font-weight:bold">*</span></label>
                                <select name="sub_category_id" id="sub_category_id" class="form-control">
                                   <option value="">Choose Sub Category</option> 
                                   <option value="1">Auto News</option> 
                                   <option value="2">Reviews</option> 
                                   <option value="3">Tools and Advice</option> 
                                   <option value="4">Car Buying Advice</option> 
                                   <option value="5">Car Tips</option> 
                                   <option value="9">News</option> 
                                   <option value="10">Innovation</option> 
                                   <option value="11">Opinion</option> 
                                   <option value="12">Financial</option> 
                                </select>
                            <span class="text-danger error-message" id="category-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="title">Title <span style="color: red; font-weight:bold">*</span></label>
                            <input type="text" class="form-control" name="title" id="title"
                                placeholder="Enter Your Title">
                            <span class="text-danger error-message" id="title-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="title">Sub Title <span style="color: red; font-weight:bold">*</span></label>
                            <input type="text" class="form-control" name="sub_title" id="sub_title"
                                placeholder="Enter Your Sub Title">
                            <span class="text-danger error-message" id="sub-title-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="description">Description <span
                                    style="color: red; font-weight:bold">*</span></label>
                            <textarea name="description" id="description" rows="12" class="rounded form-control"
                                placeholder=""></textarea>
                            <span class="text-danger error-message" id="description-error"></span>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>

                            </select>

                        </div>

                        <div class="mt-4 form-group">
                            <label for="image">Image</label>
                            <input type="file" name="image" id="image" class="">

                            <div id="imagePreviewContainer">
                                <img width="80px" height="50px" id="imagePreview" style="display: none; margin-top:5px">
                                <a id="removeImageButton" style="display: none;
                                        color:red; margin-top:5px; cursor:pointer">x</a>
                            </div>
                        </div>

                        <p class="mt-4">SEO Part</p>
                        <hr />

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="make">SEO Description</label>
                                    <textarea name="seo_description" id="seo_description" class="form-control"
                                        style="height: 37px;" cols="30" rows="10"></textarea>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="make">SEO Keyword</label>
                                    <input type="text" class="form-control" id="keyword">
                                    <div id="keywordTags"></div>

                                    <input type="hidden" name="keywords[]" id="hiddenKeyword">
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            style="float:right; margin-bottom:8px; padding-left:25px; padding-right:25px; font-size:15px"
                            class="btn btn-success" id="news_submit_button">Submit</button>
                    </form>
                </div>


            </div>
        </div>
    </div>
    {{-- create new modal end here --}}


    {{-- news edit modal start --}}

    <!-- Modal -->
    <div class="modal fade" id="newsEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Blog Edit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>

                </div>
                <div class="modal-body">
                    <form id="NewsEditFrom" action="{{ route('admin.news.update') }}" method="POST"
                        class="mt-2 form-horizontal sales-form" enctype="multipart/form-data">
                        @csrf


                        <input type="hidden" name="news_id" id="news_id">


                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Title <span
                                        style="color:red;font-weight:bold">*</span></label>
                                <input id="up_title" name="up_title" class="rounded form-control"
                                    placeholder="car title" type="text">
                                <div class="text-danger error-up_title"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Sub Title <span
                                        style="color:red;font-weight:bold">*</span></label>
                                <input id="up_sub_title" name="up_sub_title" class="rounded form-control"
                                    placeholder="car title" type="text">
                                <div class="text-danger error-up_sub_title"></div>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Car Description </small> <span
                                        style="color:red;font-weight:bold">*</span></label>
                                <textarea name="up_description" id="news_edit" rows="12"
                                    class="rounded form-control up_description" placeholder=""></textarea>
                                <div class="text-danger error-up_description"></div>
                            </div>
                        </div>
                        <div class="mb-3 row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <div class="form-group">
                                    <label for="description">Status</label>
                                    <select class="form-control" name="status" id="up_status">
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>

                                    </select>

                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
                                <label class="control-label">Car Image Upload</label>
                                <br />
                                <input name="up_img" type="file" id="up_img">
                                <img width="80px" height="50px" src="" alt="" class="imagePreview"
                                    style="margin-top:5px">
                                <div id="imagePreviewContainer">
                                    <img width="80px" height="50px" id="upimagePreview"
                                        style="display: none; margin-top:5px">
                                    <a id="UpremoveImageButtonnews" style="display: none;
                                    color:red; margin-top:5px; cursor:pointer">x</a>
                                </div>
                                <div class="text-danger error-up_img"></div>
                            </div>
                        </div>

                        <p class="mt-4">SEO Part</p>
                        <hr />

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="make">SEO Description</label>
                                    <textarea name="seo_description" id="seo_description"
                                        class="form-control seo_description" style="height: 37px;" cols="30"
                                        rows="10"></textarea>

                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="make">SEO Keyword</label>
                                    <input type="text" class="form-control keyword" id="up_keyword" placeholder="Use coma after the word">
                                    <div id="up_keywordTags"></div>
                                    <input type="hidden" name="up_keywords[]" id="up_hiddenKeyword">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="float-right mt-4 btn btn-primary"
                            id="update_button">Update</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- news edit modal close --}}


    {{-- News show modal --}}
    <div class="modal fade" id="newsShowModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">News View</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>





                <div class="modal-body">
                    <img width="100%" id="news-img" src="" alt="news-img" />


                    <h4 style="font-weight:bold; margin-top:10px" id="news-title"></h4>
                    <p id="news-des"></p>
                </div>


            </div>
        </div>
    </div>

    {{-- End add user modal --}}

    <div class="col-md-12">
        <section class="content">

            <!-- Default box -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Research / Auto News List</h3>
                    <a href="" class="float-right btn btn-primary btn-sm" data-toggle="modal"
                        data-target="#newsAddModal"> <i class="fas fa-plus-circle"></i> Add Blog</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                    <table class="table table-bordered table-striped news_table">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Image</th>
                                <th>Title</th>
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


    
// all necessary code start here
    // news add data

    $(document).ready(function() {


$(function() {

    var table = $('.news_table').DataTable({

        dom: "lBfrtip",
        buttons: ["copy", "csv", "excel", "pdf", "print"],

        pageLength: 25,
        processing: true,
        serverSide: true,
        searchable: true,
        "ajax": {
            "url": "{{ route('admin.research.news.index') }}",
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
                sWidth: '20%'
            },
            {
                data: 'title',
                name: 'title',
                sWidth: '42%'
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
$(document).on('submit', '#newsAdd', function(e) {
    e.preventDefault();
    var formData = new FormData($(this)[0]);
    $('#news_submit_button').text('Loading...');
    var errorFields = ['description', 'title'];
    errorFields.forEach(function(field) {
        $('#' + field + '-error').text('');
    });
    var form = this;

    $.ajax({
        processData: false,
        contentType: false,
        url: $(this).attr('action'),
        type: $(this).attr('method'),
        data: formData,
        success: function(res) {
            console.log(res);
            $('.error-message').html('');

            if (res.errors) {
                // Display validation errors dynamically
                $.each(res.errors, function(key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
                $('#news_submit_button').text('Submit');
            }

            if (res.status === 'success') {

                $('.news_table').DataTable().draw(false);


                // Reset CKEditor content
                if (editorInstance) {
                    editorInstance.setData('');
                }


                $('#imagePreviewContainer').html('');
                form.reset();
                $('#news_submit_button').text('Submit');
                toastr.success(res.message);
                $('#newsAddModal').modal('hide');
            }


        },

        error: function(xhr) {
            // Handle error response
            var errors = xhr.responseJSON.errors;

            // Display validation errors dynamically
            $.each(errors, function(key, value) {
                $('#' + key + '-error').text(value[0]);
            });
            $('#news_submit_button').text('Submit');
        }
    });
});


// news delete
$(document).on('click', "#news_delete", function(e) {
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
                        url: "{{ route('admin.single.news.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                $('.news_table').DataTable().draw(
                                    false);
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

// news show

$(document).on('click', ".single-news-show", function(e) {
    e.preventDefault();
    let id = $(this).data('id');



    $.ajax({
        url: "{{ route('admin.single.news.view') }}",
        method: 'get',
        data: {
            id: id
        },
        success: function(res) {

            console.log(res.data);
            $('#newsShowModal').modal('show');

            $('#news-img').attr('src',
                "{{ asset('frontend/assets/images/news') }}/" + res.data.img);
            $('#news-title').html(res.data.title);
            $('#news-des').html(res.data.description);




        },
        error: function(error) {
            console.error('Error in AJAX request:', error);
        }
    });
});

let editor;

$(document).on('click', '.editBtn', function(e) {
    e.preventDefault();
    let id = $(this).data('id');
    let title = $(this).data('title');
    let sub_title = $(this).data('sub_title');
    let description = $(this).data('description');
    let status = $(this).data('status');
    let seo_description = $(this).data('seo_description');
    let seo_keyword = $(this).data('seo_keyword');
    let image = $(this).data('image');

    $('.imagePreview').attr('src', "{{ asset('/frontend/assets/images/news') }}" + '/' + image);

    var tempElement = document.createElement('div');
    tempElement.innerHTML = description;
    var textOnlyDescription = tempElement.textContent || tempElement.innerText || '';

    $('#news_id').val(id);
    $('#up_title').val(title);
    $('#up_sub_title').val(sub_title);
    $('#up_status').val(status);
    $('#news_edit').text(textOnlyDescription);
    $('.seo_description').text(seo_description);
    $('.keyword').val(seo_keyword);
    $('.update_image').attr('src', image);
    $('#newsEdit').modal('show');
    if (editor) {
        editor.destroy();
    }

    ClassicEditor.create(document.querySelector('#news_edit'))
        .then(newEditor => {
            editor = newEditor;
            editor.setData(description);
        })
        .catch(error => {
            console.error(error);
        });
});



// news edit code
$(document).on('submit', '#NewsEditFrom', function(e) {
    e.preventDefault();

    var formData = new FormData($(this)[0]);
    $('#update_button').text('Loading...');

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
            $('#newsEdit').modal('hide');
            $('.news_table').DataTable().draw(false);
            $('#update_button').text('Update');
        },
        error: function(error) {
            console.log(error);
            $('#update_button').text('Update');
        }
    });
});


// status change
$(document).on('change', '.action-select', function(e) {
    e.preventDefault();
    let id = $(this).data('id');


    $.ajax({
        url: "{{ route('admin.news.status.change') }}",
        method: "post",
        data: {
            id: id
        },
        success: function(res) {
            console.log(res)
            toastr.success(res.message);
            $('.news_table').DataTable().draw(false);
        }
    })
});




// image show and remove
$(document).ready(function() {
    // When the file input changes
    $("#image").change(function() {
        readURL(this);
    });

    // Function to read the URL and display the image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                // Display the image preview
                $("#imagePreview").attr("src", e.target.result).show();

                // Show the remove button
                $("#removeImageButton").show();

            };

            // Read the file as a data URL
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Click event for the remove button
    $("#removeImageButton").click(function() {
        // Clear the file input and hide the image preview
        $("#image").val("");
        $("#imagePreview").attr("src", "").hide();
        // Hide the remove button
        $(this).hide();
    });
});

$(document).ready(function() {
    // When the file input changes
    $("#up_img").change(function() {
        readURL(this);
    });

    // Function to read the URL and display the image preview
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                // Display the image preview
                $("#upimagePreview").attr("src", e.target.result).show();

                // Show the remove button
                $("#UpremoveImageButtonnews").show();
                $(".imagePreview").hide();

            };

            // Read the file as a data URL
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Click event for the remove button
    $("#UpremoveImageButtonnews").click(function() {
        // Clear the file input and hide the image preview
        $("#up_img").val("");
        $("#upimagePreview").attr("src", "").hide();
        // Hide the remove button
        $(this).hide();
    });
});




});

// all necessary code end  here


    // description editor js start

    let editorInstance;

    ClassicEditor
        .create(document.querySelector('#description'), {
            ckfinder: {
                uploadUrl: "{{ route('admin.ckeditor.upload', ['_token' => csrf_token()]) }}"
            },
            toolbar: [
                'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'ckfinder', 'imageUpload', 'insertTable', 'mediaEmbed', 'undo', 'redo'
            ],
            mediaEmbed: {
                previewsInData: true
            }
        })
        .then(editor => {
            editorInstance = editor;
        })
        .catch(error => {
            console.error(error);
        });

    // description editor js close




    // add meta tag javascript

    document.getElementById("keyword").addEventListener("keyup", function(event) {
        if (event.key === "Enter" || event.key === ",") {
            event.preventDefault();
            var keywords = document.getElementById("keyword").value.trim().split(",");
            var keywordTags = document.getElementById("keywordTags");

            for (var i = 0; i < keywords.length; i++) {
                var keyword = keywords[i].trim();
                if (keyword !== "") {
                    var tagElement = document.createElement("span");
                    tagElement.textContent = keyword;
                    tagElement.className = "badge bg-primary me-2";

                    // Add a close button to each tag
                    var closeButton = document.createElement("button");
                    closeButton.innerHTML = "&times;"; // Close icon (X)
                    closeButton.className = "btn-close";
                    closeButton.setAttribute("aria-label", "Close");
                    closeButton.addEventListener("click", function() {
                        this.parentNode.remove(); // Remove the tag when the close button is clicked
                        updateHiddenKeywordInput(); // Update hidden input when removing a tag
                    });

                    tagElement.appendChild(closeButton);
                    keywordTags.appendChild(tagElement);
                }
            }
            updateHiddenKeywordInput(); // Update hidden input with all keywords
            document.getElementById("keyword").value = ""; // Clear input after adding tags
        }
    });

    function updateHiddenKeywordInput() {
        var tags = document.querySelectorAll("#keywordTags .badge");
        var keywordsArray = [];
        tags.forEach(function(tag) {
            keywordsArray.push(tag.textContent.slice(0, -1).trim()); // Remove the close button text (X)
        });
        document.getElementById("hiddenKeyword").value = keywordsArray.join(","); // Update hidden input
    }



    // edit meta tag javascript

    document.getElementById("up_keyword").addEventListener("keyup", function(event) {
        // alert('ok');
        if (event.key === "Enter" || event.key === ",") {
            event.preventDefault();
            var keywords = document.getElementById("up_keyword").value.trim().split(",");
            var keywordTags = document.getElementById("up_keywordTags");
            var hiddenKeywordInput = document.getElementById("up_hiddenKeyword");

            for (var i = 0; i < keywords.length; i++) {
                var keyword = keywords[i].trim();
                if (keyword !== "") {
                    var tagElement = document.createElement("span");
                    tagElement.textContent = keyword;
                    tagElement.className = "badge bg-primary me-2 hh";

                    // Add a close button to each tag
                    var closeButton = document.createElement("button");
                    closeButton.innerHTML = "&times;"; // Close icon (X)
                    closeButton.className = "btn-close";
                    closeButton.setAttribute("aria-label", "Close");
                    closeButton.addEventListener("click", function() {
                        this.parentNode.parentNode.removeChild(this
                            .parentNode); // Remove the tag when the close button is clicked
                        edit_updateHiddenKeywordInput(); // Update hidden input when removing a tag
                    });
                    tagElement.appendChild(closeButton);

                    keywordTags.appendChild(tagElement);
                }
            }
            edit_updateHiddenKeywordInput(); // Update hidden input with all keywords
            document.getElementById("up_keyword").value = ""; // Clear input after adding tags
        }
    });

    function edit_updateHiddenKeywordInput() {
        var tags = document.querySelectorAll("#up_keywordTags .badge");
        var keywordsArray = [];
        tags.forEach(function(tag) {
            keywordsArray.push(tag.textContent);
        });
        document.getElementById("up_hiddenKeyword").value = keywordsArray.join(",");
    }
</script>
@endpush