<div class="container-fluid">
    <div class="container">
        <div class="swiper partners">
            <div class="swiper-wrapper">
                @foreach($partners as $partner)
                    <div class="swiper-slide">
                        <x-partner-component :partner="$partner" />
                    </div>
                @endforeach
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
