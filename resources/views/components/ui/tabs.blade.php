@props(['tabs' => []])

<nav>
    <div class="nav nav-tabs" id="nav-tab" role="tablist">
        @foreach ($tabs as $index => $tab)
            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="{{ $tab['id'] }}-tab" data-bs-toggle="tab"
                data-bs-target="#{{ $tab['id'] }}-tab-content" type="button" role="tab"
                aria-controls="{{ $tab['id'] }}-tab-content" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                {{ $tab['label'] }}
            </button>
        @endforeach
    </div>
</nav>

<div class="tab-content p-3 border border-top-0 rounded-bottom" id="nav-tabContent">
    {{ $slot }}
</div>