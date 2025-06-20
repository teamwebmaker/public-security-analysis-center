@props(['program'])
<div class="col-12 col-sm-8 col-md-6 mx-auto ">
    <div class="card shadow-sm">
        <!-- image -->
        <div class="p-0 overflow-hidden overlay-label-wrapper" data-label="სურათის ნახვა" data-alpha="0.7"
            style="--overlay-alpha: 0.7;">
            @if ($program->image)
                <x-admin.image-header :src="$program->image" folder="programs" :caption="'სურათი ' . $program->title->ka" />
            @else
                <x-admin.image-header src="not-found-image.webp" folder="programs" height="150px"
                    caption="სურათი ვერ მოიძებნა" />
            @endif
            <!-- Visibility Overlay -->
            @if($program->visibility == 0)
                <div class="position-absolute  top-0 start-0 w-100 h-100 bg-dark bg-opacity-75"></div>
            @endif
        </div>

        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between gap-2">
                <!-- title -->
                <div>
                    <h5 class="card-title text-truncate" title="{{ $program->title->ka }}">
                        {{ $program->title->ka }}
                    </h5>
                    <h6 class="card-subtitle mb-2 text-muted small">{{ $program->title->en }}</h6>
                </div>

                <!-- Small visibility indicator -->
                <div>
                    @if($program->visibility == 0)
                        <i class="bi bi-eye-slash fs-5 text-danger mt-1"></i>
                    @else
                        <i class="bi bi-eye fs-5 text-success mt-1"></i>
                    @endif
                </div>
            </div>

            <!-- Description -->
            <div x-data="{ expanded: false, height: 0 }" x-init="$nextTick(() => height = $refs.content.scrollHeight)">

                <!-- Description with smooth transition -->
                <div @click="expanded = !expanded"
                    class="overflow-hidden transition-all  duration-500 ease-in-out mb-1 "
                    :style="expanded ? 'max-height:' + height + 'px' : 'max-height: 4.5em;'">
                    <p class="card-text mb-0" x-ref="content" style="cursor: pointer;">
                        {{ $program->description->ka }}
                    </p>
                </div>

                <!-- Toggle button stays outside to avoid clipping -->
                @if (strlen($program->description->ka) > 200)
                    <button @click="expanded = !expanded" class="btn btn-sm btn-link p-0 text-decoration-none">
                        <i class="bi me-1" :class="expanded ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                        <span x-text="expanded ? 'ნაკლების ჩვენება' : 'მეტის ჩვენება'"></span>
                    </button>
                @endif
            </div>

            <x-admin.programs.details-list :program="$program" />

        </div>

        <!-- Action buttnos -->
        <div class="card-footer bg-transparent ">
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-outline-primary me-md-2" href="{{ route('programs.edit', ['program' => $program]) }}">
                    <i class="bi bi-pencil-square me-1"></i>Edit
                </a>
                <form method="POST" action="{{ route('programs.destroy', ['program' => $program]) }}"
                    onsubmit="return confirm('წავშალოთ კურსი?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger w-100">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>