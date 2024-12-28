<div class="card card-article">
    <div class="card-body">
        <span class="mb-4">
            <h5 class="card-title bold fs-4 mb-2">{{ $service->title->$language }}</h5>
        </span>
        <span>
            <p class="card-text fs-6 service-desc">
                {{ Str::limit($service->description->$language, 250) }}
            </p>
        </span>
        <a href="#" class="btn view-more float-end">
            გაიგე მეტი
            <span aria-hidden="true">→</span>
        </a>
    </div>
</div>
