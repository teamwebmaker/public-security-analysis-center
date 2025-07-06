@extends('layouts.dashboard')

@section('title', 'პროექტების სია')

<x-admin.index-view :items="$projects" :resourceName="$resourceName">
    @foreach($projects as $project)
        <x-admin-card :document="$project" :title="$project->title->ka" :image="$project->image"
            :resourceName='$resourceName' />
    @endforeach
</x-admin.index-view>