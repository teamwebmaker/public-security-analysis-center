@php
    $accordionItems = config('sidebar.admin');

@endphp
<div class="accordion" id="dashboard">
    @foreach ($accordionItems as $item)

        @php
            // Below we do two things:
            //   1. Conditionally add the `.edit` route as active if a corresponding `.create` route exists.
            //   2. Check if any of the parent's child routes match the current route.

            $isParentActive = false;

            // Flatten all routes from all children
            $allChildRoutes = collect($item['children'] ?? [])
                ->flatMap(function ($child) {
                    $routes = collect($child['routes'] ?? [])->pluck('name');

                    // Only add .edit if .create exists
                    $editRoutes = $routes
                        ->filter(fn($name) => str_ends_with($name, '.create'))
                        ->map(fn($name) => str_replace('.create', '.edit', $name));

                    return $routes->merge($editRoutes);
                })
                ->unique()
                ->values()
                ->toArray();

            $parentRoutes = collect($item['routes'] ?? []);
            $parentRouteNames = $parentRoutes->pluck('name');
            $parentEditRoutes = $parentRouteNames
                ->filter(fn($name) => str_ends_with($name, '.create'))
                ->map(fn($name) => str_replace('.create', '.edit', $name));

            $allParentRoutes = $parentRouteNames
                ->merge($parentEditRoutes)
                ->unique()
                ->values()
                ->toArray();

            // Check if any of those match the current route
            $isParentActive = in_array(Route::currentRouteName(), array_merge($allChildRoutes, $allParentRoutes));
          @endphp

        <x-accordion-item :id="$item['id']" :label="$item['label']" :icon="$item['icon']" :parent="$item['parent']"
            :open="$isParentActive">
            @if (!empty($item['routes']))
                <div class="list-group list-group-flush mb-2">
                    @foreach ($item['routes'] as $route)
                        <a href="{{ route($route['name']) }}"
                            class="list-group-item list-group-item-action nav-link @if(Route::currentRouteName() === $route['name']) active @endif">
                            <i class="bi {{ $route['icon'] }}"></i>
                            {{ $route['label'] }}
                        </a>
                    @endforeach
                </div>
            @endif
            @forelse ($item['children'] ?? [] as $child)
                @php
                    $routes = collect($child['routes'] ?? []);
                    $routeNames = $routes->pluck('name');

                    $editRoutes = $routeNames
                        ->filter(fn($name) => str_ends_with($name, '.create'))
                        ->map(fn($name) => str_replace('.create', '.edit', $name));

                    $activeRoutes = $routeNames
                        ->merge($editRoutes)
                        ->unique()
                        ->values()
                        ->toArray();
                  @endphp

                <x-accordion-nav-item :id="$child['id']" :icon="$child['icon']" :label="$child['label']"
                    :routes="$child['routes']" parent="#inner-accordion" :active-routes="$activeRoutes" />
            @empty
                <!-- No children -->
            @endforelse
        </x-accordion-item>
    @endforeach

</div>

{{--
=== example of what we are sending for rendering :
<x-accordion-nav-item id="publications" icon="bi-book" label="პუბლიკაციები"
    :active-routes="['publications.index', 'publications.create','publications.edit']" :routes="[
        ['name' => 'publications.index', 'label' => 'პუბლიკაციების ნახვა', 'icon' => 'bi-list-ul'],
        ['name' => 'publications.create', 'label' => 'პუბლიკაციის შექმნა', 'icon' => 'bi-plus-circle'],
    ]" /> --}}