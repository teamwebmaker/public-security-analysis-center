<div class="{{ $overlay
    ? 'position-absolute top-50 start-50 translate-middle text-center text-muted'
    : 'd-flex flex-column justify-content-center text-center text-muted' }}" @if(!$overlay)
    style="min-height: {{ $minHeight }};" @endif>
    <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
    <p class="mb-1">{{ $message }}</p>

    @if ($resourceName)
        <a href="{{ route($resourceName . '.create') }}"
            class="text-primary small text-decoration-none d-inline-flex align-items-center">
            დამატება
            <i class="bi bi-plus fs-5"></i>
        </a>
    @endif
</div>