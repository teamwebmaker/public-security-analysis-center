@php
    $extension = strtolower(pathinfo($incident->document_original_name, PATHINFO_EXTENSION));
    $canPreviewInline = $incident->document_mime_type === 'application/pdf' || $extension === 'pdf';
@endphp

<button type="button" class="{{ $buttonClass ?? 'btn btn-sm btn-outline-secondary' }}"
    data-bs-toggle="modal" data-bs-target="#incident-document-preview-modal"
    data-document-title="{{ $incident->document_original_name }}"
    data-preview-url="{{ route($previewRoute, $incident) }}"
    data-download-url="{{ route($downloadRoute, $incident) }}"
    data-can-preview="{{ $canPreviewInline ? '1' : '0' }}">
    <i class="bi bi-eye me-1"></i>
    {{ $label ?? 'ნახვა' }}
</button>
