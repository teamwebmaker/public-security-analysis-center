<header class="page-header">
	<div class="container-fluid gold-bg py-2">
		<div class="container">
			<div class="row align-items-center justify-content-between">
				<!-- Left Side: Contact Info -->
				<div class="col-auto">
					<div class="d-flex align-items-center justify-content-between d-md-none gap-3">
						<a class="d-flex gap-2 align-items-center black-text text-decoration-none"
							href="tel:{{ $contactPhone }}">
							<i class="bi bi-telephone fs-5"></i>
							<span class="contact-option-label fs-6">
								{{ $contactPhone }}</span>
						</a>
						<!-- Toggle Icon -->
						<button class="btn p-0 border-0" type="button" data-bs-toggle="collapse"
							data-bs-target="#mobileEmail">
							<i class="bi bi-chevron-down fs-5"></i>
						</button>
					</div>

					<!-- Email collapses on mobile -->
					<div class="collapse d-md-none mt-2" id="mobileEmail">
						<a class="d-flex gap-2 align-items-center black-text text-decoration-none"
							href="mailto:psacge@gmail.com">
							<i class="bi bi-envelope fs-5"></i>
							<span class="contact-option-label fs-6">{{ $contactEmail }}</span>
						</a>
					</div>

					<!-- Desktop view: email & phone always visible -->
					<div class="d-none d-md-flex gap-4">
						<a class="contact-options d-flex gap-2 align-items-center black-text text-decoration-none"
							href="mailto:psacge@gmail.com">
							<i class="bi bi-envelope fs-5"></i>
							<span class="contact-option-label fs-6">{{ $contactEmail }}</span>
						</a>
						<a class="contact-options d-flex gap-2 align-items-center black-text text-decoration-none"
							href="tel:+995577416620">
							<i class="bi bi-telephone fs-5"></i>
							<span class="contact-option-label fs-6">{{ $contactPhone }}</span>
						</a>
					</div>
				</div>
				<div class="col-auto d-flex justify-content-end align-self-start gap-2">
					<a class="language-switcher {{ App::isLocale('ka') ? 'active-language' : '' }} text-decoration-none black-text"
						href="{{ route('lang.switch', 'ka') }}">
						KA
					</a>
					<a class="language-switcher {{ App::isLocale('en') ? 'active-language' : '' }} text-decoration-none black-text"
						href="{{ route('lang.switch', 'en') }}">
						EN
					</a>
				</div>
			</div>
		</div>
	</div>
	<div class="sticky-nav-placeholder black-bg" data-sticky-placeholder aria-hidden="true"></div>
	<div class="container-fluid black-bg sticky-nav" data-sticky-nav>
		<div class="container">
			<nav class="navbar navbar-expand-xl py-1">
				<div class="container-fluid">
					<!-- Logo -->
					<a class="navbar-brand" href="{{ route('home.page') }}">
						<img src="{{ asset('images/themes/logo-psac.png') }}" class="page-logo" alt="psac-main-logo" />
					</a>
					<!-- Menu Toggle Button -->
					<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
						aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
						<i class="bi bi-list gold-text"></i>
					</button>

					<div class="collapse navbar-collapse" id="navbarNav">
						<!-- Menu Items -->
						<ul class="navbar-nav justify-content-end w-100">
							@foreach($MainMenu as $menu_item)
								<li class="nav-item">
									<a class="nav-link truncate gold-text position-relative animated-line"
										href="{{ route($menu_item->link) }}">{{ $menu_item->title->$language}}</a>
								</li>
							@endforeach

							<li class="nav-item">
								@auth
									@if(Auth::user()->role->name !== 'admin')
										<div style="padding-block: 3px; padding-left: 10px;">
											<x-management.user-avatar-dropdown />
										</div>
									@else
										<a class="nav-link truncate gold-text position-relative animated-line"
											href="{{ route('login') }}">
											<i class="bi bi-person-bounding-box fs-5"></i>
										</a>
									@endif

								@else
									<a class="nav-link truncate gold-text position-relative animated-line"
										href="{{ route('login') }}">
										<i class="bi bi-person-bounding-box fs-5"></i>
									</a>
								@endauth
							</li>
						</ul>
					</div>
				</div>
			</nav>
		</div>
	</div>

</header>