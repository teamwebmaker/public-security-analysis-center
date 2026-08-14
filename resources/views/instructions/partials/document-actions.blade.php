@php
    $extension = strtolower(pathinfo(
        $instruction->document_original_name ?: $instruction->document,
        PATHINFO_EXTENSION
    ));
    $canPreviewInline = $instruction->document_mime_type === 'application/pdf' || $extension === 'pdf';
@endphp

<button type="button" class="btn btn-sm btn-outline-success w-100"
    data-bs-toggle="modal" data-bs-target="#incident-document-preview-modal"
    data-document-title="{{ $instruction->document_original_name ?: basename($instruction->document) }}"
    data-preview-url="{{ route($previewRoute, $instruction) }}"
    data-download-url="{{ route($downloadRoute, $instruction) }}"
    data-can-preview="{{ $canPreviewInline ? '1' : '0' }}">
    <i class="bi bi-eye me-1"></i>
    დოკუმენტის ნახვა
</button>
