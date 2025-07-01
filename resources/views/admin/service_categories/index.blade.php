@extends('layouts.dashboard')

@section('title', 'სერვის კატეგორიების სია')

@section('main')
    <x-admin.index-view :items="$serviceCategories" :resourceName="$resourceName">
        @foreach($serviceCategories as $serviceCategory)
            <x-admin-card :document="$serviceCategory" :title="$serviceCategory->name->ka" :resourceName="$resourceName" />
        @endforeach
    </x-admin.index-view>
@endsection