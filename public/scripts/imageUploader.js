// Handle image preview and removal on file input change
class ImageUploader {
   constructor(inputId, previewId, removeBtnClass = 'btn-remove-image') {
      this.input = document.getElementById(inputId);
      this.preview = document.getElementById(previewId);
      this.removeBtnClass = removeBtnClass;

      if (this.input && this.preview) {
         this.init();
      }
   }

   init() {
      // Initialize Fancybox
      Fancybox.bind('[data-fancybox]');

      // Create remove button
      this.removeBtn = document.createElement('button');
      this.removeBtn.type = 'button';
      this.removeBtn.className = `${this.removeBtnClass} btn btn-sm btn-danger position-absolute`;
      this.removeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';
      this.removeBtn.style.top = '-5px';
      this.removeBtn.style.right = '0px';
      this.removeBtn.addEventListener('click', () => this.removeImage());

      // Wrap preview in relative positioned container
      const previewContainer = this.preview.parentElement;
      previewContainer.classList.add('position-relative');
      previewContainer.appendChild(this.removeBtn);

      // Hide remove button initially
      this.removeBtn.classList.add('d-none');

      // Handle file changes
      this.input.addEventListener('change', (e) => this.handleFileChange(e));
   }

   handleFileChange(e) {
      if (this.input.files && this.input.files[0]) {
         const reader = new FileReader();
         reader.onload = (e) => {
            this.preview.src = e.target.result;
            this.preview.classList.remove('d-none');
            this.removeBtn.classList.remove('d-none');
         }
         reader.readAsDataURL(this.input.files[0]);
      }
   }

   removeImage() {
      this.preview.src = '#';
      this.preview.classList.add('d-none');
      this.removeBtn.classList.add('d-none');
      this.input.value = '';
   }
}

export default ImageUploader;