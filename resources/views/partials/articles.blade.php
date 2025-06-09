{{-- <div class="container-fluid">
    <div class="container">
        <!-- Title and Filter Row -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between py-4">
            <h2 class="mb-3 mb-md-0">{{ __('static.section.articles.title') }}</h2>
            <!-- Inline Filter (No Accordion) -->
            <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
                <select name="sort" id="sort"
                    class="form-select form-select-sm   text-dark  fs-6 border-secondary w-auto"
                    onchange="this.form.submit()">
                    <option value="newest" {{ request('sort')==='newest' ? 'selected' : '' }}>
                        ახალი &#8594; ძველი
                    </option>
                    <option value="oldest" {{ request('sort')==='oldest' ? 'selected' : '' }}>
                        ძველი &#8592; ახალი
                    </option>
                </select>
            </form>
        </div>

        <!-- Articles Grid -->
        <div class="row">
            @foreach($articles as $article)
            <div class="col-lg-4 col-md-6 mb-4">
                <x-article-component :article="$article" :language="$language" />
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="row">
            <div class="col-md-12">
                {!! $articles->withQueryString()->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    </div>
</div> --}}
<div class="container-fluid">
    <div class="container">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between py-4">
            <h2 class="mb-3 mb-md-0">{{ __('static.section.articles.title') }}</h2>

            <!-- Fixed Filter Component -->
            <x-sort-data name="sort" :options="[
        'newest' => 'Newest to Oldest',
        'oldest' => 'Oldest to Newest'
    ]"
                :selected="request()->query('sort', 'newest')"
                class="form-select form-select-sm text-dark fs-6 border-secondary w-auto" />
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