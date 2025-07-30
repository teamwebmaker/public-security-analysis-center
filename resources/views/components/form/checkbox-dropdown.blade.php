@if($label)
    <label for="dropdown-{{ $dropdownId }}" class="form-label">
        {{ $label }}
    </label>
@endif

<div class="dropdown position-relative w-100">
    @php
        $isItemsEmpty = count($items) === 0;
    @endphp
    <button class="btn border dropdown-toggle text-start" type="button" id="dropdown-{{ $dropdownId }}"
        data-bs-toggle="dropdown" aria-expanded="false" {{ $isItemsEmpty ? 'disabled' : '' }}>
        @if ($isItemsEmpty)
            {{ $label }} ვერ მოიძებნა
        @else

            აირჩიეთ ვარიანტი
        @endif
    </button>

    <div class="dropdown-menu p-3" aria-labelledby="dropdown-{{ $dropdownId }}"
        style="max-height: 200px; overflow-y: auto;">
        @forelse ($items as $item)
            @php
                $itemId = data_get($item, $idField);
                $itemLabel = data_get($item, $labelField);
              @endphp

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="{{ $name }}[]" value="{{ $itemId }}"
                    id="dropdown-{{ $dropdownId }}-item-{{ $itemId }}" @if(collect($selected)->contains($itemId)) checked
                    @endif>
                <label class="form-check-label" style="user-select: none;"
                    for="dropdown-{{ $dropdownId }}-item-{{ $itemId }}">
                    {{ $itemLabel }}
                </label>
            </div>
        @empty
            <div class="text-muted">empty</div>
        @endforelse
    </div>
</div>
<div class="form-text">შეგიძლიათ შეარჩიოთ ერთი ან რამდენიმე</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdown = document.getElementById('dropdown-{{ $dropdownId }}').parentNode;
        dropdown.querySelector('.dropdown-menu').addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });
</script>