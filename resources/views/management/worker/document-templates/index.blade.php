@extends('management.master')

@section('title', 'შაბლონების სია')

@section('main')
    <x-admin.index-view :items="$document_templates" :resourceName="$resourceName" :hasSpeedDial="false">
        <x-slot name="beforeItems">
            <x-shared.resource-list-filters
                :action="route('management.worker.document-templates.page')"
                searchPlaceholder="დასახელება ან დოკუმენტი"
                :filters="[
                    'document_type' => [
                        'label' => 'დოკუმენტის ტიპი',
                        'options' => ['pdf' => 'PDF', 'word' => 'Word', 'excel' => 'Excel'],
                    ],
                ]" />
        </x-slot>

        @foreach($document_templates as $document_template)
            <x-admin.card :document="$document_template" :title="$document_template->name" :resourceName='$resourceName'
                :hasDelete="false" :hasEdit="false" :hasTimeStamps="false">
                <x-slot name="cardDetails">

                    <li class="list-group-item d-flex justify-content-between mt-3 bg-transparent flex-wrap align-items-center">
                        <x-ui.document-link :file="$document_template->document" :path="'documents/' . $resourceName"
                            label="დოკუმენტი" />
                    </li>
                </x-slot>
            </x-admin.card>
        @endforeach
    </x-admin.index-view>

@endsection
