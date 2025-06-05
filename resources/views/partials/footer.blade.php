<footer class="bg-dark text-light py-5 mt-5">
	<div class="container">
		<div class="row align-items-center gy-4">

			<!-- Logo and Attribution -->
			<div class="col-lg-4 text-center text-lg-start align-self-start">
				<a href="{{ route('home.page') }}">
					<img src="{{ asset('images/themes/psac-logo-450x150.png') }}" alt="psac-main-logo"
						class="img-fluid mb-3" />
				</a>
				<div class="ps-2">
					<small class="d-block gold-text">Created By Webmaker.team</small>
					<small class="d-block gold-text">© {{ date('Y') }} ყველა უფლება დაცულია</small>
				</div>
			</div>

			<!-- Navigation Links -->
			<div class="col-lg-4 align-self-start">
				<h6 class="text-uppercase fw-bold gold-text text-center text-lg-start mb-3 ">ბმულები</h6>
				<div
					class="d-flex flex-wrap gap-2 col-5 col-lg-12  m-auto m-lg-0 justify-content-center justify-content-lg-start">
					<!-- Pill-shaped navigation items -->
					@foreach($MainMenu as $menu_item)
						<a href="#" class="text-white btn-sm text-decoration-none px-1">
							<span class="position-relative z-1">{{ $menu_item->title->$language }}</span>
							<span
								class="position-absolute top-0 start-0 w-100 h-100 bg-warning opacity-0 hover-opacity-10 transition-all"></span>
						</a>
					@endforeach

				</div>
			</div>
			<!-- Contact Info -->
			<div class="col-lg-4 text-center text-lg-start align-self-start">
				<h6 class="text-uppercase fw-bold gold-text mb-3">კონტაქტი</h6>
				<ul class="list-unstyled small">
					<li
						class="mb-3 d-flex  flex-row justify-content-center justify-content-lg-start align-items-center gap-2">
						<i class="bi bi-telephone-fill gold-text"></i>
						<span>+995 577 416 620</span>
					</li>
					<li
						class="mb-3 d-flex flex-row justify-content-center justify-content-lg-start align-items-center gap-2">
						<i class="bi bi-envelope-fill gold-text"></i>
						<a href="mailto:psacge@gmail.com" class="text-white text-decoration-none">psacge@gmail.com</a>
					</li>
					<li
						class="d-flex flex-row justify-content-center justify-content-lg-start align-items-center gap-2 text-center text-lg-start">
						<i class="bi bi-geo-alt-fill gold-text"></i>
						<span>საქართველო, თბილისი.</span>
					</li>
				</ul>
			</div>

		</div>
	</div>
</footer>