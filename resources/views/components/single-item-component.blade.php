<div class="card mb-3 border-0">
    <div class="row g-0">
        <div class="col-md-4">
            <img src="{{ asset(implode('/', ['images', $category, $item -> image])) }}" class="img-fluid rounded-start" alt="...">
        </div>
        <div class="col-md-8">
            <div class="card-body">
                <h5 class="card-title">
                    {{$item -> title -> $language}}
                </h5>
                <p class="card-text">
                    {{$item -> description -> $language}}
                </p>
            </div>
        </div>
    </div>
</div>
