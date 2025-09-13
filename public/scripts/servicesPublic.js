import { enableSmoothScroll, getOne, getById, getAll } from './heplers.js';

// Dom elements
const scrollLink = getOne('scroll-link') // scroll related

// Smooth scroll 
if (scrollLink) {
   scrollLink.addEventListener('click', function (e) {
      e.preventDefault();
      enableSmoothScroll(scrollLink)
   });
}
