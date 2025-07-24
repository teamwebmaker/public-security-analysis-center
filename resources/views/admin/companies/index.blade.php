@extends('layouts.admin.admin-dashboard')

@section('title', 'კომპანიების სია')

<x-admin.index-view :items="$companies" :resourceName="$resourceName">
    @foreach($companies as $company)
        <x-admin.card :document="$company" :title="$company->name" :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <x-admin.companies.details-list :company="$company" />
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>