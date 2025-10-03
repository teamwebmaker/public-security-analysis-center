
// Load Fancybox dynamically when needed.
export function loadFancyboxCDN(callback) {
  // Prevent duplicate loading
  if (window.Fancybox) {
    callback && callback();
    return;
  }

  // Load CSS
  if (!document.querySelector('link[href*="fancybox.css"]')) {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css';
    document.head.appendChild(link);
  }

  // Load JS
  const script = document.createElement('script');
  script.src = 'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js';
  script.onload = () => {
    callback && callback(); // Run Fancybox.bind when ready
  };
  document.body.appendChild(script);
}

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
export function getById(id, parent = document) {
  return parent.getElementById(id);
}

/**
 * Get first element matching selector
 * @param {string} selector
 * @returns {Element|null}
 */
export function getOne(selector, parent = document) {
  return parent.querySelector(selector);
}

/**
 * Get all elements matching selector
 * @param {string} selector
 * @returns {NodeListOf<Element>}
 */
export function getAll(selector, parent = document) {
  return parent.querySelectorAll(selector);
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




/**
 * Format a date string to a human-readable format
 * @param {string} dateString - The date string to format
 * @returns {string} - The formatted date string
 * @example
 * formatDateTime('2022-01-01T12:30:00.000Z') // '2022-01-01 12:30'
 */
export const formatDateTime = (dateString) => {
  if (!dateString) return '-';
  
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const day = String(date.getDate()).padStart(2, '0');
  const hours = String(date.getHours()).padStart(2, '0');
  const minutes = String(date.getMinutes()).padStart(2, '0');
  return `${year}-${month}-${day} ${hours}:${minutes}`;
};