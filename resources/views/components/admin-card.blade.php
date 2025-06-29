<?php
$visibility = $document->visibility == 0
?>
<div class="{{ $containerClass }}">
    <div class="card h-100 border-0 bg-white overflow-hidden position-relative">

        <!-- Card Image -->
        <div class="card-header p-0 border-0 position-relative">
            @if (isset($image))
                <x-admin.image-header :src="$image" :folder="$resourceName" :caption="'სურათი ' . $title" />

                <div class="position-absolute top-0 start-0 m-2 shadow-sm bg-white rounded-pill">
                    <span class="d-flex align-items-center gap-1 text-muted badge">
                        <i class="bi bi-pencil"></i>
                        {{ $document->updated_at->diffForHumans() }}
                    </span>
                </div>
            @endif


            <!-- Visibility Overlay -->
            @if ($visibility)
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75"></div>
            @endif
        </div>

        <!-- Card Body -->
        <div class="card-body d-flex flex-column bg-white">
            <div class="d-flex align-items-start gap-2">
                <!-- title -->
                <h3 class="card-title h5  text-truncate flex-grow-1">
                    {{ $title }}
                </h3>

                <!-- Small visibility indicator -->
                <div>
                    @if ($visibility)
                        <i class="bi bi-eye-slash fs-5 text-danger mt-1"></i>
                    @else
                        <i class="bi bi-eye fs-5 text-success mt-1"></i>
                    @endif
                </div>
            </div>
            <!-- Created Date -->
            <div class="d-flex gap-3  mb-3">
                <small class="text-muted d-flex align-items-center gap-1">
                    <i class="bi bi-calendar"></i>
                    {{ $document->created_at->format('Y-m-d') }}
                </small>
                @if (!isset($image))
                    <small class="text-muted d-flex align-items-center gap-1">
                        <i class="bi bi-pencil"></i>
                        {{ $document->updated_at->diffForHumans() }}
                    </small>
                @endif
            </div>


            <!-- description -->
            @if ($document->description)
                    <p class="card-text text-muted line-clamp mb-4" style="--bs-line-clamp: 3;">
                        {{ is_object($document->description)
                ? ($document->description->ka ?? ($document->description->en ?? ''))
                : $document->description }}
                    </p>
            @endif

            <!-- Card Details slot -->
            <div class="mt-auto">
                {{ $cardDetails ?? '' }}
            </div>
        </div>



        <!-- Card Footer -->
        <div class="card-footer border-0 pt-0 pb-3 px-4 bg-white">
            <div class="d-flex justify-content-between gap-2">
                <!-- Edit Button -->
                <a href="{{ route($resourceName . '.edit', $document) }}"
                    class="btn btn-outline-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-pencil-square"></i>
                    <span>Edit</span>
                </a>

                <!-- Delete Button -->
                <form method="POST" action="{{ route($resourceName . '.destroy', $document) }}"
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