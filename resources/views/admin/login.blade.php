<!doctype html>
<html lang="en">

<head>
    @include('partials.head')
</head>

<body data-languge="en">
    <div class="container-fluid py-5">
        <div class="container">
            <h2 class="card-title text-center mb-4 fw-bold text-secondary">
                <i class="bi bi-door-open-fill"></i>
                <span class="title-label">
                    ავტორიზაცია
                </span>
            </h2>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form method="POST" action="{{ route('admin.auth') }}">
                        @csrf
                        <div class="mb-3">
                            <input type="email" class="form-control" placeholder="Email" name="email" />
                            @session('email')
                                <div class="alert alert-danger mt-2" role="alert">
                                    {{ $value }}
                                </div>
                            @endsession
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" placeholder="Password" name="password">
                            @session('password')
                                <div class="alert alert-danger mt-2" role="alert">
                                    {{ $value }}
                                </div>
                            @endsession
                        </div>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
</body>

</html>