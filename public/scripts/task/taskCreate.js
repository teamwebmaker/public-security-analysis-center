import { getOne } from "../helpers.js";

document.addEventListener('DOMContentLoaded', () => {
   initServiceMode();
   initRecurrence();
});

function initServiceMode() {
   const serviceMode = getOne('input[name="service_mode"], select[name="service_mode"]');
   const existingField = getOne('[data-existing-service-field]');
   const temporaryField = getOne('[data-temporary-service-field]');

   if (!serviceMode || !existingField || !temporaryField) return;

   const existingInput = getOne('input[name="service_id"], select[name="service_id"]', existingField);
   const temporaryInput = getOne('input[name="temporary_service_name"]', temporaryField);

   const toggleServiceFields = () => {
      const usesTemporaryService = serviceMode.value === 'temporary';

      existingField.style.display = usesTemporaryService ? 'none' : '';
      temporaryField.style.display = usesTemporaryService ? '' : 'none';

      if (existingInput) existingInput.disabled = usesTemporaryService;
      if (temporaryInput) {
         temporaryInput.disabled = !usesTemporaryService;
         temporaryInput.required = usesTemporaryService;
      }
   };

   toggleServiceFields();
   serviceMode.addEventListener('change', toggleServiceFields);
}

function initRecurrence() {
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
}
