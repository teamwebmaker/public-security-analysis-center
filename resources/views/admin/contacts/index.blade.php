@extends('layouts.dashboard')
@section('title', 'შეტყობინებების სია')

<x-admin.index-view :items="$contacts" :hasSpeedDial="false" :resourceName="$resourceName">
    @foreach($contacts as $contact)
        @php
            $contact['description'] = $contact->message;
        @endphp
        <x-admin-card :document="$contact" :title="$contact->full_name" :resourceName='$resourceName' :hasEdit=" false"
            :hasEdit="false" :hasVisibility="false">
            <x-slot name="cardDetails">
                <ul class="list-group list-group-flush mb-3">
                    @if ($contact->subject)
                        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
                            <span>დანიშნულება:</span>
                            <label class="text-truncate d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
                                data-bs-custom-class="custom-tooltip" data-bs-title="{{ $contact->subject }}"
                                style="max-width: 150px; cursor: pointer;">
                                <span>{{ $contact->subject }}</span>
                            </label>
                        </li>

                    @endif
                    </li>
                    @if ($contact->phone)
                        <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
                            <span>ტელეფონი:</span>
                            <span class="badge bg-primary rounded-pill">
                                {{ $contact->phone }}
                            </span>
                        </li>
                    @endif
                    <li class="list-group-item d-flex justify-content-between flex-wrap align-items-center">
                        <span>ელ.ფოსტა:</span>
                        <span class="badge bg-primary rounded-pill">
                            {{ $contact->email }}
                        </span>
                    </li>
                </ul>
            </x-slot>
        </x-admin-card>
    @endforeach
</x-admin.index-view>

@section('scripts')
    {!! load_script('scripts/contact.js') !!}
@endsection