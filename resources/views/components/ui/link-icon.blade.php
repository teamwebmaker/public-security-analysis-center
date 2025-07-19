@props(
    [
        'route' => null,
        'size' => '1.4rem',
        'background' => '#d3d8de',
        'padding' => '5px 7px',
        'icon' => 'question-lg'
    ]
)

<div class="rounded-circle d-inline-flex align-items-center justify-content-center"
    style="padding: {{ $padding }}; background-color: {{ $background }};">
        <a href="{{ $route ? route($route) : '#' }}">
            <i class="bi bi-{{ $icon }} text-dark" style="font-size: {{ $size }}"></i>
        </a>
    </a>
</div>