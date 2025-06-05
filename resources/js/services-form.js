document.addEventListener('DOMContentLoaded', function () {
   console.log('services-form.js loaded');

   const form = document.getElementById("serviceForm");
   if (!form) return;

   form.addEventListener("submit", function (e) {
      e.preventDefault();

      const name = document.getElementById("name").value;
      const company = document.getElementById("company").value;
      const contact = document.getElementById("contact").value;
      const message = document.getElementById("message").value;

      const services = Array.from(document.querySelectorAll("input[name='services[]']:checked"))
         .map(cb => cb.value)
         .join(", ");

      if (!services) {
         alert("Please select at least one service.");
         return;
      }

      const subject = encodeURIComponent("psac.ge | Service Request");
      const body = encodeURIComponent(
         `Name: ${name}\nCompany: ${company}\nContact: ${contact}\nServices: ${services}\n\nMessage:\n${message}`
      );

      const mailtoLink = `https://mail.google.com/mail/?view=cm&fs=1&to=psacge@gmail.com&su=${subject}&body=${body}`;
      window.open(mailtoLink, '_blank');
   });

   const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
   tooltipTriggerList.forEach(function (tooltipTriggerEl) {
      new bootstrap.Tooltip(tooltipTriggerEl);
   });
});
