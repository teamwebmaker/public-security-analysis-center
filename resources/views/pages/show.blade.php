@extends('layouts.master')
@section('title', $item->title->$language)
@section('main')
    <div class="container-fluid my-4">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <x-single-item-component :item="$item" :language="$language" :category="$category"
                        isPdfMarkerDisplayed="{{ isset($item->file) || isset($item->document) }}" />
                </div>

                {{-- Conditional button --}}
                @if (!empty($requestButton) && !empty($requestButton['route']))
                    <div class="col-12 text-center mb-4">
                        <a href="{{ $requestButton['route'] }}"
                            class="py-2 fs-5 link-underline link-underline-opacity-0 gold-bg text-white fw-bold btn-lg px-4 rounded-pill d-inline-flex align-items-center gap-2">
                            @if (!empty($requestButton['icon']))
                                <i class="bi {{ $requestButton['icon'] }} me-2"></i>
                            @endif
                            {{ $requestButton['label'] ?? 'Button' }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection