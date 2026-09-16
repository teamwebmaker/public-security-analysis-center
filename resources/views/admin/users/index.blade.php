@extends('layouts.admin.admin-dashboard')

@section('title', 'მომხმარებებლებთა სია')

<x-admin.index-view :items="$users" :resourceName="$resourceName">
  <x-slot name="beforeItems">
    <x-shared.resource-list-filters
      :action="route('users.index')"
      searchPlaceholder="სახელი, ელფოსტა, ტელეფონი, როლი, კომპანია ან ფილიალი"
      :filters="[
        'role_id' => [
          'label' => 'როლი',
          'placeholder' => 'ყველა როლი',
          'options' => $userFilterOptions['roles'],
        ],
        'company_id' => [
          'label' => 'კომპანია',
          'placeholder' => 'ყველა კომპანია',
          'options' => $userFilterOptions['companies'],
        ],
      ]" />
  </x-slot>

  @foreach($users as $user)
    <x-admin.card :document="$user" :title="$user->full_name" :hasVisibility="false" :resourceName='$resourceName'>
    <x-slot name="cardDetails">

      <x-admin.users.details-list :user="$user" :companies="$user->companies" :branches="$user->branches"
      :services="$user->services" :tasks="$user->tasks" :workerCompanies="$user->workerCompanies" />
    </x-slot>
    </x-admin.card>
  @endforeach
</x-admin.index-view>
