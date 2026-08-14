@extends('management.master')
@section('title', 'ინსტრუქტაჟები')

<x-admin.index-view :items="$instructions" :resourceName="$resourceName" :hasSpeedDial="false">
   <x-slot name="beforeItems">
      @include('instructions.partials.filters', [
         'action' => route('management.worker.instructions.page'),
         'showAdminFilters' => false,
      ])
   </x-slot>

   @foreach($instructions as $instruction)
      <x-admin.card :document="$instruction" :title="$instruction->name" :resourceName='$resourceName' :hasDelete="false"
         :hasEdit="false" :hasTimeStamps="false">
         <x-slot name="cardDetails">
            <x-management.instructions.details-list :instruction="$instruction" :resourceName="$resourceName" />
         </x-slot>
      </x-admin.card>
   @endforeach

   <x-slot name="afterItems">
      @include('incidents.partials.document-preview-modal')
   </x-slot>
</x-admin.index-view>
