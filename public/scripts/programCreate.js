document.addEventListener('DOMContentLoaded', function () {
   // Initialize Fancybox
   Fancybox.bind('[data-fancybox]');

   // Preserve active tab after validation error
   const activeTabId = sessionStorage.getItem('activeTab');
   if (activeTabId) {
      const trigger = document.querySelector(`[id="${activeTabId}"]`);
      if (trigger) {
         new bootstrap.Tab(trigger).show();
      }
   }

   document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab => {
      tab.addEventListener('shown.bs.tab', function (event) {
         sessionStorage.setItem('activeTab', event.target.id);
      });
   });

   // Form validation
   const form = document.getElementById('program-form');
   if (form) {
      form.addEventListener('submit', function (e) {
         const daysChecked = document.querySelectorAll('input[name="days[]"]:checked').length > 0;
         if (!daysChecked) {
            e.preventDefault();
            alert('გთხოვთ აირჩიოთ მინიმუმ ერთი დღე');
         }
      });
   }
});