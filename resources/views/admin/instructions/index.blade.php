@extends('layouts.admin.admin-dashboard')

@section('title', 'ინსტრუქტაჟების სია')

<x-admin.index-view :items="$instructions" :resourceName="$resourceName">
    @foreach($instructions as $instruction)
        <x-admin.card :document="$instruction" :title="$instruction->name" :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <x-admin.instructions.details-list :instruction="$instruction" :resourceName="$resourceName" />
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>