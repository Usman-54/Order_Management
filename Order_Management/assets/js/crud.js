saveOrderBtn.addEventListener('click', () => {
    // In a real application, this would send data to the server
    alert('Order saved successfully!');
    orderModal.style.display = 'none';
});

// Edit and View buttons functionality
const editButtons = document.querySelectorAll('.action-btn.edit');
const viewButtons = document.querySelectorAll('.action-btn.view');

editButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        modalTitle.textContent = 'Edit Order';
        orderModal.style.display = 'flex';
        // In a real application, this would pre-fill the form with order data
    });
});

viewButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        alert('View order details - This would open a read-only view in a real application');
    });
});