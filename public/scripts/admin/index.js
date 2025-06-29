import '../bootstrap/bootstrapValidation.js' // Bootstrap form validation
import { imageValidation, pdfValidation } from './validation.js';
import { getAll, loadFancyboxCDN } from "../heplers.js";


document.addEventListener('DOMContentLoaded', function() {
    imageValidation(); // Image upload validation
    pdfValidation(); // PDF upload validation
    initUnsavedChangesWarning(); //  Unsaved changes warning

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

// This is used in form-container.blade.php and it is used to detect unsaved changes
function initUnsavedChangesWarning() {

    const forms = getAll('.dirty-check-form');

    forms.forEach(form => {
        let isDirty = false;

        // All text-like inputs: text, number, date, time, url, etc. + textarea
        form.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]):not([type="file"]) , textarea').forEach(input => {
            input.addEventListener('input', () => {
                isDirty = true;
            });
        });

        // Checkboxes, radios, selects, file inputs: use change
        form.querySelectorAll('select, input[type="checkbox"], input[type="radio"], input[type="file"]').forEach(input => {
            input.addEventListener('change', () => {
                isDirty = true;
            });
        });

        form.addEventListener('submit', () => {
            isDirty = false;
        });

        window.addEventListener('beforeunload', function(e) {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            }
        });
    });
}