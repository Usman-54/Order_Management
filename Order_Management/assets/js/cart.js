// Add from forms (product detail, etc.)
document.addEventListener('submit', async function(e) {
  const form = e.target;
  if (!form.matches('.addToCartForm')) return;

  e.preventDefault();
  const formData = new FormData(form);
  if (!formData.get('action')) formData.append('action', 'add');

  const res = await fetch('cart.php', { method: 'POST', body: formData });
  const html = await res.text();

  const cartItems = document.getElementById('cartItems');
  if (cartItems) cartItems.innerHTML = html;

  openCart();
});

// Increase / decrease qty
async function updateCart(action, index) {
  const url = `cart.php?update=${action}&index=${index}`;
  const res = await fetch(url);
  const html = await res.text();
  document.getElementById('cartItems').innerHTML = html;
  openCart();
}

// Remove item
async function removeCart(index) {
  const url = `cart.php?remove=1&index=${index}`;
  const res = await fetch(url);
  const html = await res.text();
  document.getElementById('cartItems').innerHTML = html;
  openCart();
}

// Recommended
async function addRecommendedToCart(id) {
  const res = await fetch('cart.php', {
    method: 'POST',
    body: new URLSearchParams({ action: 'add', id: id, quantity: 1 })
  });
  const html = await res.text();
  document.getElementById('cartItems').innerHTML = html;
  openCart();
}

// Sidebar open/close
function openCart() {
  const el = document.getElementById('cartSidebar');
  if (el) el.style.transform = 'translateX(0)';
}
function closeCart() {
  const el = document.getElementById('cartSidebar');
  if (el) el.style.transform = 'translateX(100%)';
}
