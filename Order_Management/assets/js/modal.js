//@ts-check
const addOrderBtn = document.getElementById('addOrderBtn');
const orderModal = document.getElementById('orderModal');
const closeModalBtns = document.querySelectorAll('.close-modal');
const saveOrderBtn = document.getElementById('saveOrder');
const modalTitle = document.getElementById('modalTitle');

// Open modal for adding new order
addOrderBtn.addEventListener('click', () => {
    modalTitle.textContent = 'Add New Order';
    orderModal.style.display = 'flex';
});

// Close modal
closeModalBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        orderModal.style.display = 'none';
    });
});

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    if (e.target === orderModal) {
        orderModal.style.display = 'none';
    }
});
