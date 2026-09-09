@extends('layouts.admin.admin-dashboard')

@section('title', 'შაბლონების სია')

<x-admin.index-view :items="$document_templates" :resourceName="$resourceName">
    <x-slot name="beforeItems">
        <x-shared.resource-list-filters
            :action="route('document-templates.index')"
            searchPlaceholder="დასახელება, დოკუმენტი ან შემსრულებელი"
            :filters="[
                'worker_id' => [
                    'label' => 'შემსრულებელი',
                    'placeholder' => 'ყველა შემსრულებელი',
                    'options' => $documentTemplateFilterOptions['workers'],
                ],
                'visibility' => [
                    'label' => 'ხილვადობა',
                    'options' => ['1' => 'ხილული', '0' => 'დამალული'],
                ],
                'document_type' => [
                    'label' => 'დოკუმენტის ტიპი',
                    'options' => ['pdf' => 'PDF', 'word' => 'Word', 'excel' => 'Excel'],
                ],
            ]" />
    </x-slot>

    @foreach($document_templates as $document_template)
        <x-admin.card :document="$document_template" :title="$document_template->name" :resourceName='$resourceName'>
            <x-slot name="cardDetails">
                <x-admin.document-templates.details-list :data="$document_template" :resourceName="$resourceName" />
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>
