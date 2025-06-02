@props([
    'title' => null,
    'description' => null,
    'image' => null,
    'link' => null,
    'date' => null,
])

<div class="card card-article">
    {{-- Optional image --}}
    @if($image)
        <div class="card-header p-0">
            <img src="{{ asset($image) }}" class="card-img-top response-img" alt="Image">
        </div>
    @endif

    <div class="card-body">
        {{-- Optional title --}}
        @if($title)
            <h3 class="card-title bold fs-5 mb-2">{{ $title }}</h3>
        @endif

        {{-- Optional description --}}
        @if($description)
            <p class="card-text fs-6 service-desc">
                {{ Str::limit($description, 250) }}
            </p>
        @endif
    </div>

    {{-- Optional footer with date and link --}}
    @if($date || $link)
        <div class="p-4 d-flex  justify-content-between gap-sm-0">
            <!-- First column (always present) -->
            <div>
                @if($date)
                    <p class="card-text mb-0">
                        <small class="text-body-secondary">{{ $date }}</small>
                    </p>
                @endif
            </div>

            <!-- Second column (always present) -->
            <div>
                @if($link)
                    <a href="{{ $link }}" class="btn view-more">
                        გაიგე მეტი <span aria-hidden="true">→</span>
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>