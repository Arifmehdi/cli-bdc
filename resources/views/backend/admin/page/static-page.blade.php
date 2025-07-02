
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
                           <p>All Static Page List </p>
                            <a href="{{ route('admin.frontend.add.page')}}" class="btn btn-success btn-sm float-right m-2">Add New Page</a>
                            <a href="{{ route('admin.frontend.all.page')}}" class="btn btn-warning px-5 m-2">Back</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr>
                                    <th>Sl</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Keyword</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>


                                    @forelse ($pages as $page)
                                    <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td><input type="text" value="{{$page->title}}" class="form-control" id="title{{$page->id}}"></td>
                                    <td><input type="text" value="{{$page->description}}" class="form-control" id="description{{$page->id}}"></td>
                                    <td><input type="text" value="{{$page->keyword}}" class="form-control" id="keyword{{$page->id}}"></td>
                                    <td>
                                        <select class="action-select form-control {{ $page->status == 1 ? 'bg-success' : '' }}" style="font-size:10px; font-weight:bold; opacity:97%" data-id="{{$page->id}}">
                                            <option {{ $page->status == 1 ? 'selected' : '' }} value="{{ $page->status }}">Active</option>
                                            <option {{ $page->status == 0 ? 'selected' : '' }} value="{{ $page->status }}">Inactive</option>
                                        </select>

                                    </td>
                                    <td><a href="javascript:void(0)" class="btn btn-sm btn-success" title="edit" onclick="updateData({{$page->id}})">Update</a></td>
                                    </tr>
                                    @empty
                                    <tr>
                                    <td colspan="5">No Pages Available</td>
                                    </tr>
                                    @endforelse



                            </table>

                        </div>



                    </div>
                </div>
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
    // page delete
$(document).on('click', ".deletePage", function(e){
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
                        url: "{{ route('admin.frontend.page.delete') }}",
                        type: 'post',
                        data: {
                            id: id
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                toastr.success(response.message);
                                location.reload();
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
                    url:"{{route('admin.fontend.static.page.status.change')}}",
                    method:"post",
                    data:{id:id},
                    success:function(res){
                        if(res.status == 'success')
                        {
                            toastr.success(res.message);
                            location.reload();
                        }
                    }
                })
            });




            function updateData(id)
            {
                var title = $('#title'+id).val();
                var description = $('#description'+id).val();
                var keyword = $('#keyword'+id).val();

                $.ajax({
                    url:"{{route('admin.fontend.static.page.update')}}",
                    method:"post",
                    data:{id:id,title:title,description:description,keyword:keyword},
                    success:function(res){

                        if(res.status == 'success')
                        {
                            toastr.success(res.message);
                        }


                    }
                })
            }

</script>
@endpush
