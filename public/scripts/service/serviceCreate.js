import './index.js';
import { getById } from '../helpers.js';
import { initSortableSuggestions } from './index.js';


document.addEventListener('DOMContentLoaded', function() {
   initSortableSuggestions({
      services: window.appData?.services || [],
      categorySelect: getById('service_category_id'),
      sortableInput: getById('sortable'),
      nextSortableSpan: getById('next-sortable-value'),
      usedSortablesContainer: getById('used-sortables-container'),
      usedSortablesBadges: getById('used-sortables-badges'),
      nextSortableContainer: getById('next-sortable-container'),
      highlightCurrent: false, // ❌ no highlight, we’re creating new
      autoFillNext: true, // ✅ fill next available automatically
   });
});