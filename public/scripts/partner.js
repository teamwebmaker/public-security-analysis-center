import ImageUploader from "./imageUploader.js";

document.addEventListener("DOMContentLoaded", function () {
   // Initialize all image uploaders on page
   new ImageUploader('partner-image', 'partner-image-preview');
});