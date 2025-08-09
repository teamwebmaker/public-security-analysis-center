@if($label)
    <label for="{{ $id }}" class="form-label">
        {{ $label }}&#xa0;@if($required)<span class="text-danger">*</span>@endif
    </label>
@endif

<select id="{{ $id }}" name="{{ $name }}" class="{{ $class }}@error($name) is-invalid @enderror" @if($required) required
@endif {{ $attributes }}>
    @foreach($options as $value => $text)
        <option value="{{ $value }}" @if($selected == $value) selected @endif>
            {{ $text }}
        </option>
    @endforeach
</select>

@error($name)
    <div class="invalid-feedback">
        {{ $message }}
    </div>
@enderror