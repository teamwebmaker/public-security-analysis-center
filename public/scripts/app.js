import './bootstrap/bootstrapTooltips.js';
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

// Header Sticky Navigation
const initStickyNavigation = () => {
    const navWrapper = document.querySelector('[data-sticky-nav]');
    const placeholder = document.querySelector('[data-sticky-placeholder]');

    if (!navWrapper || !placeholder) {
        return;
    }

    let scrollTicking = false;

    const setPlaceholderHeight = () => {
        placeholder.style.height = `${navWrapper.offsetHeight}px`;
    };

    const updateStickyState = () => {
        const shouldStick = placeholder.getBoundingClientRect().top <= 0;
        navWrapper.classList.toggle('is-sticky', shouldStick);

        if (shouldStick) {
            setPlaceholderHeight();
        } else {
            placeholder.style.height = '0px';
        }
    };

    const handleScroll = () => {
        if (scrollTicking) {
            return;
        }

        scrollTicking = true;
        requestAnimationFrame(() => {
            updateStickyState();
            scrollTicking = false;
        });
    };

    const handleResize = () => {
        requestAnimationFrame(() => {
            if (navWrapper.classList.contains('is-sticky')) {
                setPlaceholderHeight();
            } else {
                placeholder.style.height = '0px';
            }
            updateStickyState();
        });
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    window.addEventListener('resize', handleResize);
    window.addEventListener('orientationchange', handleResize);

    if ('ResizeObserver' in window) {
        const resizeObserver = new ResizeObserver(() => {
            if (navWrapper.classList.contains('is-sticky')) {
                setPlaceholderHeight();
            }
        });
        resizeObserver.observe(navWrapper);
    }

    updateStickyState();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initStickyNavigation, { once: true });
} else {
    initStickyNavigation();
}
