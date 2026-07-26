@props([
   'action' => request()->url(),
   'method' => 'GET',
   'filters' => [], // ['key' => ['label' => '', 'options' => []]]
   'resetUrl' => request()->url(),
   'showBadges' => true,
   'preserve' => [],
])

@php
   $componentId = 'filter-bar-' . uniqid();
   $requestFilters = request()->query('filter', []);
   $requestFilters = is_array($requestFilters) ? $requestFilters : [];

   $activeFilters = collect($requestFilters)
      ->filter(fn($value, $key) =>
         array_key_exists($key, $filters)
         && is_scalar($value)
         && $value !== ''
      )
      ->map(fn($value, $key) => [
         'key' => (string) $key,
         'value' => (string) $value,
      ])
      ->values()
      ->all();

   // Keep search, sorting, includes, and explicitly preserved parameters while
   // replacing only the filters managed by this component.
   $preservedQuery = request()->query();
   unset($preservedQuery['page']);

   foreach (array_keys($filters) as $filterKey) {
      unset($preservedQuery['filter'][$filterKey]);
   }

   if (empty($preservedQuery['filter'])) {
      unset($preservedQuery['filter']);
   }

   foreach ($preserve as $key => $value) {
      $preservedQuery[$key] = $value;
   }

   $hiddenInputs = [];
   $flattenQuery = function (array $values, string $prefix = '') use (&$flattenQuery, &$hiddenInputs): void {
      foreach ($values as $key => $value) {
         $name = $prefix === '' ? (string) $key : "{$prefix}[{$key}]";

         if (is_array($value)) {
            $flattenQuery($value, $name);
            continue;
         }

         if (is_scalar($value) && $value !== '') {
            $hiddenInputs[] = ['name' => $name, 'value' => (string) $value];
         }
      }
   };
   $flattenQuery($preservedQuery);

   $urlWithQuery = function (array $query) use ($resetUrl): string {
      if (empty($query)) {
         return $resetUrl;
      }

      return $resetUrl
         . (str_contains($resetUrl, '?') ? '&' : '?')
         . http_build_query($query);
   };

   $clearUrl = $urlWithQuery($preservedQuery);
@endphp

<div id="{{ $componentId }}" class="filter-bar"
   data-filter-bar
   data-filters='@json($filters)'
   data-active-filters='@json($activeFilters)'>
   <div class="py-3">
      <form method="{{ $method }}" action="{{ $action }}">
         @foreach($hiddenInputs as $input)
            <input type="hidden" name="{{ $input['name'] }}" value="{{ $input['value'] }}">
         @endforeach

         <div data-filter-rows class="d-flex flex-column gap-2"></div>

         <div class="d-flex flex-wrap gap-2 mt-3">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-add-filter>
               <i class="bi bi-plus-circle me-1"></i>
               ფილტრის დამატება
            </button>
            <button type="submit" class="btn btn-sm btn-primary">
               <i class="bi bi-funnel me-1"></i>
               ფილტრების გამოყენება
            </button>
            @if(!empty($activeFilters))
               <a href="{{ $clearUrl }}" class="btn btn-sm btn-outline-danger">
                  <i class="bi bi-x-circle me-1"></i>
                  ყველა ფილტრის გასუფთავება
               </a>
            @endif
         </div>
      </form>

      @if($showBadges)
         <div class="d-flex flex-wrap gap-2 small mt-3" aria-live="polite" aria-atomic="true">
            @forelse($activeFilters as $activeFilter)
               @php
                  $filterKey = $activeFilter['key'];
                  $filterValue = $activeFilter['value'];
                  $removeQuery = request()->query();
                  unset($removeQuery['page'], $removeQuery['filter'][$filterKey]);
                  if (empty($removeQuery['filter'])) {
                     unset($removeQuery['filter']);
                  }
                  $removeUrl = $urlWithQuery($removeQuery);
               @endphp
               <span class="badge rounded-pill text-bg-light border text-dark d-inline-flex align-items-center gap-2 py-2 px-3">
                  <span>
                     {{ $filters[$filterKey]['label'] ?? $filterKey }}:
                     <strong>{{ $filters[$filterKey]['options'][$filterValue] ?? $filterValue }}</strong>
                  </span>
                  <a href="{{ $removeUrl }}" class="text-danger text-decoration-none"
                     aria-label="ფილტრის წაშლა: {{ $filters[$filterKey]['label'] ?? $filterKey }}">
                     <i class="bi bi-x-lg"></i>
                  </a>
               </span>
            @empty
               <span class="text-muted fst-italic">ფილტრი არ არის გამოყენებული</span>
            @endforelse
         </div>
      @endif
   </div>
</div>

@once
   {!! load_script('scripts/components/filter-bar.js') !!}
@endonce
