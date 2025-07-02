{{-- //<?php //$class = $thread->isUnread(Auth::id()) ? 'alert-info' : ''; ?> --}}

{{-- <div class="media alert {{ $class }}">
    <h4 class="media-heading">
        <a href="{{ route('messages.show', $thread->id) }}">{{ $thread->subject }}</a>
        ({{ $thread->userUnreadMessagesCount(Auth::id()) }} unread)</h4>
    <p>
        {{ $thread->latestMessage->body }}
    </p>
    <p>
        <small><strong>Creator:</strong> {{ $thread->creator()->name }}</small>
    </p>
    <p>
        <small><strong>Participants:</strong> {{ $thread->participantsString(Auth::id()) }}</small>
    </p>
</div> --}}

<div class="container my-4">
    <!-- Create Thread Button -->

    <!-- Threads Section -->

        <?php $class = $thread->isUnread(Auth::id()) ? 'alert-info' : ''; ?>

        <div class="card shadow-sm mb-3 {{ $class }}">
            <div class="card-body">
                <h4 class="card-title">
                    <a href="{{ route('messages.show', $thread->id) }}" class="text-decoration-none text-dark">
                        {{ $thread->subject }}
                    </a>
                    <span class="badge bg-info text-white">
                        {{ $thread->userUnreadMessagesCount(Auth::id()) }} unread
                    </span>
                </h4>
                <p class="card-text text-muted">
                    {{ \Illuminate\Support\Str::limit($thread->latestMessage->body, 100) }}
                </p>
                <p class="mb-0 text-secondary">
                    <small><strong>Creator:</strong> {{ $thread->creator()->name }}</small>
                </p>
                <p class="text-secondary">
                    <small><strong>Participants:</strong> {{ $thread->participantsString(Auth::id()) }}</small>
                </p>
            </div>
        </div>

</div>

