document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-filter-bar]').forEach(wrapper => {
    if (wrapper.dataset.initialized) return;
    wrapper.dataset.initialized = 'true';

    const filters = JSON.parse(wrapper.getAttribute('data-filters') || '{}');
    const initialKey = wrapper.getAttribute('data-initial-key') || '';
    const initialValue = wrapper.getAttribute('data-initial-value') || '';

    const form = wrapper.querySelector('form');
    const keySelect = wrapper.querySelector('[data-filter-key]');
    const valueSelect = wrapper.querySelector('[data-filter-value]');
    if (!form || !keySelect || !valueSelect) return;

    const buildOptions = (key) => {
      valueSelect.innerHTML = '';

      if (!key || !filters[key]) {
        valueSelect.name = '';
        valueSelect.disabled = true;

        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'ჯერ აირჩიეთ ფილტრი';
        valueSelect.appendChild(opt);
        return;
      }

      valueSelect.disabled = false;
      valueSelect.name = `filter[${key}]`;

      const defaultOpt = document.createElement('option');
      defaultOpt.value = '';
      defaultOpt.textContent = `${filters[key].label || 'ფილტრი'} — ყველა`;
      valueSelect.appendChild(defaultOpt);

      Object.entries(filters[key].options || {}).forEach(([val, label]) => {
        const opt = document.createElement('option');
        opt.value = val;
        opt.textContent = label;
        valueSelect.appendChild(opt);
      });
    };

    const activeKey = initialKey || keySelect.value || '';
    buildOptions(activeKey);

    if (activeKey && initialValue !== '') {
      valueSelect.value = initialValue;
      valueSelect.disabled = false;
    }

    keySelect.addEventListener('change', () => {
      const selectedKey = keySelect.value;
      buildOptions(selectedKey);
      valueSelect.value = '';
      valueSelect.focus();
    });

    valueSelect.addEventListener('change', () => {
      if (!valueSelect.name) return;
      form.submit();
    });
  });
});
