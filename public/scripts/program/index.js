import { getById } from "../helpers.js";
import ImageUploader from "../imageUploader.js";
document.addEventListener('DOMContentLoaded', function () {

   // Initialize all image uploaders on page
   new ImageUploader('program-image', 'program-image-preview');
   new ImageUploader('certificate-image', 'certificate-image-preview');

   // Form validation
   const form = getById('program-form');
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