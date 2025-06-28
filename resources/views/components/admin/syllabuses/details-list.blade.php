@props(['syllabus', 'resourceName'])

<ul class="list-group list-group-flush pt-2">
    @isset($syllabus->program)
        @if ($syllabus->program?->id)
            <!-- program ID -->
            <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
                <span>პროგრამის ID:</span>
                <span class="badge bg-secondary">{{ $syllabus->program->id }}</span>
            </li>
        @endif
        @if ($syllabus->program?->title?->ka)
            <!-- program title -->
            <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
                <span>პროგრამა:</span>
                <span class="badge bg-secondary">{{ $syllabus->program->title->ka }}</span>
            </li>
        @endif
    @endisset

    @if ($syllabus->pdf)
        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center pt-3">
            <a href="{{ url('documents/' . $resourceName . '/' . $syllabus->pdf) }}" data-fancybox data-type="pdf"
                class="btn btn-sm btn-outline-success w-100">
                <i class="bi bi-file-earmark-pdf me-2"></i>Pdf დოკუმენტი
            </a>
        </li>
    @endif
</ul>