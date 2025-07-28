@extends('layouts.admin.admin-dashboard')

@section('title', 'სერვისების სია')

<x-admin.index-view :items="$services" :resourceName="$resourceName">
    @foreach($services as $service)
        <x-admin.card :document="$service" :title="$service->title->ka" :image="$service->image"
            :resourceName='$resourceName'
            message="დარწმუნებული ხარ, რომ გსურს სერვისი „{{ $service->title->ka }}“ წაიშალოს? რადგან მასთან დაკავშირებული სამუშაოები მხოლოდ სერვისის სახელს შეინარჩუნებენ და სხვა მონაცემები წაიშლება."
            cardClass="card h-100 border-0 overflow-hidden position-relative {{ $selectedServiceId == $service->id ? ' shadow-sm bg-dark-subtle' : '' }}">
            <x-slot name="cardDetails">
                <x-admin.services.details-list :service="$service" />
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>