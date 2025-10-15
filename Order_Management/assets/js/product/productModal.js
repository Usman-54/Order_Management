// @ts-nocheck
const modal = document.getElementById('addProductModal');
const openBtn = document.getElementById('addProductBtn');
const closeBtn = document.getElementById('closeModal');
const cancelBtn = document.getElementById('cancelModal');

// Add Category Section Elements
const addCategoryBtn = document.getElementById('addCategoryBtn');
const categorySection = document.getElementById('categorySection');
const cancelCategoryBtn = document.getElementById('cancelCategoryBtn');
const saveCategoryBtn = document.getElementById('saveCategoryBtn');
const newCategoryName = document.getElementById('newCategoryName');
const categorySelect = document.getElementById('category');
const productFields = document.getElementById('productFields');

// Open Product Modal
openBtn.addEventListener('click', () => {
  modal.style.display = 'flex';
});

// Close Product Modal
closeBtn.addEventListener('click', () => modal.style.display = 'none');
cancelBtn.addEventListener('click', () => modal.style.display = 'none');
window.addEventListener('click', (e) => { 
  if (e.target === modal) modal.style.display = 'none'; 
});

// Show Add Category Section
addCategoryBtn.addEventListener('click', () => {
  categorySection.style.display = 'block';
  productFields.style.display = 'none';
  newCategoryName.value = '';
  newCategoryName.focus();
});

// Cancel Add Category
cancelCategoryBtn.addEventListener('click', () => {
  categorySection.style.display = 'none';
  productFields.style.display = 'block';
});

// Save New Category via AJAX
saveCategoryBtn.addEventListener('click', () => {
  const name = newCategoryName.value.trim();
  if (!name) return alert('Category name cannot be empty');

  // ✅ Correct URL relative to index.php
  fetch('modules/add_categories.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'name=' + encodeURIComponent(name)
  })
  .then(res => res.json())
  .then(res => {
    if (res.success) {
      // Add new category to select and auto-select it
      const option = new Option(res.name, res.category_id, true, true);
      categorySelect.append(option);
      categorySelect.value = res.category_id;

      // Hide Add Category Section
      categorySection.style.display = 'none';
      productFields.style.display = 'block';
      alert('Category added successfully!');
    } else {
      alert(res.message);
    }
  })
  .catch(err => {
    console.error(err);
    alert('Error adding category. Please try again.');
  });
});

// Initialize Select2 for category search
$(document).ready(function() {
  $('#category').select2({
    placeholder: "Search for category",
    width: '100%',
    allowClear: true
  });
});

// Optional: Search Category button click (focus on select2)
const searchCategoryBtn = document.getElementById('searchCategoryBtn');
if (searchCategoryBtn) {
  searchCategoryBtn.addEventListener('click', () => {
    $('#category').select2('open');
  });
}
