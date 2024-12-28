<div class="card card-article">
    <div class="card-header p-0">
        <img src="{{ asset('images/publications/' . $publication -> image) }}" class="card-img-bottom" alt="...">
    </div>
    <div class="card-body">
        <h5 class="card-title truncate black-text">{{ $publication -> title -> $language }}</h5>
        <p class="card-text line-clamp black-text">{{ Str::limit($publication->description->$language, 250) }}</p>
    </div>
    <div class="publication-card-footer p-4">
        <div class="row">
            <div class="col">
                <p class="card-text"><small class="text-body-secondary">{{ $publication -> created }}</small></p>
            </div>
            <div class="col">
                <a href="{{ route('publications.show', ['id' => $publication->id])}}" class="btn view-more float-end">
                    გაიგე მეტი
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </div>
    </div>
</div>
