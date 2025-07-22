@props(['items'])

<ul class="nav nav-pills flex-column mb-auto d-flex gap-2 px-2">
    @foreach($items as $item)
        @php
            // Check if the route is the current route
            $isActive = $item['route'] === Route::currentRouteName();
            // Handle the case where the route is '#'
            $url = $item['route'] === '#' ? '#' : route($item['route']);
        @endphp
        <li>
            <a href="{{ $url }}" class="nav-link p-2 {{ $isActive ? 'active' : 'link-body-emphasis' }}">
                @isset($item['icon'])
                    <i class="{{ $item['icon'] }} fs-5 me-2"></i>
                @endisset
                {{ $item['label'] }}
            </a>
        </li>
    @endforeach
</ul>