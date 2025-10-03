@props([
    'tabs' => [],
    'navClass' => 'nav nav-tabs d-flex flex-column flex-sm-row',
    'buttonClass' => 'nav-link',
    'contentClass' => 'tab-content p-3 border border-top-0 rounded-bottom',
])
 
<nav>
    <div class="{{ $navClass }}" id="nav-tab" role="tablist">
        @foreach ($tabs as $index => $tab)
            <button 
                class="{{ $buttonClass }} {{ $index === 0 ? 'active' : '' }}" 
                id="{{ $tab['id'] }}-tab" 
                data-bs-toggle="tab"
                data-bs-target="#{{ $tab['id'] }}-tab-content" 
                type="button" 
                role="tab"
                aria-controls="{{ $tab['id'] }}-tab-content" 
                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                {{ $tab['label'] }}
            </button>
        @endforeach
    </div>
</nav>
 
<div {{ $attributes->merge(['class' => $contentClass]) }} id="nav-tabContent">
    {{ $slot }}
</div>
