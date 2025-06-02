<div class="card card-article">
    <img src="{{ asset(implode("/", ['images', $article->collection, $article->image])) }}" class="response-img"
        alt="{{ json_decode($article->title)->$language }}">
    <div class="card-body">
        <h5 class="card-title truncate" title="{{ json_decode($article->title)->$language }}">
            {{ json_decode($article->title)->$language }} </h5>
        <p class="card-text line-clamp">
            {{ Str::limit(json_decode($article->description)->$language, 70) }}
        </p>
        <a href="{{ route(implode('.', [$article->collection, 'show']), ['id' => $article->id]) }}"
            class="btn view-more float-end">{{ __("static.page.more") }}</a>
    </div>
</div>