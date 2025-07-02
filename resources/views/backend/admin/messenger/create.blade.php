@extends('backend.admin.layouts.master')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3>Create a New Message</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('messages.store') }}" method="post">
                        {{ csrf_field() }}

                        <!-- Subject Form Input -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" placeholder="Enter subject"
                                   value="{{ old('subject') }}" required>
                        </div>

                        <!-- Message Form Input -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" id="message" class="form-control" rows="5"
                                      placeholder="Type your message here..." required>{{ old('message') }}</textarea>
                        </div>

                        <!-- Recipients Form Input -->
                        @if($users->count() > 0)
                            <div class="mb-3">
                                <label for="recipients" class="form-label">Recipients</label>
                                <select name="recipients[]" id="recipients" class="form-select form-control" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                No users available to send messages to.
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-success w-100">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <div class="row">
    <h1>Create a new message</h1>
    <form action="{{ route('messages.store') }}" method="post">
        {{ csrf_field() }}
        <div class="col-md-6">
            <!-- Subject Form Input -->
            <div class="form-group">
                <label class="control-label">Subject</label>
                <input type="text" class="form-control" name="subject" placeholder="Subject"
                       value="{{ old('subject') }}">
            </div>

            <!-- Message Form Input -->
            <div class="form-group">
                <label class="control-label">Message</label>
                <textarea name="message" class="form-control">{{ old('message') }}</textarea>
            </div>

            @if($users->count() > 0)
                <div class="checkbox">
                    @foreach($users as $user)
                        <label title="{{ $user->name }}"><input type="checkbox" name="recipients[]"
                                                                value="{{ $user->id }}">{!!$user->name!!}</label>
                    @endforeach
                </div>
            @endif

            <!-- Submit Form Input -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary form-control">Submit</button>
            </div>
        </div>
    </form>
</div> --}}
@endsection

@push('js')

@endpush
