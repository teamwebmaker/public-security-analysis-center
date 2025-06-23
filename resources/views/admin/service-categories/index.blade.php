@extends('layouts.dashboard')

@section('title', 'სერვის კატეგორიების სია')

@section('main')
    <x-admin.index-view :items="$serviceCategories">
        @foreach($serviceCategories as $serviceCategory)
            <x-admin-card :document="$serviceCategory" :image="$serviceCategory->image" :resourceName="$resourceName" />
        @endforeach
    </x-admin.index-view>
@endsection