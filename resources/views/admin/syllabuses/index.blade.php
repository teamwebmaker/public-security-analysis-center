@extends('layouts.dashboard')

@section('title', 'სილაბუსების სია')

@section('main')
    <x-admin.index-view :items="$syllabuses" :resourceName="$resourceName">
        @foreach($syllabuses as $syllabus)
            <x-admin-card :document="$syllabus" :title="$syllabus->title->ka" :resourceName="$resourceName">
                <x-slot name="cardDetails">
                    <x-admin.syllabuses.details-list :syllabus="$syllabus" :resourceName="$resourceName" />
                </x-slot>
            </x-admin-card>
        @endforeach
    </x-admin.index-view>
@endsection