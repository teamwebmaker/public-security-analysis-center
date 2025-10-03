import { getAll, getById } from "../helpers.js";

document.addEventListener('DOMContentLoaded', function () {
    const roleInput = getById('role_id'); // Now this is a hidden input
    const roleDependentElements = getAll('.role-dependent');

    // Get roles data from PHP
    const roles = window.roles || {};

    function clearSectionInputs(section) {
        const inputs = getAll('input, select, textarea', section);
        inputs.forEach(input => {
            if (input.type === 'checkbox' || input.type === 'radio') {
                input.checked = false;
            } else if (input.tagName === 'SELECT') {
                input.selectedIndex = 0;
            } else {
                input.value = '';
            }
        });
    }

    function updateRoleDependentElements(roleId) {
        const roleName = roles[roleId];

        // First clear inputs in sections that will be hidden
        roleDependentElements.forEach(section => {
            if (
                section.style.display !== 'none' &&
                (!roleName || section.dataset.role !== roleName)
            ) {
                clearSectionInputs(section);
            }
        });

        // Hide all sections
        roleDependentElements.forEach(el => {
            el.style.display = 'none';
        });

        // Show only matching ones
        if (roleName) {
            const elementsToShow = getAll(`.role-dependent[data-role="${roleName}"]`);
            elementsToShow.forEach(el => {
                el.style.display = 'block';
            });
        }
    }

    // Observer for changes in the hidden input's value
    const observer = new MutationObserver(() => {
        updateRoleDependentElements(roleInput.value);
    });

    observer.observe(roleInput, {
        attributes: true,
        attributeFilter: ['value'],
    });

    // Initial update
    if (roleInput.value) {
        updateRoleDependentElements(roleInput.value);
    }
});
