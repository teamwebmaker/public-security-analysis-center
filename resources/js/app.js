import './bootstrap';
import { enableSmoothScroll } from './heplers';


// Services page: Smooth scroll 
const scrollLink = document.querySelector('scroll-link')

if (scrollLink) {
    scrollLink.addEventListener('click', function (e) {
        e.preventDefault();
       enableSmoothScroll(scrollLink)
    });
}