<div class="card h-100 border-0 shadow-sm overflow-hidden d-flex flex-column">
    <!-- Optional image -->
    @if(isset($image) && $image)
        <div class="card-header p-0">
            <img src="{{ asset($image) }}" class="card-img-top response-img " alt="card image">
        </div>
    @endif


    <div class="card-body d-flex flex-column">
        <!-- Optional title -->
        @if(isset($title) && $title)
            <h3 class="card-title fs-5 fw-semibold mb-3 text-truncate">{{ $title }}</h3>
        @endif

        <!-- Optional description -->
        @if(isset($description) && $description)
            <div class="card-text mb-3 flex-grow-1"
                style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $description }}
            </div>
        @endif

        <!-- Footer content -->
        <div class="mt-auto">
            @if((isset($date) && $date) || (isset($link) && $link))
                <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                    <div>
                        @if(isset($date) && $date)
                            <small class="text-muted">{{ $date }}</small>
                        @endif
                    </div>
                    <div>
                        @if(isset($link) && $link)
                            <a href="{{ $link }}" class="btn btn-sm view-more">
                                <span>{{ __("static.page.more") }}</span>
                                <i class="bi bi-arrow-right-short "></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>