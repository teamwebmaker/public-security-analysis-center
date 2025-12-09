@props([
   'action' => request()->url(),
   'method' => 'GET',
   'filters' => [], // ['key' => ['label' => '', 'options' => []]]
   'resetUrl' => request()->url(),
   'showBadges' => true,
])
@php
   $componentId = 'filter-bar-' . uniqid();
   $initialFilterKey = collect(request('filter', []))
      ->keys()
      ->first(fn($key) => array_key_exists($key, $filters));
   $initialFilterValue = $initialFilterKey ? request("filter.$initialFilterKey") : '';
@endphp

<div id="{{ $componentId }}" class="filter-bar"
   data-filter-bar
   data-filters='@json($filters)'
   data-initial-key="{{ $initialFilterKey }}"
   data-initial-value="{{ $initialFilterValue }}">
   <div class="py-3 ">
      <form method="{{ $method }}" action="{{ $action }}" class="">
            <div class="d-flex flex-wrap gap-2 align-items-start" role="group" aria-label="ჩანაწერების ფილტრები">
               <div class="d-flex flex-column  gap-1 gap-sm-2">
                  <label for="{{ $componentId }}-filter-key" class="form-label mb-0 small text-muted">
                     ფილტრის ველი
                  </label>

                  <select id="{{ $componentId }}-filter-key" data-filter-key class="form-select form-select-sm filter-bar-select-key" aria-label="აირჩიეთ ფილტრის ტიპი">
                     <option value="" @selected(!$initialFilterKey) disabled>
                        აირჩიეთ ფილტრი
                     </option>

                     @foreach($filters as $key => $option)
                     <option value="{{ $key }}" @selected($initialFilterKey===$key)>
                        {{ $option['label'] }}
                     </option>
                     @endforeach
                  </select>
               </div>
               <div class="d-flex flex-column  gap-1 gap-sm-2">
                  <label for="{{ $componentId }}-filter-value" class="form-label mb-0 small text-muted">
                     მნიშვნელობა
                  </label>

                  <select id="{{ $componentId }}-filter-value" data-filter-value class="form-select form-select-sm filter-auto-submit filter-bar-select-value" aria-label="აირჩიეთ ფილტრის მნიშვნელობა" disabled>
                     <option value="">
                        ჯერ აირჩიეთ ფილტრი
                     </option>
                  </select>
               </div>
            </div>
      </form>

      @if($showBadges)
      <div class="d-flex flex-column small mt-2" aria-live="polite" aria-atomic="true">
         @if($initialFilterKey && $initialFilterValue !== '' && $initialFilterValue !== null)
         <span class="text-muted fw-semibold">
            {{ $filters[$initialFilterKey]['label'] ?? $initialFilterKey }}:
            <strong>
               {{ $filters[$initialFilterKey]['options'][$initialFilterValue] ?? $initialFilterValue }}
            </strong>
         </span>
         @else
         <span class="text-muted fst-italic">
            ფილტრი არ არის გამოყენებული
         </span>
         @endif
      </div>
      @endif
      @if($initialFilterKey && $initialFilterValue !== '' && $initialFilterValue !== null)
      <div>
         <a href="{{ $resetUrl }}" class="text-decoration-none text-danger small">
            <span>
               გასუფთავება
            </span>
         </a>
      </div>
      @endif
   </div>
</div>

@once
   {!! load_script('scripts/components/filter-bar.js') !!}
@endonce
