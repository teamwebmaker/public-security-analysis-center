@props([
  'icon',
  'color',
  'count',
  'label',
])

<div class="d-flex align-items-center gap-2">
  <i class="{{ $icon }} text-{{ $color }}"></i>
  <span class="badge ps-0 bg-opacity-10 text-{{ $color }}">
    {{ $count }} {{ $label }}
  </span>
</div>
