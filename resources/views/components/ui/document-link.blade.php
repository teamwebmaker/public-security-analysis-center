@props([
    'file' => null,            // file name (e.g., "example.pdf")
    'path' => null,            // base path (e.g., "documents/services")
    'label' => 'დოკუმენტი',   // optional label text
])

@if ($file)
    @php
        $documentPath = asset(trim($path, '/') . '/' . $file);
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        $icons = [
            'pdf' => 'bi-file-earmark-pdf text-danger',
            'doc' => 'bi-file-earmark-word text-primary',
            'docx' => 'bi-file-earmark-word text-primary',
            'xls' => 'bi-file-earmark-excel text-success',
            'xlsx' => 'bi-file-earmark-excel text-success',
        ];

        $iconClass = $icons[$extension] ?? 'bi-file-earmark text-secondary';
    @endphp

    @if ($extension === 'pdf')
        {{-- Open PDF in Fancybox viewer --}}
        <a href="{{ $documentPath }}" data-fancybox data-type="pdf"
           class="btn btn-sm btn-outline-success w-100">
            <i class="bi {{ $iconClass }} me-2"></i> {{ $label }} (PDF)
        </a>
    @else
        {{-- For Word/Excel, offer download --}}
        <a href="{{ $documentPath }}" class="btn btn-sm btn-outline-primary w-100"
           target="_blank" download>
            <i class="bi {{ $iconClass }} me-2"></i> {{ strtoupper($extension) }} ფაილი ჩამოტვირთვა
        </a>
    @endif
@endif
