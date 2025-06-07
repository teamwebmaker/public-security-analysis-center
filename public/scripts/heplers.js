export function enableSmoothScroll(selector, offset = 100) {
  document.querySelectorAll(selector).forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        window.scrollTo({
          top: target.offsetTop - offset,
          behavior: 'smooth'
        });
      }
    });
  });
}


// Select from dom 

/**
 * Get element by ID
 * @param {string} id
 * @returns {HTMLElement|null}
 */
export function getElementById(id) {
  return document.getElementById(id);
}

/**
 * Get first element matching selector
 * @param {string} selector
 * @returns {Element|null}
 */
export function getOne(selector) {
  return document.querySelector(selector);
}

/**
 * Get all elements matching selector
 * @param {string} selector
 * @returns {NodeListOf<Element>}
 */
export function getAll(selector) {
  return document.querySelectorAll(selector);
}

/**
 * Smooth scroll to element by selector or ID
 * @param {string} selector
 */
export function scrollToElement(selector) {
  const element = getOne(selector.startsWith('#') ? selector : `#${selector}`);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
  }
}