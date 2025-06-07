<form method="GET" action="{{ url()->current() }}" class="d-inline">
    @foreach(request()->except($name) as $key => $value)
        @if(is_array($value))
            @foreach($value as $arrayValue)
                <input type="hidden" name="{{ $key }}[]" value="{{ $arrayValue }}">
            @endforeach
        @else
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endif
    @endforeach

    <select name="{{ $name }}" id="{{ $name }}" class="{{ $class }}" onchange="this.form.submit()">
        @foreach($options as $value => $label)
            <option value="{{ $value }}" @selected($selected === $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>
</form>