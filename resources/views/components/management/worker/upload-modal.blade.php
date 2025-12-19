<x-modal id="uploadDocumentModal_{{ $task->id }}" title="სამუშაოს დასრულების პროცესი" size="md" height="30dvh">
    <div class="p-4" style="height: 100%; overflow-y: auto;">
        <p class="text-muted mb-4">
            იმისათვის რომ სამუშაო მოინიშნოს დასრულებულად, გთხოვთ ატვირთოთ დოკუმენტი და დაჭიროთ დასრულებას.
        </p>

        <form action="{{ route('management.tasks.upload-document', $task) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <x-form.input type="file" name="document" id="document_{{ $task->id }}" label="აირჩიეთ დოკუმენტი"
                    :isImage="false" accept=".pdf, .doc, .docx, .xls, .xlsx"
                    infoMessage="მხარდაჭერილი ფორმატები: .pdf, .doc, .docx, .xls, .xlsx" />
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">დახურვა</button>
                <button type="submit" class="btn btn-primary">დასრულება</button>
            </div>
        </form>
    </div>
</x-modal>