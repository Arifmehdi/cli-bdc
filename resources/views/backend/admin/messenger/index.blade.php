@extends('backend.admin.layouts.master')

@section('content')
@include('backend.admin.messenger.partials.flash')
<div class="d-flex justify-content-end mb-3">
    <a href="{{ route('messages.create') }}" class="btn btn-primary">Create Message</a>
</div>
@each('backend.admin.messenger.partials.thread', $threads, 'thread', 'backend.admin.messenger.partials.no-threads')
@endsection

@push('js')

@endpush
