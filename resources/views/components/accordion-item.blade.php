@php
    $routeName = request()->route()->getName();
    $isOpen = in_array($routeName, $activeRoutes);
@endphp

<div class="accordion-item">
    <h2 class="accordion-header">
        <button class="accordion-button d-flex gap-2" type="button" data-bs-toggle="collapse"
            data-bs-target="#{{ $id }}" aria-expanded="{{ $isOpen ? 'true' : 'false' }}" aria-controls="{{ $id }}">
            <i class="bi {{ $icon }}"></i>
            <span class="btn-label">{{ $label }}</span>
        </button>
    </h2>
    <div id="{{ $id }}" class="accordion-collapse collapse {{ $isOpen ? 'show' : '' }}" data-bs-parent="#dashboard">
        <div class="accordion-body">
            <div class="list-group list-group-flush">
                @foreach ($routes as $route)
                    <a href="{{ route($route['name']) }}"
                        class="list-group-item list-group-item-action nav-link @if($routeName === $route['name']) active @endif">
                        <i class="bi {{ $route['icon'] }}"></i>
                        {{ $route['label'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>