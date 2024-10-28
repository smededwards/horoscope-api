// Import styles
import '../scss/style.scss';

/**
 * Add active class to the clicked day and remove it from the others.
 */
document.addEventListener('DOMContentLoaded', () => {
  // Select all navigation links and day elements
  const navLinks = document.querySelectorAll('.horoscope__nav-link');
  const days = document.querySelectorAll('.horoscope__days');
  const nav = document.querySelector('.horoscope__nav');

  // Ensure the navigation element exists before attempting to attach event listeners
  if (!nav) {
    console.error('Navigation element not found');
    return; // Exit if the nav element is not found
  }

  // Function to toggle the active class on elements
  const toggleActiveClass = (elements, activeElement, activeClass) => {
    elements.forEach((element) => {
      // Add active class to the active element, remove it from others
      element.classList.toggle(activeClass, element === activeElement);
    });
  };

  // Function to handle the click event on navigation links
  const handleLinkClick = (event) => {
    // Find the closest navigation link that was clicked
    const link = event.target.closest('.horoscope__nav-link');
    if (!link) return; // Exit if no link was clicked

    const day = link.dataset.day; // Get the day from the clicked link's data attribute

    // Update active navigation link and corresponding day
    toggleActiveClass(navLinks, link, 'horoscope__nav-link--active'); // Update nav links
    toggleActiveClass(days, Array.from(days).find((dayElement) => dayElement.dataset.day === day), 'horoscope__days--active'); // Update days
  };

  // Attach the click event to the navigation element
  nav.addEventListener('click', handleLinkClick);
});
