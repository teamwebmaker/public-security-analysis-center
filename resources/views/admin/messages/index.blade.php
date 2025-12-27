@extends('layouts.admin.admin-dashboard')
@section('title', 'შეტყობინებების სია')

<x-admin.index-view :items="$messages" :hasSpeedDial="false">
    @foreach($messages as $message)
        @php
            // needed because admin card uses document->description
            $message['description'] = $message->message;
            $isUnread = $message->read_at === null;
        @endphp

        <x-admin.card :document="$message" :title="$message->full_name" :resourceName='$resourceName' :hasEdit="false"
            :hasDelete="false" :hasVisibility="false"
            cardClass="card h-100 border-0 overflow-hidden position-relative {{ $selectedMessageId == $message->id ? ' shadow-sm bg-dark-subtle' : '' }}">
            <x-slot name="cardDetails">
                <x-admin.messages.details-list :message="$message" />
            </x-slot>
            <x-slot name="cardFooter">
                <form method="POST" action="{{ route('messages.destroy', $message) }}"
                    onsubmit="return confirm('ნამდვილად გსურთ შეტყობინების წაშლა?')" class="flex-grow-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-trash"></i>
                        <span>წაშლა</span>
                    </button>
                </form>
                @if ($isUnread)
                    <form method="POST" action="{{ route('messages.mark-read', $message) }}" class="flex-grow-1">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                            class="btn btn-outline-success btn-sm w-100 d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-check2-circle"></i>
                            <span>წაკითხულად მონიშვნა</span>
                        </button>
                    </form>
                @endif
            </x-slot>
        </x-admin.card>
    @endforeach
</x-admin.index-view>
