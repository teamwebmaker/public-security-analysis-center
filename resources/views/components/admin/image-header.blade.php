@php
    $imageUrl = asset("images/{$folder}/{$src}");
@endphp

<div class="card-header bg-transparent border-0 text-center position-relative p-0" style="min-height: {{ $height }};">

    <a href="{{ $imageUrl }}" data-fancybox data-caption="{{ $caption }}" class="d-block w-100 h-100 position-relative">

        <!-- Image -->
        <img src="{{ $imageUrl }}" alt="{{ implode('-', explode(' ', $caption)) }}"
            class="img-fluid rounded-top w-100 h-100 object-fit-cover"
            style="max-height: {{ $height }}; object-position: center;">

        <!-- Always-visible icon in top-right -->
        <div class="position-absolute top-0 end-0 ms-2 mt-2 d-flex align-items-center justify-content-center shadow-sm bg-white rounded-start-1"
            style="width: 30px; height: 22px; ">
            <i class="bi bi-aspect-ratio text-secondary fs-4"></i>
        </div>
    </a>
</div>