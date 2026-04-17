@extends('layouts.admin.admin-dashboard')

@section('title', 'გზამკვლევები')

<x-admin.index-view :items="$guides" :resourceName="$resourceName">
    @foreach($guides as $guide)
        <x-admin.card :document="$guide" :title="$guide->name" :resourceName="$resourceName" :hasVisibility="false">
            <x-slot name="cardDetails">
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center gap-2">
                        <span>ბმული:</span>
                        <a href="{{ $guide->link }}" class="text-decoration-underline text-break">
                            {{ $guide->link }}
                        </a>
                    </li>
                    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
                        <span>რიგითობა:</span>
                        <span class="badge bg-primary rounded-pill">{{ $guide->sort_order }}</span>
                    </li>
                </ul>
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>
