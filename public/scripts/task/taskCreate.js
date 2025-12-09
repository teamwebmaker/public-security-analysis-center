document.addEventListener('DOMContentLoaded', () => {
   const group = document.querySelector('[data-recurrence-group]');
   if (!group) return;

   const isRecurringSelect = group.querySelector('input[name="is_recurring"], [data-recurrence-select], select[name="is_recurring"]');
   const intervalRow = group.querySelector('[data-recurrence-interval]');
   console.log( isRecurringSelect,
intervalRow)
   if (!isRecurringSelect || !intervalRow) return;

   const toggleInterval = () => {
      const show = isRecurringSelect.value === '1';
      intervalRow.style.display = show ? '' : 'none';
   };

   toggleInterval();
   isRecurringSelect.addEventListener('change', toggleInterval);
});
