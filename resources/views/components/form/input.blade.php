@if($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }}@if($required)&#xa0;<span class="text-danger">*</span>@endif
    </label>
@endif

<div class="input-group">
    @if ($icon && $iconPosition == 'left')
        <span class="input-group-text"><i class="bi bi-{{ $icon }}"></i></span>
    @endif

    <input type="{{ $type }}" id="{{ $id }}" name="{{ $name }}" class="{{ trim($class) }}
         @if ($displayError) @error($name) is-invalid @enderror @endif" @if ($minlength !== null)
        minlength="{{ $minlength }}" @endif @if ($maxlength) maxlength="{{ $maxlength }}" @endif @if($value) @endif
        @if($value !== null) value="{{ $value }}" @endif @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($type == 'file') accept="{{ $accept }}" @endif @if($required) required @endif {{ $attributes }}>

    @if ($icon && $iconPosition == 'right')
        <span class="input-group-text"><i class="bi bi-{{ $icon }}"></i></span>
    @endif

    @if ($displayError)
        @error($name)
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    @endif

</div>

@if ($type == 'file' && $isImage)
    <div class="form-text">მხარდაჭერილი ფორმატები: JPG, PNG, WEBP. მაქსიმალური ზომა: 2MB</div>
@endif