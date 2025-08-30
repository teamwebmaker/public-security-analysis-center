<div class="container-fluid pt-2">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between py-4">
            <h2 class="mb-3 mb-md-0">{{ __('static.section.articles.title') }}</h2>
            <!-- Fixed Filter Component -->
            @if ($articles->isNotEmpty())
                <x-sort-data name="sort" :selected="request()->query('sort', 'newest')" />
            @endif
        </div>

        @if ($articles->isEmpty())
            <x-ui.empty-state-message minHeight="60dvh" />
        @endif
        <!-- Responsive Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($articles as $article)
                <div class="col">
                    <x-card-component :title="json_decode($article->title)->$language"
                        :description="json_decode($article->description)->$language" :image="implode('/', ['images', $article->collection, $article->image])" :link="route(implode('.', [$article->collection, 'show']), ['id' => $article->id])" />
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($articles->hasPages())
            <div class="row mt-4">
                <div class="col-md-12 ">
                    {{ $articles->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>