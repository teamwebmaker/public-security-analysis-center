@props(['placeholder' => 'Search...'])

<div class="col d-flex justify-content-center  justify-content-md-end">
    <form method="GET">
        <div class="row row-cols-1 row-cols-sm-auto g-4 m-0">
            <div class="col m-sm-0">
                <input name="filter[search]" value="{{ request('filter.search') }}" type="text"
                    class="form-control w-100" placeholder="ძიება...">
                @if(request('filter.search'))
                    <p class="text-muted m-0 align-self-start pt-2">საძიებო სიტყვა:
                        <strong>{{ request('filter.search') }}</strong>
                    </p>
                @endif
            </div>
            <div class="row mt-2 mt-sm-0 g-2">
                <div class="col m-sm-0">
                    <button type="submit" class="btn btn-primary w-100">ძიება</button>
                </div>
                <div class="col m-sm-0 ">
                    {{-- {{ route('management.dashboard.tasks') }} --}}
                    <a href="#" class="btn btn-danger w-100">
                        <i class="bi bi-trash-fill"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>