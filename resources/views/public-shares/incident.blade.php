<!doctype html>
<html lang="ka">
<head>
    @include('partials.head')
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $incident->title }}</title>
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                                <i class="bi bi-file-earmark-text fs-3"></i>
                            </div>
                            <div>
                                <div class="text-muted small">გაზიარებული ინციდენტის დოკუმენტი</div>
                                <h1 class="h4 mb-0">{{ $incident->title }}</h1>
                            </div>
                        </div>

                        <dl class="row mb-4">
                            <dt class="col-sm-3">კომპანია</dt>
                            <dd class="col-sm-9">{{ $incident->branch->company?->name }}</dd>
                            <dt class="col-sm-3">ფილიალი</dt>
                            <dd class="col-sm-9">{{ $incident->branch->name }}</dd>
                        </dl>

                        <a href="{{ route('public-shares.document', $share->token) }}" class="btn btn-primary">
                            <i class="bi bi-download me-1"></i>
                            დოკუმენტის ჩამოტვირთვა
                        </a>

                        <p class="text-muted small mt-4 mb-0">
                            ეს ბმული განკუთვნილია მხოლოდ დოკუმენტის ნახვისთვის და ხელმოწერას არ ადასტურებს.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
