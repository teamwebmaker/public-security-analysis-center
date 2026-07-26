@props(['count'])

<div class="d-flex justify-content-end align-items-center mb-2" aria-live="polite">
    <span class="text-muted">სულ: <strong class="text-dark">{{ number_format((int) $count) }}</strong></span>
</div>
