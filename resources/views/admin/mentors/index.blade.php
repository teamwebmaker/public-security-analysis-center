@extends('layouts.admin.admin-dashboard')

@section('title', 'პარტნიორების სია')

<x-admin.index-view :items="$mentors" :resourceName="$resourceName">
    @foreach($mentors as $mentor)
        <x-admin.card :document="$mentor" :title="$mentor->full_name" :image="$mentor->image" :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <ul class="list-group list-group-flush mb-3">
                    <x-ui.info-dropdown-item label="პროგრამები" icon="bi bi-briefcase" name="programs_dropdown"
                        :items="$mentor->programs" :getItemText="fn($program) => $program->title->ka" />
                </ul>
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>