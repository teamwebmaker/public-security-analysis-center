@extends('management.master')
@section('title', 'ინსტრუქტაჟები')


@section('main')
   @if ($instructions->isNotEmpty())
      <x-admin.index-view :items="$instructions" :resourceName="$resourceName">
         @foreach($instructions as $instruction)
            <x-admin.card :document="$instruction" :title="$instruction->name" :resourceName='$resourceName' :hasDelete="false"
               :hasEdit="false" :hasTimeStamps="false">
               <x-slot name="cardDetails">
                  <x-management.instructions.details-list :instruction="$instruction" :resourceName="$resourceName" />
               </x-slot>
            </x-admin.card>
         @endforeach
      </x-admin.index-view>
   @else
      <x-ui.empty-state-message :resourceName="null" :overlay="false" />
   @endif
@endsection