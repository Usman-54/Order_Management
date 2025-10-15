document.addEventListener('DOMContentLoaded', function() {

  // open/close (exposed globally)
  window.openCart = function() {
    const el = document.getElementById('cartSidebar');
    if (el) el.style.transform = 'translateX(0)';
  };
  window.closeCart = function() {
    const el = document.getElementById('cartSidebar');
    if (el) el.style.transform = 'translateX(100%)';
  };

  // helper: sync floating "View order" pill from cart DOM
  window.syncViewOrderTotal = function() {
    // try to read machine-readable subtotal
    const subtotalEl = document.getElementById('cartSubtotal');
    let total = null;
    let count = 0;

    if (subtotalEl && subtotalEl.dataset && subtotalEl.dataset.total) {
      total = subtotalEl.dataset.total;
      count = subtotalEl.dataset.count || 0;
    } else {
      // fallback: parse subtotal visible text inside #cartItems
      const el = document.querySelector('#cartItems .text-end strong');
      if (el) {
        const m = el.textContent.match(/[\d,.]+/);
        if (m) total = m[0].replace(',', '');
      }
    }

    const badge = document.getElementById('viewOrderTotal');
    if (badge && total != null) {
      badge.textContent = '$' + Number(total).toFixed(2);
    }

    // optional: hide viewOrderBtn when total is 0
    const viewBtn = document.getElementById('viewOrderBtn');
    if (viewBtn) {
      if (total && parseFloat(total) > 0) viewBtn.style.display = 'block';
      else viewBtn.style.display = 'none';
    }
  };

  // initial sync on page load
  syncViewOrderTotal();

  // Handle product/modal form adds (forms must have class "addToCartForm")
  document.addEventListener('submit', async function(e) {
    const form = e.target;
    if (!form || !form.matches('.addToCartForm')) return;

    e.preventDefault();
    const formData = new FormData(form);
    if (!formData.get('action')) formData.append('action', 'add');

    try {
      const res = await fetch('cart.php', { method: 'POST', body: formData });
      const html = await res.text();

      const cartItems = document.getElementById('cartItems');
      if (cartItems) {
        cartItems.innerHTML = html;
        // sync pill
        syncViewOrderTotal();
      }
      openCart();
    } catch (err) {
      console.error('Add to cart failed:', err);
      alert('Failed to add to cart.');
    }
  });

  // update qty
  window.updateCart = async function(action, index) {
    try {
      const url = `cart.php?update=${encodeURIComponent(action)}&index=${encodeURIComponent(index)}`;
      const res = await fetch(url);
      const html = await res.text();
      const cartItems = document.getElementById('cartItems');
      if (cartItems) {
        cartItems.innerHTML = html;
        syncViewOrderTotal();
      }
      openCart();
    } catch (err) {
      console.error('updateCart failed:', err);
    }
  };

  // remove item
  window.removeCart = async function(index) {
    try {
      const url = `cart.php?remove=1&index=${encodeURIComponent(index)}`;
      const res = await fetch(url);
      const html = await res.text();
      const cartItems = document.getElementById('cartItems');
      if (cartItems) {
        cartItems.innerHTML = html;
        syncViewOrderTotal();
      }
      openCart();
    } catch (err) {
      console.error('removeCart failed:', err);
    }
  };

  // recommended add
  window.addRecommendedToCart = async function(id) {
    try {
      const res = await fetch('cart.php', {
        method: 'POST',
        body: new URLSearchParams({ action: 'add', id: id, quantity: 1 })
      });
      const html = await res.text();
      const cartItems = document.getElementById('cartItems');
      if (cartItems) {
        cartItems.innerHTML = html;
        syncViewOrderTotal();
      }
      openCart();
    } catch (err) {
      console.error('addRecommendedToCart failed:', err);
      alert('Failed to add recommended item.');
    }
  };

}); // DOMContentLoaded
