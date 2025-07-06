@extends('layouts.dashboard')
@section('title', 'შეტყობინებების სია')

<x-admin.index-view :items="$contacts" :hasSpeedDial="false">
    @foreach($contacts as $contact)
        @php
            // needed because admin card uses document->description
            $contact['description'] = $contact->message;
        @endphp

        <x-admin-card :document="$contact" :title="$contact->full_name" :resourceName='$resourceName' :hasEdit="false"
            :hasVisibility="false"
            cardClass="card h-100 border-0 overflow-hidden position-relative {{ $selectedContactId == $contact->id ? ' shadow-sm bg-dark-subtle' : '' }}">
            <x-slot name="cardDetails">
                <x-admin.contacts.details-list :contact="$contact" />
            </x-slot>
        </x-admin-card>
    @endforeach
</x-admin.index-view>