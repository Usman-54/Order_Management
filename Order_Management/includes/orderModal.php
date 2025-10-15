<div class="modal" id="orderModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title" id="modalTitle">New Order</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
<form id="orderForm">
    <!-- Auto-generated Customer ID -->
    <input type="hidden" id="customerId" name="customerId">

    <div class="form-group">
        <label for="customerName" class="form-label">Customer Name</label>
        <input type="text" id="customerName" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="billingEmail" class="form-label">Billing Email</label>
        <input type="email" id="billingEmail" class="form-control" required>
    </div>
    
    <!-- Product Selection -->
    <div class="form-group">
        <label for="products" class="form-label">Products</label>
        <select id="products" name="products[]" class="form-control" multiple="multiple" required></select>
    </div>
    
    <div class="form-group">
        <label for="quantity" class="form-label">Quantity</label>
        <input type="number" id="quantity" class="form-control" min="1" value="1" required>
    </div>
    
    <div class="form-group">
        <label for="amount" class="form-label">Amount ($)</label>
        <input type="number" id="amount" class="form-control" step="0.01" required>
    </div>

    <!-- Hidden field for current order date -->
    <input type="hidden" id="orderDate" name="orderDate">
</form>



</div>
<div class="modal-footer">
            <button class="btn btn-secondary close-modal">Cancel</button>
            <button class="btn btn-primary" id="saveOrder">Save Order</button>
        </div>
    </div>
</div>
<script>
  // Auto-generate customerId & current datetime
function generateCustomerId() {
  return Math.floor(1000 + Math.random() * 9000);
}
function getCurrentDateTime() {
  const now = new Date();
  return now.toISOString().slice(0, 19).replace("T", " "); // "YYYY-MM-DD HH:MM:SS"
}
document.addEventListener("DOMContentLoaded", function() {
  document.getElementById("customerId").value = generateCustomerId();
  document.getElementById("orderDate").value = getCurrentDateTime();
});

// Select2 for product search
$(document).ready(function() {
  $('#products').select2({
    placeholder: "Search and select products",
    minimumInputLength: 1,
    ajax: {
      url: 'backend/getProducts.php',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return { q: params.term };
      },
      processResults: function (data) {
        return data;
      },
      cache: true
    }
  });
});

</script>