<div class="card card-article">
    <img src="{{ asset('images/projects/' . $project -> image ) }}" class="card-img-top response-img" alt="...">
    <div class="card-body">
        <h5 class="card-title">{{ Str::limit($project -> title -> $language, 35) }}</h5>
        <p class="card-text">{{ Str::limit($project->description->$language, 250) }}</p>
        <a href="{{ route('projects.show', ['id' => $project->id])}}" class="btn view-more">{{ __('static.page.more') }}</a>
    </div>
</div>
