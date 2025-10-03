import { getById } from '../helpers.js';
import { initSortableSuggestions } from './index.js';

document.addEventListener('DOMContentLoaded', function() {
   initSortableSuggestions({
      services: window.appData?.services || [],
      currentService: window.appData?.currentService || {},
      categorySelect: getById('service_category_id'),
      sortableInput: getById('sortable'),
      nextSortableSpan: getById('next-sortable-value'),
      usedSortablesContainer: getById('used-sortables-container'),
      usedSortablesBadges: getById('used-sortables-badges'),

      highlightCurrent: true, // ✅ highlight my row
      autoFillNext: false, // ❌ don’t auto-fill, because we’re editing
   });
});