<?php
$visibility = $document->visibility == 0;
$descriptionText = is_object($document->description)
    ? $document->description->ka ?? ($document->description->en ?? "")
    : $document->description;
$shouldTruncate = strlen(strip_tags($descriptionText)) > 180;

?>

<div class="{{ $containerClass }}">
    <div class="{{ $cardClass }}">

        <!-- Card Image -->
        <div class="card-header p-0 border-0 position-relative" style="isolation: isolate;">
            @if (isset($image))
                <x-admin.image-header :src="$image" :folder="$resourceName" :caption="'სურათი ' . $title" />

                <!-- Updated Date Overlay -->
                <div class="position-absolute top-0 start-0 m-2 shadow-sm bg-white rounded-pill" style="z-index: 7777;">
                    <span class="d-flex align-items-center gap-1 text-muted badge">
                        <i class="bi bi-pencil"></i>
                        {{ $document->updated_at->diffForHumans() }}
                    </span>
                </div>
            @endif

            <!-- Visibility Overlay -->
            @if ($hasVisibility)
                <div>
                    @if ($visibility)
                        @if (!isset($image))
                            <div style="height: 35px;"> </div>
                        @endif
                        <div
                            class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-75 d-flex align-items-center justify-content-center">
                            <i class="bi bi-eye-slash fs-2 text-danger mt-1"></i>
                        </div>
                    @endif
                </div>
            @endif

        </div>

        <!-- Card Body -->
        <div class="card-body d-flex flex-column bg-transparent">
            <div class="d-flex align-items-start gap-2">
                <!-- title -->
                <h3 class="card-title h5 mb-0 text-wrap">
                    {{ $title }}
                </h3>
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
            @if ($descriptionText)
                <div @if($shouldTruncate) x-data="{ expanded: false }" @endif class="mb-4">
                    <p class="card-text text-muted mb-0"
                    :class="@if($shouldTruncate)!expanded ? 'line-clamp' : ''@endif"
                    style="@if($shouldTruncate)--bs-line-clamp: 3;@endif">
                        {{ $descriptionText }}
                    </p>

                    @if ($shouldTruncate)
                        <button @click="expanded = !expanded" class="btn btn-link p-0 d-inline-flex align-items-center gap-1" type="button">
                            <span x-show="!expanded">See more <i class="bi bi-chevron-down"></i></span>
                            <span x-show="expanded">See less <i class="bi bi-chevron-up"></i></span>
                        </button>
                    @endif
                </div>
            @endif

            <!-- Card Details slot -->
            <div class="mt-auto">
                {{ $cardDetails ?? '' }}
            </div>
        </div>

        <!-- Card Footer -->
        <div class="card-footer border-0 pt-0 pb-3 px-4 bg-transparent">
            <div class="d-flex justify-content-between gap-2">
                {{ $cardFooter ?? '' }}
                <!-- Edit Button -->
                @if ($hasEdit)
                    <a href="{{ route($resourceName . '.edit', $document) }}"
                        class="btn btn-outline-primary btn-sm flex-grow-1 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit</span>
                    </a>
                @endif

                @if ($hasDelete)
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
                @endif
            </div>
        </div>
    </div>
</div>