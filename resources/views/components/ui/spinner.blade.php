@props([
    "id" => 'spinner',
    'size' => 'md',        // sm, md, lg
    'color' => 'primary',  // Bootstrap colors: primary, secondary, danger, etc.
    'text' => null,        // Optional loading text
    'centered' => false,   // true = absolutely center inside parent
    'inline' => false,     // true = inline spinner (e.g. inside a button)
])
@php
    $sizeClasses = [
        'sm' => 'spinner-border-sm',
        'md' => '',
        'lg' => 'spinner-border-lg',
    ][$size] ?? '';

    $wrapperClass = $centered
        ? 'position-absolute top-50 start-50 translate-middle text-center'
        : ($inline ? 'd-inline-flex align-items-center gap-2' : 'd-flex flex-column align-items-center justify-content-center py-3');
@endphp
<div class="{{ $wrapperClass }}" id="{{ $id }}">
    <div class="spinner-border text-{{ $color }} {{ $sizeClasses }}" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    @if($text)
        <span class="{{ $inline ? '' : 'mt-2' }}">{{ $text }}</span>
    @endif
</div>
