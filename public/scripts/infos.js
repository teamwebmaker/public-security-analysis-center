import ImageUploader from "./imageUploader.js";

document.addEventListener("DOMContentLoaded", function () {
   // Initialize all image uploaders on page
   new ImageUploader('info-image', 'info-image-preview');
});