@extends('layouts.admin.admin-dashboard')

@section('title', 'მენიუების სია')

<x-admin.index-view :items="$main_menus" :resourceName="$resourceName">
    @foreach($main_menus as $main_menu)
        <x-admin-card :document="$main_menu" :title="$main_menu->title->ka" :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
                        <span>რიგითობა:</span>
                        <span class="badge bg-primary rounded-pill">
                            {{ $main_menu->sorted }}
                        </span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
                        <span>სტატუსი:</span>
                        <span class="badge {{ $main_menu->visibility ? 'bg-success' : 'bg-warning' }}">
                            {{ $main_menu->visibility ? 'ხილული' : 'დამალული' }}
                        </span>
                    </li>
                </ul>
            </x-slot>
        </x-admin-card>
    @endforeach
</x-admin.index-view>