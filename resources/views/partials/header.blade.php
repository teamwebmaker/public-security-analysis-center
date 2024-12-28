<header class="page-header">
    <div class="container-fluid gold-bg py-2">
        <div class="container">
            <div class="row">
                <div class="col-6 d-flex gap-4">
                    <a class="contact-options d-flex gap-2 align-items-center black-text text-decoration-none" href="mailto:psacge@gmail.com">
                        <i class="bi bi-envelope fs-5"></i>
                        <span class="contact-option-label fs-6">
                             psacge@gmail.com
                        </span>
                    </a>
                    <a class="contact-options d-flex gap-2 align-items-center black-text text-decoration-none" href="tel:+995577416620">
                        <i class="bi bi-telephone fs-5 "></i>
                        <span class="contact-option-label fs-6">
                             577416620
                        </span>
                    </a>
                </div>
                <div class="col-6 d-flex justify-content-end gap-2">
                    <a class="language-switcher  active-language text-decoration-none black-text"  href="#">
                        KA
                    </a>
                    <a class="language-switcher text-decoration-none black-text"  href="#">
                        EN
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid black-bg">
        <div class="container">
            <nav class="navbar navbar-expand-xl py-1">
                <div class="container-fluid">
                    <a class="navbar-brand" href="{{ route('home.page') }}">
                        <img src="{{ asset('images/themes/psac-logo-450x150.png') }}" class="page-logo"/>
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="bi bi-list gold-text"></i>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav justify-content-end w-100">
                            @foreach($MainMenu as $menu_item)
                                <li class="nav-item">
                                    <a class="nav-link truncate gold-text position-relative animated-line"  href="{{ route($menu_item->link) }}">{{ $menu_item->title->ka}}</a>
                                </li>
                            @endforeach
                                <li class="nav-item">
                                    <a class="nav-link truncate gold-text position-relative animated-line"  href="#">
                                        <i class="bi bi-person-bounding-box fs-5"></i>
                                    </a>
                                </li>
                        </ul>
                    </div>
                </div>
            </nav>
        </div>
    </div>

</header>
