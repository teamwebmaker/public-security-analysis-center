import { getAll, getOne } from "../helpers.js";

document.addEventListener('DOMContentLoaded', function () {

   // Read query parameters from the URL (e.g. ?occurrences_task_id=5)
   const params = new URLSearchParams(window.location.search);
   const occurrenceTaskId = params.get('occurrences_task_id');

   // If no task ID is in the URL, just set up listeners and clean links
   if (!occurrenceTaskId) {
      bindOccurrenceModals();
      cleanTaskPaginationLinks();
      return;
   }

   // Build modal ID like: task-occurrences-5
   const modalId = 'task-occurrences-' + occurrenceTaskId;
   const modalElement = getOne(`#${modalId}`);

   // Stop if modal does not exist or Bootstrap is not loaded
   if (!modalElement || typeof bootstrap === 'undefined') return;
   

   // Create and show Bootstrap modal programmatically
   const modal = new bootstrap.Modal(modalElement);
   modal.show();

   // Load modal content via AJAX
   loadOccurrences(modalElement);

   // Set up modal event listeners
   bindOccurrenceModals();
   cleanTaskPaginationLinks();
});

/**
 * Attaches event listeners to all task-occurrence modals
 */
function bindOccurrenceModals() {

   // Guard: Bootstrap must exist
   if (typeof bootstrap === 'undefined') return;
   
   // Select all modals whose ID starts with "task-occurrences-"
   getAll("[id^='task-occurrences-']").forEach(function (modalElement) {

      /**  When modal is about to open */
      modalElement.addEventListener('show.bs.modal', function () {

         // Extract task ID from modal ID
         const taskId = modalElement.id.replace('task-occurrences-', '');

         // Update browser URL without reloading the page (e.g. ?occurrences_task_id=5)
         const url = new URL(window.location.href);
         url.searchParams.set('occurrences_task_id', taskId);
         window.history.replaceState({}, '', url);

         // Load occurrences for this task
         loadOccurrences(modalElement);
      });

      /**
       * When modal is fully closed
       */
      modalElement.addEventListener('hidden.bs.modal', function () {

         // Remove modal-related parameters from the URL
         const url = new URL(window.location.href);
         url.searchParams.delete('occurrences_task_id');

         // Remove pagination parameters related to occurrences
         for (const key of Array.from(url.searchParams.keys())) {
            if (key.startsWith('occurrences_page') || key.startsWith('occurrences_task_page')) {
               url.searchParams.delete(key);
            }
         }

         // Update URL without reloading
         window.history.replaceState({}, '', url);

         // Fix pagination links on the main page
         cleanTaskPaginationLinks();

         // Fix focus issues for accessibility (Bootstrap warning prevention)
         if (modalElement.contains(document.activeElement)) {
            document.activeElement.blur();
            document.body.focus();
         }
      });

      /**
       * Handle pagination clicks inside the modal
       */
      modalElement.addEventListener('click', function (event) {

         // Find clicked pagination link (if any)
         const link = event.target.closest('.pagination a');
         if (!link) return;

         // Prevent full page reload
         event.preventDefault();

         // Load new page of occurrences via AJAX
         loadOccurrences(modalElement, link.getAttribute('href'));
      });
   });
}

/**
 * Loads occurrences HTML into the modal using fetch (AJAX)
 */
function loadOccurrences(modalElement, urlOverride = null) {
   const loader = getOne('.js-occurrences-loader', modalElement);

   // Fetched HTML will be inserted
   const content = getOne('.js-occurrences-content', modalElement);

   if (!loader || !content) return;

   // Use pagination URL if provided, otherwise use modal's data attribute
   const baseUrl = urlOverride || modalElement.dataset.occurrencesUrl;

   if (!baseUrl) return;

   // Get task ID from modal ID
   const taskId = modalElement.id.replace('task-occurrences-', '');

   // Build request URL
   const url = new URL(baseUrl, window.location.origin);
   url.searchParams.set('occurrences_task_id', taskId);

   // Preserve current page number when opening modal initially
   if (!urlOverride) {
      const currentParams = new URLSearchParams(window.location.search);
      const pageParam = currentParams.get('occurrences_task_page');
      if (pageParam) {
         url.searchParams.set('occurrences_task_page', pageParam);
      }
   }

   /** Keep browser URL in sync with modal state */
   const historyUrl = new URL(window.location.href);
   historyUrl.searchParams.set('occurrences_task_id', taskId);

   const pageParam = url.searchParams.get('occurrences_task_page');

   if (pageParam) historyUrl.searchParams.set('occurrences_task_page', pageParam);

   window.history.replaceState({}, '', historyUrl);

   // Show loader and clear old content
   loader.classList.remove('d-none');
   content.innerHTML = '';

   // Fetch HTML from server (AJAX request)
   fetch(url.toString(), {
      headers: {
         // Lets backend know this is an AJAX request
         'X-Requested-With': 'XMLHttpRequest'
      }
   })
      .then(response => response.text())
      .then(html => {
         // Insert returned HTML into modal
         content.innerHTML = html;
      })
      .catch(() => {
         content.innerHTML =
            '<p class="text-danger mb-0">ციკლების ჩატვირთვა ვერ მოხერხდა.</p>';
      })
      .finally(() => {
         loader.classList.add('d-none');
      });
}

/**
 * Cleans modal-related query params from main page pagination links
 */
function cleanTaskPaginationLinks() {

   getAll('.pagination a').forEach(function (link) {

      // Skip pagination links inside modals
      if (link.closest('.modal')) return;

      const url = new URL(link.href, window.location.origin);

      // Remove occurrences-related parameters
      url.searchParams.delete('occurrences_task_id');

      for (const key of Array.from(url.searchParams.keys())) {
         if (key.startsWith('occurrences_page') || key.startsWith('occurrences_task_page')) {
            url.searchParams.delete(key);
         }
      }

      // Update link href
      link.href = url.toString();
   });
}
