<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../backend/db.php");

// 🟥 Handle Delete Request
if (isset($_GET['delete']) && isset($_GET['customer_id'])) {
    $customer_id = intval($_GET['customer_id']);

    // Delete all orders first
    $deleteOrders = $conn->prepare("DELETE FROM orders WHERE customer_id = ?");
    $deleteOrders->bind_param("i", $customer_id);
    $deleteOrders->execute();

    // Delete customer record
    $deleteCustomer = $conn->prepare("DELETE FROM customers WHERE customer_id = ?");
    $deleteCustomer->bind_param("i", $customer_id);
    $deleteCustomer->execute();

    header("Location: index.php?deleted=1");
    exit;
}

// 🟢 Show Customer Detail
if (!isset($_GET['customer_id'])) {
    die("Customer ID not provided.");
}

$customer_id = intval($_GET['customer_id']);

// ✅ Fetch customer info
$query = $conn->prepare("
    SELECT u.name, u.email, c.phone_no, c.created_at
    FROM customers c
    JOIN users u ON c.user_id = u.user_id
    WHERE c.customer_id = ?
");
$query->bind_param("i", $customer_id);
$query->execute();
$customer = $query->get_result()->fetch_assoc();

if (!$customer) {
    die("Customer not found.");
}

// ✅ Fetch orders for this customer
$orderQuery = $conn->prepare("
    SELECT id AS order_id, order_date, order_time, final_price, status
    FROM orders
    WHERE customer_id = ?
    ORDER BY id DESC
");

$orderQuery->bind_param("i", $customer_id);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

$grandTotal = 0;
$orders = [];
$lastOrderDate = "No orders yet";

while ($order = $orderResult->fetch_assoc()) {
    $grandTotal += $order['final_price'];
    $orders[] = $order;
}

if (!empty($orders)) {
    $lastOrderDate = $orders[0]['order_date'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Detail</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3><i class="fa fa-user"></i> Customer Detail</h3>
    <div>
      <a href="index.php" class="btn btn-secondary btn-sm">
        <i class="fa fa-arrow-left"></i> Back
      </a>
      <button class="btn btn-danger btn-sm" onclick="deleteCustomer(<?= $customer_id ?>)">
        <i class="fa fa-trash"></i> Delete
      </button>
    </div>
  </div>

  <!-- Customer Info -->
<div class="card mb-4 shadow-sm">
  <div class="card-body p-0">
    <table class="table table-bordered mb-0 text-start">
      <thead class="table-primary" style="font-size: 1.1rem;">
        <tr>
          <th class="py-3">Personal Information</th>
          <th class="py-3">Dates</th>
        </tr>
      </thead>
      <tbody style="font-size: 1rem;">
        <tr>
          <td class="py-3">
            <strong>Name:</strong> <?= htmlspecialchars($customer['name']) ?><br>
            <strong>Email:</strong> <?= htmlspecialchars($customer['email']) ?><br>
            <strong>Phone:</strong> <?= htmlspecialchars($customer['phone_no']) ?>
          </td>
          <td class="py-3">
            <strong>Joined:</strong> <?= htmlspecialchars($customer['created_at']) ?><br>
            <strong>Last Order:</strong> <?= htmlspecialchars($lastOrderDate) ?>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</div>




  <!-- Orders -->
  <div class="card">
    <div class="card-header bg-dark text-white d-flex justify-content-between">
      <span>Order History</span>
      <span>Total: <strong>Rs <?= number_format($grandTotal, 2) ?></strong></span>
    </div>
    <div class="card-body p-0">
      <table class="table table-striped text-center mb-0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Order Date</th>
            <th>Order Time</th>
            <th>Price</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($orders): $i=1; foreach ($orders as $order): ?>
            <tr>
              <td><?= $i++ ?></td>
              <td><?= htmlspecialchars($order['order_date']) ?></td>
              <td><?= htmlspecialchars($order['order_time']) ?></td>
              <td>Rs <?= number_format($order['final_price'], 2) ?></td>
              <td><?= htmlspecialchars($order['status']) ?></td>
            </tr>
          <?php endforeach; else: ?>
            <tr><td colspan="5" class="text-muted">No orders found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  function deleteCustomer(id) {
    if (confirm('Are you sure you want to delete this customer and all their orders?')) {
      window.location.href = 'customer_detail.php?delete=1&customer_id=' + id;
    }
  }
</script>

</body>
</html>
