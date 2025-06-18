@if($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }}&#xa0;@if($required)<span class="text-danger">*</span>@endif
    </label>
@endif

<input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
    class="{{ trim($class) }} @error($name) is-invalid @enderror" @if($value !== null) value="{{ $value }}" @endif
    @if($placeholder) placeholder="{{ $placeholder }}" @endif @if($type == 'file') accept="{{ $accept }}" @endif
    @if($required) required @endif {{ $attributes }}>

@error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror

@if ($type == 'file')
    <div class="form-text">Supported formats: JPG, PNG, WEBP. Max size: 5MB</div>
@endif