
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
                           <p>All Page list </p>
                            <a href="{{ route('admin.frontend.show.static.page')}}" class="btn btn-success btn-sm float-right m-2">Show Static Page</a>
                            <a href="{{ route('admin.frontend.add.page')}}" class="btn btn-success btn-sm float-right m-2">Add New Page</a>
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tr>
                                    <th>Sl</th>
                                    <th>Title</th>
                                    <th>Slug</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>


                                    @forelse ($pages as $page)
                                    <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$page->title}}</td>
                                    <td>{{$page->slug}}</td>
                                    <td>
                                        <select class="action-select form-control {{ $page->status == 1 ? 'bg-success' : '' }}" style="font-size:10px; font-weight:bold; opacity:97%" data-id="{{$page->id}}">
                                            <option {{ $page->status == 1 ? 'selected' : '' }} value="{{ $page->status }}">Active</option>
                                            <option {{ $page->status == 0 ? 'selected' : '' }} value="{{ $page->status }}">Inactive</option>
                                        </select>

                                    </td>
                                    <td><a href="{{ route('admin.frontend.edit.page',$page->id)}}" class="btn btn-sm btn-success" title="edit"><i class="fas fa-edit"></i></a> |<a href="{{ url($page->slug) }}" class="btn btn-sm btn-primary" title="preview" target="_blank"><i class="fas fa-eye"></i></a> | <a href="#" title="delete" class="btn btn-danger btn-sm deletePage" data-id="{{$page->id}}"><i class="fas fa-trash"></i></a></td>
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
                    url:"{{route('admin.page.status.change')}}",
                    method:"post",
                    data:{id:id},
                    success:function(res){
                        console.log(res)
                        toastr.success(res.message);
                        location.reload();
                    }
                })
            });


</script>
@endpush
