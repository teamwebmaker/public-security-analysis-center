const partnersSliderParams = {
    slidesPerView: 2,
    speed: 1000,
    autoplay: {
        delay: 3000,
    },
    spaceBetween: 40,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    breakpoints: {
        600:{
            slidesPerView: 2
        },
        900: {
            slidesPerView: 3
        },
        1200: {
            slidesPerView: 4
        },
       1400: {
            slidesPerView: 5
        }
    }
}
