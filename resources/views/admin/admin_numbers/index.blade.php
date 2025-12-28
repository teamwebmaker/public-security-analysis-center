@extends('layouts.admin.admin-dashboard')

@section('title', 'ჩემი ნომრები')

<x-admin.index-view :items="$admin_numbers" :resourceName="$resourceName">
    @foreach($admin_numbers as $admin_number)
        <x-admin.card :document="$admin_number" :title="$admin_number->name" :resourceName="$resourceName"
            :hasVisibility="false">
            <x-slot name="cardDetails">
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex align-items-center gap-2">
                        <i class="bi bi-telephone"></i>
                        <span>{{ $admin_number->phone }}</span>
                    </li>
                </ul>
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>
