<!doctype html>
<html lang="ka">
<head>
    @include('partials.head')
    <meta name="robots" content="noindex,nofollow">
    <title>{{ $instruction->name }}</title>
</head>
<body class="bg-light">
    <main class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                                <i class="bi bi-person-video3 fs-3"></i>
                            </div>
                            <div>
                                <div class="text-muted small">გაზიარებული ინსტრუქტაჟის დოკუმენტი</div>
                                <h1 class="h4 mb-0">{{ $instruction->name }}</h1>
                            </div>
                        </div>

                        @if ($instruction->link)
                            <div class="mb-4">
                                <div class="text-muted small mb-1">ვიდეო</div>
                                <a href="{{ $instruction->link }}" target="_blank" rel="noopener">
                                    {{ $instruction->link }}
                                </a>
                            </div>
                        @endif

                        <a href="{{ route('public-shares.document', $share->token) }}" class="btn btn-primary">
                            <i class="bi bi-download me-1"></i>
                            დოკუმენტის ჩამოტვირთვა
                        </a>

                        <p class="text-muted small mt-4 mb-0">
                            ბმული მოქმედებს მანამ, სანამ დოკუმენტი საჯაროდ არის გაზიარებული.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
