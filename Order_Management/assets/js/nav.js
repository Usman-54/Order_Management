// assets/js/nav.js
// @ts-nocheck
document.addEventListener('DOMContentLoaded', () => {
  // Category filter
  const links = document.querySelectorAll('.nav-link[data-category]');
  const productCards = document.querySelectorAll('.product-card');

  links.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const category = link.getAttribute('data-category');

      productCards.forEach(card => {
        if (category === 'All' || card.getAttribute('data-category') === category) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // Search by title
  const menuSearch = document.getElementById('menuSearch');
  const searchInput = document.getElementById('searchInput');

  if (menuSearch && searchInput) {
    menuSearch.addEventListener('submit', function (e) {
      e.preventDefault(); // prevent page reload
      const query = searchInput.value.toLowerCase();

      productCards.forEach(card => {
        const titleEl = card.querySelector('.card-title');
        const title = titleEl ? titleEl.textContent.toLowerCase() : '';
        if (title.includes(query)) {
          card.style.display = 'block';
        } else {
          card.style.display = 'none';
        }
      });
    });
  }
});
