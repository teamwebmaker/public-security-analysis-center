<!-- With this component user can go back to previous page but if there is no previous page then it will go to fallback route -->
<!-- previous page resets if page reloads -->
@php
    $currentUrl = url()->full();
    $previousUrl = url()->previous();
    $fallbackUrl = route($fallback);

    $backUrl = ($previousUrl === $currentUrl) ? $fallbackUrl : $previousUrl;

    $btnClass = "btn btn-{$type}";
    if ($size) {
        $btnClass .= " btn-{$size}";
    }
@endphp

<a href="{{ $backUrl }}" class="{{ $btnClass }}">
    <i class="bi bi-arrow-left me-2"></i> {{ $text }}
</a>