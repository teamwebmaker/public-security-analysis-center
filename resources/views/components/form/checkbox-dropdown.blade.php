<div class="dropdown position-relative w-100">
    <button class="btn border dropdown-toggle text-start" type="button" id="dropdown-{{ $dropdownId }}"
        data-bs-toggle="dropdown" aria-expanded="false">
        {{ $label }}
    </button>

    <div class="dropdown-menu w-100 p-3" aria-labelledby="dropdown-{{ $dropdownId }}"
        style="max-height: 200px; overflow-y: auto;">
        @foreach ($items as $item)
            @php
                $itemId = data_get($item, $idField);
                $itemLabel = data_get($item, $labelField);
              @endphp
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="{{ $name }}[]" value="{{ $itemId }}"
                    id="dropdown-item-{{ $itemId }}" @if(collect($selected)->contains($itemId)) checked @endif>
                <label class="form-check-label" for="dropdown-item-{{ $itemId }}">
                    {{ $itemLabel }}
                </label>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdown = document.getElementById('dropdown-{{ $dropdownId }}').parentNode;
        dropdown.querySelector('.dropdown-menu').addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });
</script>