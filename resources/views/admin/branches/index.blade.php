@extends('layouts.admin.admin-dashboard')

@section('title', 'ფილიალების სია')

<x-admin.index-view :items="$branches" :resourceName="$resourceName">
    @foreach($branches as $branch)
        <x-admin.card :document="$branch" :title="$branch->name" :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <x-admin.branches.details-list :branch="$branch" />
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>