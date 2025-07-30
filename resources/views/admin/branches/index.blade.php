@extends('layouts.admin.admin-dashboard')

@section('title', 'ფილიალების სია')

<x-admin.index-view :items="$branches" :resourceName="$resourceName">
    @foreach($branches as $branch)
        <x-admin.card :document="$branch" :title="$branch->name" :resourceName='$resourceName'
            message="დარწმუნებული ხარ, რომ გსურს ფილიალი „{{ $branch->name }}“ წაიშალოს? რადან მასთან დაკავშირებული სამუშაოები მხოლოდ ფილიალის სახელს შეინარჩუნებენ და სხვა მონაცემები წაიშლება"
            cardClass="card h-100 border-0 overflow-hidden position-relative {{ $selectedBranchId == $branch->id ? ' shadow-sm bg-dark-subtle' : '' }}">
            <x-slot name="cardDetails">
                <x-admin.branches.details-list :branch="$branch" :users="$branch->users" />
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>