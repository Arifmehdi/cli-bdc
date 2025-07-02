@props([
    'type' => $type ?? null,
    'placeholder' => $placeholder ?? null,
    'label' => $label ?? null,
    'clases' => $class ?? null,
    'name' => $name ?? null,
    'value' => $value ?? null,
    'telephone' => $data ?? null,
])
<div class="{{ ($clases) ? $clases : 'col-sm-12 col-md-12' }}">
    <div class="form-group">
        <label class="form-label">{{$label}}</label>
        <input type="{{$type}}" name="{{$name}}" class="form-control {{$telephone}}" placeholder="{{ $placeholder}}" value="{{ $value}}">
        @error($name)
        <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>
</div>
