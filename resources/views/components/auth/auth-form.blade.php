<div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <div class="text-center mb-4">
        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center pe-1 m-auto mb-2"
            style="width: 50px; height: 50px;">
            <i class="bi bi-box-arrow-in-right fs-4"></i>
        </div>
        <h3 class="fw-bold"> {{$type == 'login' ? 'შესვლა' : 'რეგისტრაცია'}}</h3>
    </div>
    <form method="POST" action="{{ route($route) }}" class="needs-validation" novalidate>
        @csrf
        {{ $slot }}
        <button type="submit" class="btn btn-primary w-100">{{$type == 'login' ? 'შესვლა' : 'რეგისტრაცია'}}</button>
    </form>
</div>