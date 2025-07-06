@extends('layouts.dashboard')

@section('title', 'შეტყობინების გამომწერები სია')


<x-admin.index-view :items="$subscriptions" :hasSpeedDial="false"
    containerClass="position-relative row row-cols-1 row-cols-sm-2 row-cols-lg-3 pt-5">

    <!-- Push Toggle Button - Placed above cards -->
    <div class="position-absolute top-0 start-0">
        <button class="btn btn-sm bg-secondary text-white" id="push-toggle-btn">
            ვამოწმებ...
        </button>
    </div>

    @foreach($subscriptions as $subscription)
        <x-admin-card :document="$subscription" :title="$subscription->browser ?? 'Browser not detected'"
            :resourceName="$resourceName" :hasEdit="false" :hasDelete="false" :hasVisibility="false">
            <x-slot name="cardDetails">
                <x-admin.push-subscriptions.details-list :subscription="$subscription" />
            </x-slot>
            <x-slot name='cardFooter'>
                <x-admin.push-subscriptions.card-footer :subscription="$subscription" />
            </x-slot>
        </x-admin-card>
    @endforeach
</x-admin.index-view>

@section('scripts')
    <script>
        // Pass Vapid Public Key to js
        window.APP_VAPID_PUBLIC_KEY = "{{ env('VAPID_PUBLIC_KEY') }}";
        window.APP_CSRF_TOKEN = "{{ csrf_token() }}";
    </script>

    {!! load_script('scripts/bootstrap/bootstrapTooltips.js') !!}
    {!! load_script('scripts/toggle_notification_subscription.js') !!}
@endsection