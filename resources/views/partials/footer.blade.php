<footer class="bg-dark text-light">
	<div class="py-5 mt-5 container">
		<div class="row align-items-center justify-content-center gy-4">
			<!-- Logo and Attribution -->
			<div class="col-lg-4 align-self-center text-center text-lg-start">
				<a href="{{ route('home.page') }}">
					<img src="{{ asset('images/themes/psac-logo-450x150.png') }}" alt="psac-main-logo"
						class="img-fluid mb-2 mx-auto mx-lg-0 d-block" />
				</a>


				<!-- Social Icons -->
				<div class="d-flex justify-content-center justify-content-lg-start gap-3 ps-2 mb-3">
					<a href="https://www.facebook.com/profile.php?id=61552198050161" target="_blank" class="gold-text fs-5"
						title="Facebook">
						<i class="bi bi-facebook"></i>
					</a>
					<a href="https://www.instagram.com/psacge?igsh=ZTE4OG5lZzBlNHBr" target="_blank" class="gold-text fs-5"
						title="Instagram">
						<i class="bi bi-instagram"></i>
					</a>
					<a href="https://www.linkedin.com/in/giorgi-gratiashvili-9809481bb" target="_blank"
						class="gold-text fs-5" title="Linkedin">
						<i class="bi bi-linkedin"></i>
					</a>
					<a href="https://www.youtube.com/@psac-ge" target="_blank" class="gold-text fs-5" title="Youtube">
						<i class="bi bi-youtube"></i>
					</a>
				</div>
			</div>

			<!-- Navigation Links -->
			<div class="col-lg-5  align-self-start">
				<div class="w-75 m-auto">
					<h6 class="fw-bold gold-text text-center text-lg-start mb-3">ბმულები</h6>
					<div
						class="d-flex flex-wrap gap-2 col-5 col-lg-12 m-auto m-lg-0 justify-content-center justify-content-lg-start">
						@foreach($MainMenu as $menu_item)
							<a href="{{ route($menu_item->link) }}" class="text-white btn-sm text-decoration-none px-1">
								<span class="position-relative z-1">{{ $menu_item->title->ka }}</span>
							</a>
						@endforeach
					</div>
				</div>

			</div>

			<!-- Contact Info -->
			<div class="col-lg-3 text-center text-lg-start align-self-start">
				<div class="m-auto m-lg-0 ms-lg-auto" style="width: max-content;">
					<h6 class="text-uppercase fw-bold gold-text mb-3">კონტაქტი</h6>
					<ul class="list-unstyled small text-center text-lg-end ">
						<li class="mb-3 d-flex justify-content-center justify-content-lg-start align-items-center gap-2">
							<i class="bi bi-telephone-fill gold-text"></i>
							<span>+995 595 401 188</span>
						</li>
						<li class="mb-3 d-flex justify-content-center justify-content-lg-start align-items-center gap-2">
							<i class="bi bi-envelope-fill gold-text"></i>
							<a href="mailto:psacge@gmail.com" class="text-white text-decoration-none">psacge@gmail.com</a>
						</li>
						<li class="d-flex justify-content-center justify-content-lg-start align-items-center gap-2">
							<i class="bi bi-geo-alt-fill gold-text"></i>
							<span>საქართველო, თბილისი.</span>
						</li>
					</ul>
				</div>

			</div>
		</div>
	</div>

	<!-- Bottom Bar -->
	<div class="py-3 border-top border-1 border-secondary ">
		<div class="container">
			<div class="row text-center text-md-start align-items-center">
				<div class="col-md-9 mb-2 mb-md-0">
					<small class="gold-text d-block">Created By Webmaker.team</small>
				</div>
				<div class="col-md-3 text-md-end">
					<small class="gold-text d-block ">© {{ date('Y') }} ყველა უფლება დაცულია</small>

				</div>
			</div>
		</div>
	</div>
</footer>