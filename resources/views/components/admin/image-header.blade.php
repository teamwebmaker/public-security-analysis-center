@php
    $imageUrl = asset("images/{$folder}/{$src}");
@endphp

<div class="card-header bg-transparent border-0 text-center position-relative p-0" style="min-height: {{ $height }};">
    <a href="{{ $imageUrl }}" data-fancybox data-caption="{{ $caption }}" class="d-block w-100 h-100">
        <img src="{{ $imageUrl }}" class="img-fluid rounded-top w-100 h-100 object-fit-cover"
            style="max-height: {{ $height }}; object-position: center;">
    </a>
</div>