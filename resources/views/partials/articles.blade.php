<div class="container-fluid">
    <div class="container">
        <h2 class="py-4 ">{{ __('static.section.articles.title') }}</h2>
        <div class="row">
            @foreach($articles as $article)
                <div class="col-lg-4 col-md-6 mb-4">
                    <x-article-component :article="$article" :language="$language" />
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-12">
                {!! $articles->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div>