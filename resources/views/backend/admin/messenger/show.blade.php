{{-- @extends('backend.admin.layouts.master')

@section('content')
<div class="row">
    <div class="col-md-6">
        <h1>{{ $thread->subject }}</h1>
        @each('backend.admin.messenger.partials.messages', $thread->messages, 'message')

        @include('backend.admin.messenger.partials.form-message')
    </div>
</div>
@endsection

@push('js')

@endpush --}}

@extends('backend.admin.layouts.master')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h3>{{ $thread->subject }}</h3>
            </div>
            <div class="card-body">
                <!-- Display Messages -->
                @each('backend.admin.messenger.partials.messages', $thread->messages, 'message')

                <!-- Message Reply Form -->
                <div class="mt-4">
                    @include('backend.admin.messenger.partials.form-message')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
