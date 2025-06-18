@if($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }}&#xa0;@if($required)<span class="text-danger">*</span>@endif
    </label>
@endif

<textarea id="{{ $id }}" name="{{ $name }}" class="{{ trim($class) }} @error($name) is-invalid @enderror"
    @if($placeholder) placeholder="{{ $placeholder }}" @endif @if($required) required @endif rows="{{ $rows }}"
    @if($minlength) minlength="{{ $minlength }}" @endif {{ $attributes }}>{{ $value }}</textarea>

@error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror