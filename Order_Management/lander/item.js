document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.add-product-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
      const id = btn.dataset.id;
      if (!id) return;

      try {
        // Fetch product overlay HTML
        const res = await fetch('productDetail.php?id=' + encodeURIComponent(id));
        const html = await res.text();

        // Remove old overlay if exists
        const oldOverlay = document.getElementById('productOverlay');
        if (oldOverlay) oldOverlay.remove();

        // Add overlay to body
        document.body.insertAdjacentHTML('beforeend', html);
        const overlay = document.getElementById('productOverlay');

        // Elements inside overlay
        const form = overlay.querySelector('.addToCartForm');
        const closeBtn = overlay.querySelector('#closeDetail');
        const totalPriceEl = overlay.querySelector('.totalPrice');
        const hiddenPrice = overlay.querySelector('.hiddenPrice');
        const basePrice = parseFloat(overlay.querySelector('.basePrice').dataset.base) || 0;
        const qtyInput = overlay.querySelector('.quantity');
        const sizeSelect = overlay.querySelector('.size');
        const drinkSelect = overlay.querySelector('.drink');
        const sauceSelect = overlay.querySelector('.sauce');
        const extras = overlay.querySelectorAll('.extra-option');

        // Close overlay
        closeBtn.addEventListener('click', () => overlay.remove());

        // Price calculation
        function getSelectedPrice(select){
          return parseFloat(select?.selectedOptions[0]?.dataset.price || 0);
        }

        function updateTotal(){
          let total = basePrice;
          total += getSelectedPrice(sizeSelect);
          total += getSelectedPrice(drinkSelect);
          total += getSelectedPrice(sauceSelect);
          extras.forEach(chk => { if(chk.checked) total += parseFloat(chk.dataset.price || 0); });
          const qty = parseInt(qtyInput.value) || 1;
          total *= qty;
          totalPriceEl.textContent = total.toFixed(2);
          hiddenPrice.value = total.toFixed(2);
        }

        [qtyInput, sizeSelect, drinkSelect, sauceSelect].forEach(el => {
          el.addEventListener('change', updateTotal);
          el.addEventListener('input', updateTotal);
        });
        extras.forEach(chk => chk.addEventListener('change', updateTotal));
        updateTotal();

        // Add to cart
        form.addEventListener('submit', async e => {
          e.preventDefault();
          const formData = new FormData(form);
          if (!formData.get('action')) formData.append('action', 'add');

          const res = await fetch('../lander/cart.php', { method: 'POST', body: formData });
          const html = await res.text();
          // Update only cart items
          const cartItems = document.getElementById('cartItems');
          if (cartItems) cartItems.innerHTML = html;

          // Update cart sidebar
          // const cartSidebar = document.getElementById('cartSidebar');
          // if (cartSidebar) cartSidebar.innerHTML = html;

          // Open cart
          if (typeof openCart === 'function') openCart();

          // Close overlay
          overlay.remove();
        });

      } catch (err) {
        console.error(err);
        alert('Failed to load product details.');
      }
    });
  });
});
//  for extra item in product detail page
document.addEventListener('DOMContentLoaded', function() {
  document.addEventListener('change', function(e) {
    const form = e.target.closest('form');
    if (!form) return;

    // extras
    const extras = Array.from(form.querySelectorAll('.extra-option:checked'))
      .map(opt => opt.value)
      .join(', ');
    const extrasInput = form.querySelector('.extrasInput');
    if (extrasInput) extrasInput.value = extras;

    // drink
    const drink = form.querySelector('.drink');
    if (drink) {
      const selectedDrink = drink.options[drink.selectedIndex]?.textContent || '';
      const drinkInput = form.querySelector('.drinkInput');
      if (drinkInput) drinkInput.value = selectedDrink;
    }

    // sauce
    const sauce = form.querySelector('.sauce');
    if (sauce) {
      const selectedSauce = sauce.options[sauce.selectedIndex]?.textContent || '';
      const sauceInput = form.querySelector('.sauceInput');
      if (sauceInput) sauceInput.value = selectedSauce;
    }
  });
});
