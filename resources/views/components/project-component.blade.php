<div class="card">
    <img src="{{ $project -> image }}" class="card-img-top response-img" alt="...">
    <div class="card-body">
        <h5 class="card-title">{{ $project -> title -> $language }}</h5>
        <p class="card-text">{{ $project -> description -> $language }}</p>
        <a href="#" class="btn btn-primary">{{ __('static.page.more') }}</a>
    </div>
</div>
