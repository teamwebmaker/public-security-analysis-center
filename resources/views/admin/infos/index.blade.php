@extends('layouts.dashboard')

@section('title', 'ჩვენს შესახებ')

<x-admin.index-view :items="$infos" :resourceName="$resourceName" :hasSpeedDial="false" containerClass="row">
    @foreach($infos as $info)
        <x-admin-card :document="$info" :title="$info->title->ka" :image="$info->image" :resourceName='$resourceName'
            :hasDelete="false" :hasVisibility="false" containerClass="col col-sm-7 m-auto">
            <x-slot name="cardDetails">
                <x-infos.details-list :info="$info" />
            </x-slot>
        </x-admin-card>
    @endforeach
</x-admin.index-view>