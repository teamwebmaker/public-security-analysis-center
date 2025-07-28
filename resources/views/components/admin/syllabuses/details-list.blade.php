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

                <label class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
                    data-bs-custom-class="custom-tooltip" data-bs-title="{{ $syllabus->program->title->ka }}"
                    style="max-width: 150px; cursor: pointer;">
                    <span>{{ $syllabus->program->title->ka }}</span>
                </label>
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