@extends('layouts.admin.admin-dashboard')

@section('title', 'ინსტრუქტაჟების სია')

<x-admin.index-view :items="$instructions" :resourceName="$resourceName">
    <x-slot name="beforeItems">
        @include('instructions.partials.filters', [
            'action' => route('instructions.index'),
            'showAdminFilters' => true,
        ])
    </x-slot>

    @foreach($instructions as $instruction)
        <x-admin.card :document="$instruction" :title="$instruction->name" :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <x-admin.instructions.details-list :instruction="$instruction" :resourceName="$resourceName" />
            </x-slot>
        </x-admin.card>
    @endforeach

    <x-slot name="afterItems">
        @include('incidents.partials.document-preview-modal')
    </x-slot>
</x-admin.index-view>
