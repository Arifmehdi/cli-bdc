
@extends('backend.admin.layouts.master')
@push('css')
@endpush
@section('content')
    <div class="row">
        <div class="col-md-12">
            @if (session()->has('message'))
                <div class="alert alert-success">{{ session()->get('message')}}</div>
            @endif
            <section class="content">


                <div class="col-md-12">
                    <div class="card">

                        <div class="card-header">
                            <p>{{ $page_content->header_title ?? 'Setup New Page' }}</p>
                            <a href="{{ route('admin.frontend.all.page')}}" class="btn btn-success btn-sm float-right mb-4">All Pages</a>
                            <form action="{{ isset($page_content) ? route('admin.frontend.update.page',$page_content->id) : route('admin.frontend.page.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                            <div class="form-group">
                                <label for="make">Page Name <span style="color:red">*</span></label>
                                <input type="text" name="page_title" class="form-control" id="page_title" value="{{ isset($page_content) ? $page_content->title : old('page_title')}}" onblur="checkDuplicateTitle()">
                                <span id="titleError" class="text-danger error-message"></span>
                                @if ($errors->has('page_title'))
                                    <span class="text-danger error-message">{{ $errors->first('page_title') }}</span>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="make">Permalink</label>
                                <input type="text" name="permalink" id="permalink" class="form-control" value="{{ isset($page_content) ? $page_content->slug : ''}} " readonly>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="form-group">
                                <label for="make">Page Content</label>
                                <textarea name="description" id="description" cols="200" rows="200" aria-valuetext="{{ old('description')}}">
                                    {{ isset($page_content) ? $page_content->description : ''}}
                                </textarea>
                                <span class="text-danger error-message" id="description_error"></span>
                            </div>


                            <p>Page SEO Part</p>
                            <hr/>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6"> <label for="make">Meta Title</label></div>
                                                <div class="col-md-6">
                                                    @php
                                                    $title = App\Models\GeneralSetting::select('site_title', 'separator')->first();

                                                    @endphp
                                                    <a href="#" class="btn btn-success btn-sm float-right fw-bold dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="text-align: end">Insert Variable</a>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        <a class="dropdown-item insert-variable_title" href="#" data-title="{{$title->site_title}}">Site Title</a>
                                                        <a class="dropdown-item insert-variable_title" href="#" data-separator="{{$title->separator}}">Separator</a>
                                                      </div>
                                            </div>
                                        </div>


                                        <input type="text" name="seo_title" class="form-control" id="seo_title" value="  {{ isset($page_content) ? $page_content->seo_title : ''}}">

                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6"><label for="make">Meta Description </label></div>
                                            <div class="col-md-6">

                                                <a href="#" class="btn btn-success btn-sm float-right fw-bold dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Insert Variable</a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <a class="dropdown-item insert-variable" href="#" data-title="{{$title->site_title}}">Site Title</a>
                                                    <a class="dropdown-item insert-variable" href="#" data-separator="{{$title->separator}}">Separator</a>
                                                  </div>
                                            </div>
                                        </div>

                                      <textarea name="seo_description" id="seo_description" class="form-control" style="height: 37px;" cols="30" rows="10">{{ isset($page_content) ? $page_content->seo_description : ''}}</textarea>

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="make">SEO Keyword</label>
                                        <input type="text" class="form-control" id="keyword"
                                        value="{{ isset($page_content) ? $page_content->seo_keyword : old('keyword') }} " id="keyword">
                                        <div id="keywordTags"></div>
                                        <input type="hidden" name="keywords[]" id="hiddenKeyword">
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="card-footer">
                            <button class="btn btn-success float-right">{{ isset($page_content) ? 'Update' : 'Publish'}}</button>
                        </div>

                    </div>
                </div>
            </form>
            </section>

        </div>
    </div>
@endsection
@push('js')
    <script>
        $('.dropify').dropify();

      $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    // description editor js start

    ClassicEditor
    .create( document.querySelector( '#description'),{
        ckfinder:{
            uploadUrl: '{{ route('ckeditor.upload').'?_token='.csrf_token()}}'
        }
    } )
    .catch( error => {
        console.error( error );
    } );

    // description editor js close


    document.addEventListener('DOMContentLoaded', function() {
    const pageTitleInput = document.getElementById('page_title');
    const permalinkInput = document.getElementById('permalink');

    pageTitleInput.addEventListener('input', function() {
        const pageTitle = this.value.trim().toLowerCase();
        const permalink = pageTitle.replace(/\s+/g, '-');
        permalinkInput.value = permalink;
    });

    pageTitleInput.addEventListener('blur', function() {
        const pageTitle = this.value.trim().toLowerCase();
        const permalink = pageTitle.replace(/\s+/g, '-');
        permalinkInput.value = permalink;
    });
});


function checkDuplicateTitle() {
        var title = document.getElementById('page_title').value;
        if (title.trim() !== '') {

            $.ajax({
                url: "{{ route('admin.check.duplicate.title') }}",
                type: "POST",
                data: {
                    title: title,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.exists) {
                        document.getElementById('titleError').innerText = "Title already exists!";
                    } else {

                        document.getElementById('titleError').innerText = "";
                    }
                }
            });
        }
    }



    // meta tag javascript

    document.getElementById("keyword").addEventListener("keyup", function(event) {
    if (event.key === "Enter" || event.key === ",") {
        event.preventDefault();
        var keywords = document.getElementById("keyword").value.trim().split(",");
        var keywordTags = document.getElementById("keywordTags");
        var hiddenKeywordInput = document.getElementById("hiddenKeyword");

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
                    this.parentNode.parentNode.removeChild(this.parentNode); // Remove the tag when the close button is clicked
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
        keywordsArray.push(tag.textContent);
    });
    document.getElementById("hiddenKeyword").value = keywordsArray.join(",");
}


// variable set ajax

    // Function to set the value of the textarea when a dropdown item is clicked
    document.querySelectorAll('.insert-variable_title').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            const textarea = document.getElementById('seo_title');
            const value = item.dataset.title || item.dataset.separator;
            textarea.value += value; // You can modify how you want to insert the value here
        });
    });

    document.querySelectorAll('.insert-variable').forEach(item => {
        item.addEventListener('click', event => {
            event.preventDefault();
            const textarea = document.getElementById('seo_description');
            const value = item.dataset.title || item.dataset.separator;
            textarea.value += value; // You can modify how you want to insert the value here
        });
    });

    </script>
@endpush
