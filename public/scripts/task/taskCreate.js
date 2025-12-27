import { getOne } from "../helpers.js";

document.addEventListener('DOMContentLoaded', () => {
   const group = getOne('[data-recurrence-group]');
   if (!group) return;

   const isRecurringSelect = getOne('input[name="is_recurring"], [data-recurrence-select], select[name="is_recurring"]', group);
   const intervalRow = getOne('[data-recurrence-interval]', group);
   // console.log( isRecurringSelect, intervalRow)
   if (!isRecurringSelect || !intervalRow) return;

   const toggleInterval = () => {
      const show = isRecurringSelect.value === '1';
      intervalRow.style.display = show ? '' : 'none';
   };

   toggleInterval();
   isRecurringSelect.addEventListener('change', toggleInterval);
});
