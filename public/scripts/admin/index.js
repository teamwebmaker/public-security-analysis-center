import '../bootstrap/bootstrapValidation.js' // Bootstrap form validation
import {
   imageValidation,
   pdfValidation
} from './validation.js';
import {
   loadFancyboxCDN
} from "../heplers.js";


document.addEventListener('DOMContentLoaded', function() {
   imageValidation(); // Image upload validation
   pdfValidation(); // PDF upload validation
});


// Initialize Fancybox
document.addEventListener("DOMContentLoaded", function() {
   // Only bind Fancybox if [data-fancybox] exists
   if (document.querySelector('[data-fancybox]')) {
      loadFancyboxCDN(() => {
         Fancybox.bind('[data-fancybox]', {
 
            PdfViewer: {
               enable: true
            }
         });
      });
   }
});