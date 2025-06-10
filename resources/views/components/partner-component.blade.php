<div class="card card-partner overflow-hidden">
	{{-- The container for the image --}}
	<div class="card-header p-0 d-flex align-items-center justify-content-center" style="height: 90px;">
		<a class="card-link" href="{{  $partner->link }}" target="_blank">
			<img src="{{ asset('images/partners/' . $partner->image) }}" class="card-img-top"
				style="width: 100%; height: 100%; object-fit: cover;" alt="{{  $partner->title }}" />
		</a>
	</div>
</div>