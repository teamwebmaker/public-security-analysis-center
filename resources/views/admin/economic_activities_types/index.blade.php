@extends('layouts.admin.admin-dashboard')

@section('title', 'ეკონომიკური საქმიანობის ტიპების სია')

<x-admin.index-view :items="$economic_activities_types" :resourceName="$resourceName">
    @foreach($economic_activities_types as $economic_activity_type)
        <x-admin.card :document="$economic_activity_type" :title="$economic_activity_type->display_name"
            :resourceName='$resourceName' :hasVisibility="false" />
    @endforeach
</x-admin.index-view>