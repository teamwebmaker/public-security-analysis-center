// window.appData?.services || [],

	document.addEventListener('DOMContentLoaded', function () {
			const roleSelect = document.getElementById('role_id');
			const roleDependentElements = document.querySelectorAll('.role-dependent');

			// Get roles data from PHP
			const roles = window.roles || {};
         
			// Function to clear inputs in a section
			function clearSectionInputs(section) {
				const inputs = section.querySelectorAll('input, select, textarea');

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

			// Function to update UI based on selected role
			function updateRoleDependentElements(roleId) {
				const roleName = roles[roleId];

				// First clear all inputs in sections that will be hidden
				roleDependentElements.forEach(section => {
					// If this section is currently visible but won't be after update
					if (section.style.display !== 'none' &&
						(!roleName || section.dataset.role !== roleName)) {
						clearSectionInputs(section);
					}
				});

				// Hide all role-dependent elements first
				roleDependentElements.forEach(el => {
					el.style.display = 'none';
				});

				// Show only the elements that match the selected role
				if (roleName) {
					const elementsToShow = document.querySelectorAll(`.role-dependent[data-role="${roleName}"]`);
					elementsToShow.forEach(el => {
						el.style.display = 'block';
					});
				}
			}

			// Initial update based on selected role (if any)
			if (roleSelect.value) {
				updateRoleDependentElements(roleSelect.value);
			}

			// Update when role changes
			roleSelect.addEventListener('change', function (e) {
				updateRoleDependentElements(e.target.value);
			});
		});