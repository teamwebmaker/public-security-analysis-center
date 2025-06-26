import './index.js';
import {
    getById
} from '../heplers.js';


document.addEventListener('DOMContentLoaded', function() {
    const services = window.appData?.services || [];
    const categorySelect = getById('service_category_id');
    const sortableInput = getById('sortable');
    const nextSortableSpan = getById('next-sortable-value');
    const usedSortablesContainer = getById('used-sortables-container');
    const usedSortablesBadges = getById('used-sortables-badges');
    const nextSortableContainer = getById('next-sortable-container');

    // Initialize on page load
    updateSortableSuggestions();

    // Update when category changes
    categorySelect.addEventListener('change', updateSortableSuggestions);

    function updateSortableSuggestions() {
        const selectedCategoryId = categorySelect.value;

        if (!selectedCategoryId) {
            nextSortableContainer.style.display = 'none';
            usedSortablesContainer.style.display = 'none';
            return;
        }

        // Filter services by selected category
        const filteredServices = services.filter(service =>
            service.service_category_id == selectedCategoryId
        );

        // Get all sortable values for this category
        const usedSortables = filteredServices
            .map(service => service.sortable)
            .sort((a, b) => a - b);

        // Calculate next available sortable
        let nextAvailable = 1;
        if (usedSortables.length > 0) {
            for (let i = 1; i <= Math.max(...usedSortables) + 1; i++) {
                if (!usedSortables.includes(i)) {
                    nextAvailable = i;
                    break;
                }
            }
        }

        // Update UI
        nextSortableSpan.textContent = nextAvailable;
        sortableInput.value = nextAvailable;

        // Show/hide used sortables
        if (usedSortables.length > 0) {
            usedSortablesBadges.innerHTML = '';
            usedSortables.forEach(sortable => {
                const badge = document.createElement('span');
                badge.className = 'badge bg-secondary';
                badge.textContent = sortable;
                usedSortablesBadges.appendChild(badge);
            });
            usedSortablesContainer.style.display = 'block';
            nextSortableContainer.style.display = 'block';
        } else {
            usedSortablesContainer.style.display = 'none';
            nextSortableContainer.style.display = 'block';
        }
    }
});