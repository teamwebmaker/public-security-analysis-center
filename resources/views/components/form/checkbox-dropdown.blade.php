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
        @unless ($isItemsEmpty)
            <div class="d-flex justify-content-end border-bottom pb-2 mb-2 position-sticky bg-body"
                style="top: -1rem; z-index: 1; padding-top: 0.5rem;">
                <button type="button" class="btn btn-sm btn-link text-decoration-none p-0"
                    data-checkbox-dropdown-toggle-all>
                    {{ __('static.form.dropdown.select_all') }}
                </button>
            </div>
        @endunless

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
        const checkboxes = Array.from(dropdown.querySelectorAll('.form-check-input'));
        const selectableCheckboxes = checkboxes.filter(checkbox => !checkbox.disabled);
        const toggleAllButton = dropdown.querySelector('[data-checkbox-dropdown-toggle-all]');

        const defaultText = @json(__('static.form.dropdown.select_option'));
        const emptyText = @json(__('static.form.dropdown.not_found', ['label' => $label]));
        const selectedTextTemplate = @json(__('static.form.dropdown.selected_count'));
        const selectAllText = @json(__('static.form.dropdown.select_all'));
        const clearAllText = @json(__('static.form.dropdown.clear_all'));

        // Prevent menu from closing when clicking inside
        dropdown.querySelector('.dropdown-menu').addEventListener('click', function (e) {
            e.stopPropagation();
        });

        // Function to update button text
        function updateButtonText() {
            const count = checkboxes.filter(checkbox => checkbox.checked).length;
            if (count > 0) {
                button.textContent = selectedTextTemplate.replace(':count', count);
            } else {
                button.textContent = checkboxes.length === 0 ? emptyText : defaultText;
            }

            if (toggleAllButton) {
                const allSelected = selectableCheckboxes.length > 0
                    && selectableCheckboxes.every(checkbox => checkbox.checked);

                toggleAllButton.textContent = allSelected ? clearAllText : selectAllText;
                toggleAllButton.setAttribute('aria-pressed', allSelected ? 'true' : 'false');
            }
        }

        // Bind change event to all checkboxes
        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButtonText);
        });

        if (toggleAllButton) {
            toggleAllButton.addEventListener('click', function () {
                const shouldSelectAll = !selectableCheckboxes.every(checkbox => checkbox.checked);

                selectableCheckboxes.forEach(function (checkbox) {
                    if (checkbox.checked === shouldSelectAll) {
                        return;
                    }

                    checkbox.checked = shouldSelectAll;
                    checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                });

                updateButtonText();
            });
        }

        // Initial check (for pre-selected values)
        updateButtonText();
    });
</script>
