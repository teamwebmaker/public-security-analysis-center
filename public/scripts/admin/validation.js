// Image upload validation
export function imageValidation() {
   // Select all file inputs with specific accept attribute
   const imageInputs = document.querySelectorAll('input[type="file"][accept="image/jpeg,image/png,image/webp"]');

   imageInputs.forEach(input => {
      input.addEventListener('change', function (e) {
         const file = e.target.files[0];
         if (file) {
            const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
               alert('ფაილის ტიპი არასწორია. გთხოვთ, ატვირთოთ JPG, PNG ან WEBP სურათი.');
               e.target.value = ''; // Clear invalid file
            }
         }
      });
   });
}

// PDF upload validation
export function pdfValidation() {
   // Select all file inputs accepting PDF files
   const pdfInputs = document.querySelectorAll('input[type="file"][accept="application/pdf"]');

   pdfInputs.forEach(input => {
      input.addEventListener('change', function (e) {
         const file = e.target.files[0];
         if (file) {
            const validType = 'application/pdf';
            if (file.type !== validType) {
               alert('ფაილის ტიპი არასწორია. გთხოვთ, ატვირთოთ PDF დოკუმენტი.');
               e.target.value = ''; // Clear invalid file
            }
         }
      });
   });
}
