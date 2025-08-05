<div class="col-12 col-md-6 col-xl-3">
  <div class="card h-100 border-0 shadow-sm">
    <div class="card-body d-flex align-items-center gap-3 p-3">
      <div
        class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle {{ $iconWrapperClasses ?? '' }}"
        style="min-width: 48px; min-height: 48px; {{ $iconWrapperStyle ?? '' }}">
        <i class="{{ $icon }} fs-5 {{ $iconStyle ?? '' }}"></i>
      </div>
      <div class="flex-grow-1">
        <div class="text-muted text-uppercase small fw-medium mb-1" style="word-break: break-word;">
          {{ $label }}
        </div>
        <div class="fs-4 fw-semibold">{{ $count }}</div>
      </div>
    </div>
  </div>
</div>