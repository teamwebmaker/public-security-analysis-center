import { loadFancyboxCDN } from "../heplers.js";

document.addEventListener("DOMContentLoaded", function () {
   // Only bind Fancybox if [data-fancybox] exists
   if (document.querySelector('[data-fancybox]')) {
      loadFancyboxCDN(() => {
         Fancybox.bind('[data-fancybox]');
      });
   }
});
