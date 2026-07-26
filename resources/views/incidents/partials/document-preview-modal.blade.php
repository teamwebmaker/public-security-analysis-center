<div class="modal fade" id="incident-document-preview-modal" tabindex="-1"
    aria-labelledby="incident-document-preview-title" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h2 class="modal-title fs-5" id="incident-document-preview-title">დოკუმენტის ნახვა</h2>
                    <div class="small text-muted" data-document-file-name></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="დახურვა"></button>
            </div>

            <div class="modal-body p-0">
                <div data-document-loading class="d-flex align-items-center justify-content-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">იტვირთება...</span>
                    </div>
                </div>

                <iframe data-document-frame class="d-none w-100 border-0"
                    style="height: min(72vh, 850px);" title="ინციდენტის დოკუმენტი"></iframe>

                <div data-document-fallback class="d-none text-center p-5">
                    <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-inline-flex p-4 mb-3">
                        <i class="bi bi-file-earmark-richtext fs-1"></i>
                    </div>
                    <h3 class="h5">ამ ფორმატის პირდაპირი ნახვა ბრაუზერში შეუძლებელია</h3>
                    <p class="text-muted mb-0">
                        დოკუმენტის გასახსნელად გამოიყენეთ ჩამოტვირთვის ღილაკი.
                    </p>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">დახურვა</button>
                <a href="#" data-document-download class="btn btn-primary">
                    <i class="bi bi-download me-1"></i>
                    ჩამოტვირთვა
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('incident-document-preview-modal');
        if (!modal) {
            return;
        }

        const frame = modal.querySelector('[data-document-frame]');
        const fallback = modal.querySelector('[data-document-fallback]');
        const loading = modal.querySelector('[data-document-loading]');
        const download = modal.querySelector('[data-document-download]');
        const fileName = modal.querySelector('[data-document-file-name]');

        modal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            const canPreview = trigger?.dataset.canPreview === '1';

            fileName.textContent = trigger?.dataset.documentTitle || '';
            download.href = trigger?.dataset.downloadUrl || '#';
            frame.classList.add('d-none');
            fallback.classList.add('d-none');
            loading.classList.toggle('d-none', !canPreview);

            if (!canPreview) {
                frame.removeAttribute('src');
                fallback.classList.remove('d-none');
                return;
            }

            frame.src = trigger.dataset.previewUrl;
        });

        frame.addEventListener('load', function () {
            loading.classList.add('d-none');
            frame.classList.remove('d-none');
        });

        modal.addEventListener('hidden.bs.modal', function () {
            frame.removeAttribute('src');
            frame.classList.add('d-none');
            fallback.classList.add('d-none');
            loading.classList.remove('d-none');
        });
    });
</script>
