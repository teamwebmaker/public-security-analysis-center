document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-filter-bar]').forEach(wrapper => {
    if (wrapper.dataset.initialized) return;
    wrapper.dataset.initialized = 'true';

    const filters = JSON.parse(wrapper.getAttribute('data-filters') || '{}');
    const activeFilters = JSON.parse(wrapper.getAttribute('data-active-filters') || '[]');
    const form = wrapper.querySelector('form');
    const rowsContainer = wrapper.querySelector('[data-filter-rows]');
    const addButton = wrapper.querySelector('[data-add-filter]');

    if (!form || !rowsContainer || !addButton) return;

    const appendOption = (select, value, label, selected = false) => {
      const option = document.createElement('option');
      option.value = value;
      option.textContent = label;
      option.selected = selected;
      select.appendChild(option);
    };

    const populateValues = (valueSelect, key, selectedValue = '') => {
      valueSelect.innerHTML = '';

      if (!key || !filters[key]) {
        valueSelect.name = '';
        valueSelect.disabled = true;
        appendOption(valueSelect, '', 'ჯერ აირჩიეთ ფილტრი');
        return;
      }

      valueSelect.name = `filter[${key}]`;
      valueSelect.disabled = false;
      appendOption(valueSelect, '', `${filters[key].label || 'ფილტრი'} — ყველა`);

      Object.entries(filters[key].options || {}).forEach(([value, label]) => {
        appendOption(valueSelect, value, label, String(value) === String(selectedValue));
      });
    };

    const updateAvailableKeys = () => {
      const rows = [...rowsContainer.querySelectorAll('[data-filter-row]')];
      const selectedKeys = rows
        .map(row => row.querySelector('[data-filter-key]')?.value)
        .filter(Boolean);

      rows.forEach(row => {
        const keySelect = row.querySelector('[data-filter-key]');
        if (!keySelect) return;

        [...keySelect.options].forEach(option => {
          option.disabled = option.value !== ''
            && option.value !== keySelect.value
            && selectedKeys.includes(option.value);
        });
      });

      addButton.disabled = rows.length >= Object.keys(filters).length;
    };

    const createRow = (selectedKey = '', selectedValue = '') => {
      const row = document.createElement('div');
      row.className = 'd-flex flex-column flex-md-row gap-2 align-items-md-end';
      row.dataset.filterRow = '';

      const keyGroup = document.createElement('div');
      keyGroup.className = 'd-flex flex-column gap-1 flex-grow-1';

      const keyLabel = document.createElement('label');
      keyLabel.className = 'form-label mb-0 small text-muted';
      keyLabel.textContent = 'ფილტრის ველი';

      const keySelect = document.createElement('select');
      keySelect.className = 'form-select form-select-sm filter-bar-select-key';
      keySelect.dataset.filterKey = '';
      keySelect.setAttribute('aria-label', 'აირჩიეთ ფილტრის ტიპი');
      appendOption(keySelect, '', 'აირჩიეთ ფილტრი', selectedKey === '');

      Object.entries(filters).forEach(([key, definition]) => {
        appendOption(keySelect, key, definition.label || key, key === selectedKey);
      });

      keyGroup.append(keyLabel, keySelect);

      const valueGroup = document.createElement('div');
      valueGroup.className = 'd-flex flex-column gap-1 flex-grow-1';

      const valueLabel = document.createElement('label');
      valueLabel.className = 'form-label mb-0 small text-muted';
      valueLabel.textContent = 'მნიშვნელობა';

      const valueSelect = document.createElement('select');
      valueSelect.className = 'form-select form-select-sm filter-bar-select-value';
      valueSelect.dataset.filterValue = '';
      valueSelect.setAttribute('aria-label', 'აირჩიეთ ფილტრის მნიშვნელობა');
      populateValues(valueSelect, selectedKey, selectedValue);

      valueGroup.append(valueLabel, valueSelect);

      const removeButton = document.createElement('button');
      removeButton.type = 'button';
      removeButton.className = 'btn btn-sm btn-outline-danger';
      removeButton.dataset.removeFilter = '';
      removeButton.setAttribute('aria-label', 'ფილტრის წაშლა');
      removeButton.innerHTML = '<i class="bi bi-trash"></i><span class="d-md-none ms-1">წაშლა</span>';

      keySelect.addEventListener('change', () => {
        populateValues(valueSelect, keySelect.value);
        updateAvailableKeys();
        valueSelect.focus();
      });

      removeButton.addEventListener('click', () => {
        row.remove();
        if (!rowsContainer.querySelector('[data-filter-row]')) {
          createRow();
        }
        updateAvailableKeys();
      });

      row.append(keyGroup, valueGroup, removeButton);
      rowsContainer.appendChild(row);
      updateAvailableKeys();
    };

    if (Array.isArray(activeFilters) && activeFilters.length > 0) {
      activeFilters.forEach(filter => createRow(filter.key || '', filter.value || ''));
    } else {
      createRow();
    }

    addButton.addEventListener('click', () => {
      createRow();
      const newRow = rowsContainer.lastElementChild;
      newRow?.querySelector('[data-filter-key]')?.focus();
    });
  });
});
