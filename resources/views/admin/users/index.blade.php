@extends('layouts.admin.admin-dashboard')

@section('title', 'მომხმარებებლებთა სია')

<x-admin.index-view :items="$users" :resourceName="$resourceName">
  @foreach($users as $user)
    <x-admin-card :document="$user" :title="$user->full_name" :hasVisibility="false" :resourceName='$resourceName'>
    <x-slot name="cardDetails">
      <x-admin.users.details-list :user="$user" />
    </x-slot>
    </x-admin-card>
  @endforeach
</x-admin.index-view>