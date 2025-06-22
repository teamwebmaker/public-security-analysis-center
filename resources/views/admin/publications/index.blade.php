@extends('layouts.dashboard')

@section('title', 'პუბლიკაციების სია')

@section('main')
    <x-admin.index-view :items="$publications">
        @foreach($publications as $publication)
            <x-admin-card :document="$publication" :image="$publication->image" :resourceName="$resourceName">
                <x-slot name="cardDetails">
                    @if ($publication->file)
                        <a href="{{ asset('documents/' . $resourceName . '/' . $publication->file) }}" data-fancybox data-type="pdf"
                            class="btn btn-sm btn-outline-success w-100">
                            <i class="bi bi-file-earmark-pdf me-2"></i>Pdf დოკუმენტი
                        </a>
                    @endif
                </x-slot>
            </x-admin-card>
        @endforeach
    </x-admin.index-view>
@endsection