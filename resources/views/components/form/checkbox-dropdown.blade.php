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
            {{ __('static.form.dropdown.not_found', ['label' => $label]) }}
        @else
            {{ __('static.form.dropdown.select_option') }}
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
            <div class="text-muted">{{ __('static.form.dropdown.empty') }}</div>
        @endforelse
    </div>
</div>

<div class="form-text">{{ __('static.form.dropdown.help_text') }}</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropdown = document.getElementById('dropdown-{{ $dropdownId }}').parentNode;
        const button = document.getElementById('dropdown-{{ $dropdownId }}');
        const checkboxes = dropdown.querySelectorAll('.form-check-input');

        const defaultText = @json(__('static.form.dropdown.select_option'));
        const emptyText = @json(__('static.form.dropdown.not_found', ['label' => $label]));
        const selectedTextTemplate = @json(__('static.form.dropdown.selected_count'));

        // Prevent menu from closing when clicking inside
        dropdown.querySelector('.dropdown-menu').addEventListener('click', function (e) {
            e.stopPropagation();
        });

        // Function to update button text
        function updateButtonText() {
            const count = dropdown.querySelectorAll('.form-check-input:checked').length;
            if (count > 0) {
                button.textContent = selectedTextTemplate.replace(':count', count);
            } else {
                button.textContent = checkboxes.length === 0 ? emptyText : defaultText;
            }
        }

        // Bind change event to all checkboxes
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButtonText);
        });

        // Initial check (for pre-selected values)
        updateButtonText();
    });
</script>