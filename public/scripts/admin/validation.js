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
               alert('Invalid file type. Please upload a JPG, PNG, or WEBP image.');
               e.target.value = ''; // Clear invalid file
            }
         }
      });
   });
}