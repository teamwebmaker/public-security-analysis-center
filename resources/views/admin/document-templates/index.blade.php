@extends('layouts.admin.admin-dashboard')

@section('title', 'შაბლონების სია')

<x-admin.index-view :items="$document_templates" :resourceName="$resourceName">
    @foreach($document_templates as $document_template)
        <x-admin.card :document="$document_template" :title="$document_template->name" :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <x-admin.document-templates.details-list :data="$document_template" :resourceName="$resourceName" />
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>