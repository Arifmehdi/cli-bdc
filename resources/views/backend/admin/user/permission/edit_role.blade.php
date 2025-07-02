@extends('backend.admin.layouts.master')
@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Content Header (Page header) -->

        {{-- breadcumb bosbe dynamic --}}

        <!-- Main content -->
        <section class="content">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit '{{$role->name}}'</h3>
                    <a href="{{route('admin.roles.index')}}" class="btn btn-primary btn-sm float-right" > <i class="fas fa-list"></i> All Role</a>
                </div>
                <!-- /.card-header -->
                <div class="card-body">

                            <form action="{{route('admin.roles.update',$role->id)}}" method="post" id="editRolePermissionForm">
                                @method('PUT')
                                @csrf
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="rolename">Role Name</label>
                                        <input type="text" class="form-control" name="rolename" value="{{$role->name}}" id="rolename" placeholder="Enter Role" >
                                        <x-input-error  :errorId="'rolename_error'"/>
                                    </div>


                                    <div class="form-group">
                                        <label>Permissions</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkpermissionAll" {{  \App\Models\User::roleHasPermission($role,$All_permissions) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="checkpermissionAll">All</label>
                                        </div>
                                        <hr>
                                        @php $i =1;  @endphp
                                        @foreach($group_permissions as $group)
                                            @php
                                                $permissions = \App\Models\User::getpermissionByGroupName($group->name);
                                                $j = 1;
                                            @endphp
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="{{ $i }}management" value="{{$group->name}}" onclick="checkPermissionByGroup('role-{{ $i }}-management-checkbox',this)" {{  \App\Models\User::roleHasPermission($role,$permissions) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="checkpermission">{{$group->name}}</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 role-{{ $i }}-management-checkbox">

                                                    @foreach($permissions as $permission)
                                                        <div class="form-check">
                                                            <input class="form-check-input" {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                            type="checkbox" name="permissions[]" id="permission-{{$permission->id}}" value="{{$permission->name}}"
                                                                   onclick="checkSinglePermission('role-{{ $i }}-management-checkbox','{{ $i }}management',{{count($permissions)}})"
                                                            >
                                                            <label class="form-check-label" for="permission-{{$permission->id}}">{{$permission->name}}</label>
                                                        </div>
                                                        @php $j++; @endphp
                                                    @endforeach

                                                </div>

                                            </div>

                                            @php $i++; @endphp

                                        @endforeach


                                    </div>



                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Update Role</button>
                                </div>
                            </form>
                        </div>

            </div>

        </section>
        <!-- /.content -->
    </div>
</div>
@endsection

@push('js')
@include('backend.admin.user.permission.partial.scripts')

<script>
    $(document).ready(function(){

        $('#editRolePermissionForm').submit(function (e) {
            e.preventDefault();

            // Serialize the form data
            var formData = $(this).serialize();

            // Make Ajax request
            $.ajax({
                url: $(this).attr('action'),
                type: $(this).attr('method'),
                data: formData,
                success: function (response) {
                    // Handle success response
                    // console.log(response);
                    // return
                    $('#exampleModal').modal('hide');
                    toastr.success(response.message);
                    window.location.reload();
                },
                error: function (xhr) {
                    // Handle error response
                    var errors = xhr.responseJSON.errors;
                    // console.log(errors);
                    // return
                    // $('.input-error').text('');

                    // Display validation errors
                    $.each(errors, function (key, value) {
                        // Display the error messages
                        $('#' + key + '_error').text(value[0]);
                    });
                }
            });
        });
    })
</script>
@endpush
