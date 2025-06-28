import ImageUploader from "../imageUploader.js";

document.addEventListener("DOMContentLoaded", function() {
   new ImageUploader('service-image', 'service-image-preview');
});



/**
 * Initializes sortable value suggestions and used-sortable badges 
 * for service category selection.
 * 
 * Works for both create & edit forms.
 * - In create mode: auto-fills next available sortable.
 * - In edit mode: only shows used sortables, highlights current if needed.
 */
export function initSortableSuggestions({
   services, // All existing services
   currentService, // The service being edited (or null in create)
   categorySelect, // <select> element for categories
   sortableInput, // <input> for sortable value
   nextSortableSpan, // <span> for displaying next available value
   usedSortablesContainer, // Wrapper for used-sortable badges
   usedSortablesBadges, // Container for the badges
   nextSortableContainer, // Container for the next value suggestion (optional in edit)
   highlightCurrent = true, // Whether to highlight the current service's sortable
   autoFillNext = false, // Whether to auto-fill next available sortable (create only)
}) {

   function updateSortableSuggestions() {
      const selectedCategoryId = categorySelect.value;

      // If no category selected, hide containers and exit
      if (!selectedCategoryId) {
         usedSortablesContainer.style.display = 'none';
         if (nextSortableContainer) nextSortableContainer.style.display = 'none';
         return;
      }

      // Get services in the selected category
      const filteredServices = services.filter(service =>
         service.service_category_id == selectedCategoryId
      );

      // Collect and sort used sortable values for that category
      const usedSortables = filteredServices
         .map(service => service.sortable)
         .sort((a, b) => a - b);

      // Calculate and apply next available sortable if enabled (create only)
      if (autoFillNext && nextSortableSpan && sortableInput) {
         let nextAvailable = 1;
         if (usedSortables.length > 0) {
            for (let i = 1; i <= Math.max(...usedSortables) + 1; i++) {
               if (!usedSortables.includes(i)) {
                  nextAvailable = i;
                  break;
               }
            }
         }
         nextSortableSpan.textContent = nextAvailable;
         sortableInput.value = nextAvailable;
      }

      // Render badges for used sortables and highlight current if relevant
      if (usedSortables.length > 0) {
         usedSortablesBadges.innerHTML = '';

         usedSortables.forEach(sortable => {
            const badge = document.createElement('span');

            // Only highlight if it's the current service AND in the selected category
            const isCurrent = currentService &&
               parseInt(sortable) === parseInt(currentService.sortable) &&
               currentService.service_category_id == selectedCategoryId;

            if (highlightCurrent && isCurrent) {
               badge.className = 'badge bg-primary';
               badge.textContent = `${sortable} (ჩემი)`;
            } else {
               badge.className = 'badge bg-secondary';
               badge.textContent = sortable;
            }

            usedSortablesBadges.appendChild(badge);
         });

         usedSortablesContainer.style.display = 'block';
         if (nextSortableContainer && autoFillNext) nextSortableContainer.style.display = 'block';
      } else {
         usedSortablesContainer.style.display = 'none';
         if (nextSortableContainer && autoFillNext) nextSortableContainer.style.display = 'block';
      }
   }

   // Run once on load, then on category change
   updateSortableSuggestions();
   categorySelect.addEventListener('change', updateSortableSuggestions);
}