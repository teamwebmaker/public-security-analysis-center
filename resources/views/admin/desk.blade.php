@extends('layouts.dashboard')
@section('title', 'Admin Dashboard')
@section('main')
    <div class="row">
    <div class="col-md-4 mb-3">
        <div class="card gold-bg">
            <div class="card-header  d-flex justify-content-between">
                <button class="btn black-bg px-2 gold-text py-1">
                    <i class="bi bi-folder-plus"></i>
                </button>
                <button class="btn black-bg gold-text px-2 py-1">
                    <span class="btn-label">{{ $projects -> title }}</span>
                </button>
            </div>
            <div class="card-body d-flex justify-content-between">
                <button class="btn black-bg gold-text  px-2 py-1">
                    <span class="btn-label">რაოდენობა</span>
                </button>
                <button class="btn black-bg gold-text px-2 py-1">{{ $projects -> count }}</button>
            </div>
        </div>
    </div>
     <div class="col-md-4 mb-3">
            <div class="card gold-bg">
                <div class="card-header  d-flex justify-content-between">
                    <button class="btn black-bg px-2 gold-text py-1">
                        <i class="bi bi-people"></i>
                    </button>
                    <button class="btn black-bg  px-2 gold-text py-1">
                        <span class="btn-label">{{ $partners -> title }}</span>
                    </button>
                </div>
                <div class="card-body d-flex justify-content-between">
                    <button class="btn black-bg gold-text px-2 py-1">
                        <span class="btn-label">რაოდენობა</span>
                    </button>
                    <button class="btn black-bg gold-text px-2 py-1">{{ $partners -> count }}</button>
                </div>
            </div>
     </div>
     <div class="col-md-4 mb-3">
            <div class="card gold-bg">
                <div class="card-header  d-flex justify-content-between">
                    <button class="btn black-bg gold-text px-2 py-1">
                        <i class="bi bi-briefcase"></i>
                    </button>
                    <button class="btn black-bg gold-text px-2 py-1">
                        <span class="btn-label">სპეც.პროგრამები</span>
                    </button>
                </div>
                <div class="card-body d-flex justify-content-between">
                    <button class="btn black-bg gold-text px-2 py-1">
                        <span class="btn-label">რაოდენობა</span>
                    </button>
                    <button class="btn black-bg gold-text px-2 py-1">40</button>
                </div>
            </div>
     </div>
      <div class="col-md-4 mb-3">
            <div class="card gold-bg">
                <div class="card-header  d-flex justify-content-between">
                    <button class="btn black-bg gold-text px-2 py-1">
                        <i class="bi bi-book"></i>
                    </button>
                    <button class="btn black-bg gold-text px-2 py-1">
                        <span class="btn-label">{{ $publications -> title }}</span>
                    </button>
                </div>
                <div class="card-body d-flex justify-content-between">
                    <button class="btn black-bg gold-text px-2 py-1">
                        <span class="btn-label">რაოდენობა</span>
                    </button>
                    <button class="btn black-bg gold-text px-2 py-1">{{ $publications -> count }}</button>
                </div>
            </div>
      </div>
      <div class="col-md-4 mb-3">
            <div class="card gold-bg">
                <div class="card-header  d-flex justify-content-between">
                    <button class="btn black-bg gold-text px-2 py-1">
                        <i class="bi bi-book"></i>
                    </button>
                    <button class="btn black-bg gold-text px-2 py-1">
                        <span class="btn-label">{{ $contacts -> title }}</span>
                    </button>
                </div>
                <div class="card-body d-flex justify-content-between">
                    <button class="btn black-bg gold-text px-2 py-1">
                        <span class="btn-label">რაოდენობა</span>
                    </button>
                    <button class="btn black-bg gold-text px-2 py-1">{{ $contacts -> count }}</button>
                </div>
            </div>
        </div>
</div>
@endsection
@section('scripts')
    <script></script>
@endsection
