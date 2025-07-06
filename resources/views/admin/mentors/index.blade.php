@extends('layouts.dashboard')

@section('title', 'პარტნიორების სია')

<x-admin.index-view :items="$mentors" :resourceName="$resourceName">
    @foreach($mentors as $mentor)
        <x-admin-card :document="$mentor" :title="$mentor->full_name" :image="$mentor->image" :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <ul class="list-group list-group-flush mb-3">
                    <span>პროგრამა:</span>
                    @foreach ($mentor->programs as $program)
                        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
                            <label class="truncate d-inline-block badge bg-secondary" data-bs-toggle="tooltip"
                                data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                                data-bs-title="{{ $program->title->ka }}" style="max-width: 150px; cursor: pointer;">
                                <span>{{ $program->title->ka }}</span>
                            </label>
                        </li>
                    @endforeach
                </ul>
            </x-slot>
        </x-admin-card>
    @endforeach
</x-admin.index-view>