export const partnersSliderParams = {
    // slidesPerView: 2,
    slidesPerView: 'auto',
    speed: 1000,
    autoplay: {
        delay: 3000,
    },
    spaceBetween: 30,
    // spaceBetween: 16,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
}
// Initialize Swiper
new Swiper(".partners", partnersSliderParams);


// Bootstrap initialize tooltips
const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
});