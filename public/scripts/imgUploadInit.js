import ImageUploader from "./imageUploader.js";

document.addEventListener("DOMContentLoaded", function () {
   // Initialize all image uploaders on page and use image preview functionality
   new ImageUploader('image', 'image-preview');
});