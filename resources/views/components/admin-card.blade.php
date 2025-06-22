<?php
$title = is_object($document->title) ? $document->title->ka : $document->title;
$visibility = $document->visibility == 0
?>
<div class="{{ $containerClass }}">
    <div class="card h-100 border-0 shadow-sm overflow-hidden position-relative">
        <!-- Card Image -->
        <div class="card-header p-0 border-0  position-relative">
            <x-admin.image-header :src="$image" :folder='$resourceName' :caption="'სურათი ' . $title" />
            <!-- Visibility Overlay -->
            @if($visibility)
                <div class="position-absolute  top-0 start-0 w-100 h-100 bg-dark bg-opacity-75"></div>
            @endif
        </div>

        <!-- Card Body -->
        <div class="card h-100 d-flex flex-column">
            <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-start gap-2">
                    <!-- title -->
                    <h3 class="card-title h5 mb-3 text-truncate flex-grow-1">
                        {{ $title }}
                    </h3>

                    <!-- Small visibility indicator -->
                    <div>
                        @if($visibility)
                            <i class="bi bi-eye-slash fs-5 text-danger mt-1"></i>
                        @else
                            <i class="bi bi-eye fs-5 text-success mt-1"></i>
                        @endif
                    </div>
                </div>

                @if ($document->description)
                    <div>
                        <p class="card-text text-muted line-clamp mb-4" style="--bs-line-clamp: 3;">
                            {{ $document->description->ka ?? $description }}
                        </p>
                    </div>
                @endif

                <!-- Push this to bottom using mt-auto -->
                <div class="mt-auto">
                    {{ $cardDetails ?? '' }}
                </div>
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer bg-transparent border-0 pt-0 pb-3 px-4">
            <div class="d-flex justify-content-between gap-2">
                <a href="{{ route($resourceName . '.edit', $document) }}"
                    class="btn btn-outline-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit</span>
                </a>
                <form method="POST" action="{{route($resourceName . '.destroy', $document) }}"
                    onsubmit="return confirm('ნამდვილად გსურთ დოკუმენტი {{ $title }} წაიშალოს?')" class="flex-grow-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-trash"></i>
                        <span>Delete</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>