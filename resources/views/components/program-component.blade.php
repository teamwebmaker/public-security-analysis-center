<div class="card card-article">
    <div class="image">
        <img src="{{ asset('images/programs/' . $program->image) }}" class="card-img-top response-img" alt="...">
    </div>
    <div class="card-body">
        <h5 class="card-title">{{ $program->title->$language }}</h5>
        <a href="{{ route('programs.show', ['id' => $program->id]) }}" class="btn view-more float-end">
            გაიგე მეტი
            <span aria-hidden="true">→</span>
        </a>
    </div>
</div>