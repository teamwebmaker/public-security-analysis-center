@if($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }}&#xa0;@if($required)<span class="text-danger">*</span>@endif
    </label>
@endif

<div class="input-group">
    @if ($icon && $iconPosition == 'left')
        <span class="input-group-text"><i class="bi bi-{{ $icon }}"></i></span>
    @endif

    <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}"
        class="{{ trim($class) }} @error($name) is-invalid @enderror" @if($value !== null) value="{{ $value }}" @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif @if($type == 'file') accept="{{ $accept }}" @endif
        @if($required) required @endif {{ $attributes }}>

    @if ($icon && $iconPosition == 'right')
        <span class="input-group-text"><i class="bi bi-{{ $icon }}"></i></span>
    @endif

    @error($name)
        <div class="invalid-feedback">
            {{ $message }}
        </div>
    @enderror
</div>

@if ($type == 'file' && $isImage)
    <div class="form-text">მხარდაჭერილი ფორმატები: JPG, PNG, WEBP. მაქსიმალური ზომა: 2MB</div>
@endif