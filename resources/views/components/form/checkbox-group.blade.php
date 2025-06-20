<div class="mb-3">
    @if ($label)
        <label class="form-label">{{ $label }} <span class="text-danger">*</span></label>
    @endif

    <div class="{{ $class }}">
        @foreach($options as $i => $option)
            @php
                $value = is_array($option) ? $option['value'] : $option;
                $text = is_array($option) ? $option['label'] : $option;
                $id = $name . '_option_' . $i;
            @endphp
            <div class="form-check form-check-inline">
                <input class="form-check-input"
                       type="checkbox"
                       name="{{ $name }}[]"
                       value="{{ $value }}"
                       id="{{ $id }}"
                       {{ in_array($value, $selected) ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $id }}">{{ $text }}</label>
            </div>
        @endforeach
    </div>

    @error($name)
        <div class="text-danger small">{{ $message }}</div>
    @enderror

    @if ($label)
        <div class="form-text ps-1">გთხოვთ აირჩიოთ მინიმუმ ერთი</div>
    @endif
</div>
