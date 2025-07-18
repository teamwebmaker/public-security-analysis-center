@extends('layouts.admin.admin-dashboard')

@section('title', 'სერვისების სია')

<x-admin.index-view :items="$services" :resourceName="$resourceName">
    @foreach($services as $service)
        <x-admin-card :document="$service" :title="$service->title->ka" :image="$service->image"
            :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <x-admin.services.details-list :service="$service" />
            </x-slot>
        </x-admin-card>
    @endforeach
</x-admin.index-view>