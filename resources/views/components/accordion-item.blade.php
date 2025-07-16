<div class="accordion-item {{ $open ? 'show' : '' }}">
    <h2 class="accordion-header" id="heading-{{ $id }}">
        <button class="accordion-button {{ $open ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
            data-bs-target="#collapse-{{ $id }}" aria-expanded="{{ $open ? 'true' : 'false' }}"
            aria-controls="collapse-{{ $id }}">
            <i class="bi {{ $icon }} me-2 fs-5"></i> {{ $label }}
        </button>
    </h2>
    <div id="collapse-{{ $id }}" class="accordion-collapse collapse {{ $open ? 'show' : '' }}"
        aria-labelledby="heading-{{ $id }}" data-bs-parent="#{{ $parent }}">
        <div class="accordion-body">
            {{ $slot }}
        </div>
    </div>
</div>