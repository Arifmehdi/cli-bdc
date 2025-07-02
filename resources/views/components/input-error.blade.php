@props(['messages' => '', 'errorId' => null])

<div {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
    @if ($errorId)
        <span class="text-danger" id="{{ $errorId }}">{{ $messages }}</span>
    @else
        <span class="text-danger">{{ $messages }}</span>
    @endif
</div>


