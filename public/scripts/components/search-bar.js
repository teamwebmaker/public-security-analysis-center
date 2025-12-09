document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-search-bar]').forEach(form => {
    if (form.dataset.searchInit) return;
    form.dataset.searchInit = 'true';

    const searchName = form.getAttribute('data-search-name');
    if (!searchName) return;

    const input = form.querySelector(`input[name="${searchName.replace(/"/g, '\\"')}"]`);
    if (!input) return;

    const getSearchTerm = () => {
      const urlParams = new URLSearchParams(window.location.search);
      return urlParams.get(searchName)?.trim() || '';
    };

    const isSearchApplied = () => getSearchTerm().length > 0;

    form.querySelectorAll('.clear-search').forEach(button => {
      button.addEventListener('click', () => {
        input.value = '';
        form.submit();
      });
    });

    input.addEventListener('input', () => {
      if (input.value.trim() === '' && isSearchApplied()) {
        form.submit();
      }
    });

    const currentSearchTerm = getSearchTerm();
    if (currentSearchTerm && input.value.trim() === currentSearchTerm) {
      setTimeout(() => {
        input.focus();
        const val = input.value;
        input.value = '';
        input.value = val;
      }, 100);
    }
  });
});
