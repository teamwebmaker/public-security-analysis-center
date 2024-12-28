@extends('layouts.dashboard')
@section('title', 'Contacts List')
@section('main')
    @session('success')
    <div class="alert alert-success" role="alert"  x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        {{ $value }}
    </div>
    @endsession
    <div class="row">
        @foreach($contacts as $contact)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card">
                    <div class="card-body">
                         <h3 class="card-title truncate" title="{{ $contact -> email }}">
                             {{ $contact -> email }}
                         </h3>
                        <p class="line-clamp">
                            {{ $contact -> message}}
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between">
                        <a type="button" class="btn btn-success d-flex gap-2" href="{{ route('contacts.show', ['contact' => $contact]) }}">
                            <i class="bi bi-binoculars"></i>
                            <span class="text-label">Show</span>
                        </a>
                        <form method="POST" action="{{ route('contacts.destroy', ['contact' => $contact]) }}" onsubmit="return confirm('წავშალოთ შეტყობინება?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger d-flex gap-2">
                                <i class="bi bi-trash"></i>
                                <span class="text-label">Delete</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! $contacts->withQueryString()->links('pagination::bootstrap-5') !!}
        </div>
    </div>
@endsection

@section('scripts')
    <script></script>
@endsection

