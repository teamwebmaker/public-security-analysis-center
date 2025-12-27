import { getAll, getOne } from "../helpers.js";

document.addEventListener('DOMContentLoaded', function () {
   // Open a specific occurrences modal when the URL contains ?occurrences_task_id=TASK_ID.
   const params = new URLSearchParams(window.location.search);
   const occurrenceTaskId = params.get('occurrences_task_id');

   if (!occurrenceTaskId) {
      // No modal requested; just wire events and clean pagination links.
      bindOccurrenceModals();
      cleanTaskPaginationLinks();
      return;
   }

   const modalId = 'task-occurrences-' + occurrenceTaskId;
   const modalElement = getOne(`#${modalId}`);
   if (!modalElement || typeof bootstrap === 'undefined') {
      return;
   }

   const modal = new bootstrap.Modal(modalElement);
   modal.show();
   loadOccurrences(modalElement);
   bindOccurrenceModals();
   cleanTaskPaginationLinks();
});

function bindOccurrenceModals() {
   // Keep the URL in sync and load occurrences on modal open.
   if (typeof bootstrap === 'undefined') {
      return;
   }

   getAll("[id^='task-occurrences-']").forEach(function (modalElement) {
      // Update URL when modal opens and load data.
      modalElement.addEventListener('show.bs.modal', function () {
         const taskId = modalElement.id.replace('task-occurrences-', '');
         const url = new URL(window.location.href);
         url.searchParams.set('occurrences_task_id', taskId);
         window.history.replaceState({}, '', url);
         loadOccurrences(modalElement);
      });

      modalElement.addEventListener('hidden.bs.modal', function () {
         // Clean the URL and reset focus to avoid aria-hidden warnings.
         const url = new URL(window.location.href);
         url.searchParams.delete('occurrences_task_id');
         for (const key of Array.from(url.searchParams.keys())) {
            if (key.startsWith('occurrences_page') || key.startsWith('occurrences_task_page')) {
               url.searchParams.delete(key);
            }
         }
         window.history.replaceState({}, '', url);
         cleanTaskPaginationLinks();

         if (modalElement.contains(document.activeElement)) {
            document.activeElement.blur();
            document.body.focus();
         }
      });

      modalElement.addEventListener('click', function (event) {
         const link = event.target.closest('.pagination a');
         if (!link) {
            return;
         }

         // Load modal pagination via fetch instead of full page reload.
         event.preventDefault();
         loadOccurrences(modalElement, link.getAttribute('href'));
      });
   });
}

function loadOccurrences(modalElement, urlOverride = null) {
   const loader = getOne('.js-occurrences-loader', modalElement);
   const content = getOne('.js-occurrences-content', modalElement);

   if (!loader || !content) return;
   
   const baseUrl = urlOverride || modalElement.dataset.occurrencesUrl;
   
   if (!baseUrl) return;

   const taskId = modalElement.id.replace('task-occurrences-', '');
   const url = new URL(baseUrl, window.location.origin);
   url.searchParams.set('occurrences_task_id', taskId);
   if (!urlOverride) {
      const currentParams = new URLSearchParams(window.location.search);
      const pageParam = currentParams.get('occurrences_task_page');
      if (pageParam) {
         url.searchParams.set('occurrences_task_page', pageParam);
      }
   }

   // Keep the URL aligned with the current modal and page.
   const historyUrl = new URL(window.location.href);
   historyUrl.searchParams.set('occurrences_task_id', taskId);
   const pageParam = url.searchParams.get('occurrences_task_page');
   if (pageParam) {
      historyUrl.searchParams.set('occurrences_task_page', pageParam);
   }
   window.history.replaceState({}, '', historyUrl);

   loader.classList.remove('d-none');
   content.innerHTML = '';

   fetch(url.toString(), {
      headers: {
         'X-Requested-With': 'XMLHttpRequest'
      }
   })
      .then(response => response.text())
      .then(html => {
         content.innerHTML = html;
      })
      .catch(() => {
         content.innerHTML = '<p class="text-danger mb-0">ციკლების ჩატვირთვა ვერ მოხერხდა.</p>';
      })
      .finally(() => {
         loader.classList.add('d-none');
      });
}

function cleanTaskPaginationLinks() {
   // Remove modal-related params from main task pagination links.
   getAll('.pagination a').forEach(function (link) {
      if (link.closest('.modal')) {
         return;
      }

      const url = new URL(link.href, window.location.origin);
      url.searchParams.delete('occurrences_task_id');
      for (const key of Array.from(url.searchParams.keys())) {
         if (key.startsWith('occurrences_page') || key.startsWith('occurrences_task_page')) {
            url.searchParams.delete(key);
         }
      }
      link.href = url.toString();
   });
}
	
