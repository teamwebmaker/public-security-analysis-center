@extends('management.master')

@section('title', 'შაბლონების სია')

@section('main')
   @if ($document_templates->isNotEmpty())
      <x-admin.index-view :items="$document_templates" :resourceName="$resourceName">
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
   @else
      <x-ui.empty-state-message :resourceName="null" :overlay="false" />
   @endif


@endsection