@props([
    'label' => 'Label',
    'name' => 'select',
    'value' => $value ?? null
])

<div class="col-md-5">
    <div class="form-group">
        <label class="form-label">{{ $label }}</label>
        <select class="form-control select2-show-search border-bottom-0 w-100 select2-show-search" data-placeholder="Select" name="{{ $name }}">
            <optgroup label="Categories">
                <option value="1" {{ $value == 1 ? 'selected' : '' }}>Germany</option>
                <option value="2" {{ $value == 2 ? 'selected' : '' }}>Mercedes-Sapiente Swift</option>
                <option value="3" {{ $value == 3 ? 'selected' : '' }}>Canada</option>
                <option value="4" {{ $value == 4 ? 'selected' : '' }}>USA</option>
            </optgroup>
        </select>
    </div>
</div>
