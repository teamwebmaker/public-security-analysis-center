import { enableSmoothScroll, getOne, getElementById, getAll } from './heplers.js';

// Dom elements
const scrollLink = getOne('scroll-link') // scroll related
const form = getElementById('serviceForm'); // form related


// Smooth scroll 
if (scrollLink) {
   scrollLink.addEventListener('click', function (e) {
      e.preventDefault();
      enableSmoothScroll(scrollLink)
   });
}


// For Services form
document.addEventListener("DOMContentLoaded", function () {

   if (!form) return
   form.addEventListener("submit", function (e) {
      e.preventDefault();

      const { name, company, contact, message } = form

      const services = Array.from(getAll("input[name='services[]']:checked"))
         .map(cb => cb.value)
         .join(", ");

      if (!services) {
         alert("Please select at least one service.");
         return;
      }

      const subject = encodeURIComponent("psac.ge | Service Request");
      const body = encodeURIComponent(
         `Name: ${name.value}\nCompany: ${company.value}\nContact: ${contact.value}\nServices: ${services}\n\nMessage:\n${message.value}`
      );

      const isMobile = /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|Mobile/i.test(navigator.userAgent);

      if (isMobile) {
         window.location.href = `mailto:psacge@gmail.com?subject=${subject}&body=${body}`;
      } else {
         window.open(`https://mail.google.com/mail/?view=cm&fs=1&to=psacge@gmail.com&su=${subject}&body=${body}`, '_blank');
      }
   });
});
