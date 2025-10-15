document.addEventListener("DOMContentLoaded", function () {

  // View Customer
  document.querySelectorAll(".viewBtn").forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.getAttribute("data-id");
      window.location.href = `customer_detail.php?customer_id=${id}`;
    });
  });

  // Delete Customer
  document.querySelectorAll(".deleteBtn").forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.getAttribute("data-id");
      if (confirm("Are you sure you want to delete this customer and all their orders?")) {
        window.location.href = `customer_detail.php?delete=1&customer_id=${id}`;
      }
    });
  });

});
