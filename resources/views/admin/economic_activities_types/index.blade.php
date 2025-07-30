@extends('layouts.admin.admin-dashboard')

@section('title', 'ეკონომიკური საქმიანობის ტიპების სია')

<x-admin.index-view :items="$economic_activities_types" :resourceName="$resourceName">
    @foreach($economic_activities_types as $economic_activity_type)
        <x-admin.card :document="$economic_activity_type" :title="$economic_activity_type->display_name"
            :resourceName='$resourceName' :hasVisibility="false"
            message='ტიპი „{{ $economic_activity_type->display_name }}“ წაშლის შემდეგ, კომპანიების ჩამონათვალში ველი „ეკონომიკური საქმიანობის ტიპი“ გამოჩნდება როგორც „მითითებული არ არის“. გსურთ გაგრძელება?'>
            <x-slot name="cardDetails">
                <x-admin.economic-activities-types.details-list :companies="$economic_activity_type->companies" />
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>