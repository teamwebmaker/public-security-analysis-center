<div class="container-fluid pt-2">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between py-4">
            <h2 class="mb-3 mb-md-0">{{ __('static.section.articles.title') }}</h2>

            <!-- Fixed Filter Component -->
            <x-sort-data name="sort" :selected="request()->query('sort', 'newest')" />
        </div>

        <!-- Responsive Grid -->
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @forelse($articles as $article)
                <div class="col">
                    <x-article-component :article="$article" :language="$language" />
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">{{ __('No articles found') }}</div>
                </div>
            @endforelse
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