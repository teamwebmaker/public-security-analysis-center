@extends('layouts.admin.admin-dashboard')

@section('title', 'პარტნიორების სია')

<x-admin.index-view :items="$partners" :resourceName="$resourceName">
    @foreach($partners as $partner)
        <x-admin-card :document="$partner" :title="$partner->title" :image="$partner->image"
            :resourceName='$resourceName' />
    @endforeach
</x-admin.index-view>