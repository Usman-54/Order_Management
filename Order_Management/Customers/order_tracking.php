<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) session_start();

// Include database connection using absolute path
include __DIR__ . "/../backend/db.php";

// Check if customer is logged in
// Check if customer is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$customerId = $_SESSION['user_id'];
$error = '';

// Fetch the latest order for this customer
$stmt = $conn->prepare("
    SELECT id, cart, status 
    FROM orders 
    WHERE customer_id = ? 
    ORDER BY order_date DESC, order_time DESC 
    LIMIT 1
");
$stmt->bind_param("i", $customerId);
$stmt->execute();
$stmt->bind_result($orderId, $cartJson, $orderStatus);
$stmt->fetch();
$stmt->close();

// Check if there is an order
if (!$orderId) {
    $error = "You have no orders yet.";
} else {
    // Decode cart JSON from database
    $cart = json_decode($cartJson, true);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Tracking</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="../assets/css/modal.css">
  <link rel="stylesheet" href="../assets/css/customer_sidebar.css">
</head>
<body>

  <?php include __DIR__ . '/../includes/header.php'; ?>

  <div class="container">
    <div class="main-content">

      <?php include 'customer_sidebar.php'; ?>

      <main class="content mt-4">
        

        <?php if ($error): ?>
          <div class="alert alert-warning"><?= htmlspecialchars($error) ?></div>
        <?php else: ?>
          <div class="row g-4">
            <!-- LEFT: Order Status -->
            <div class="col-lg-7">
              <?php
              // Pass order ID to order_view_status.php
              $_GET['id'] = $orderId;

              // Include order_view_status.php with correct DB path
              include __DIR__ . "/../lander/Checkout/order_view_status.php";
              ?>
            </div>

            <!-- RIGHT: Cart / Order Summary -->
            
              <?php
              // Pass cart to right_checkout_cart.php
            //   $cartForSidebar = $cart;

              // Include right_checkout_cart.php with correct DB path
              include __DIR__ . "/../lander/Checkout/right_checkout_cart.php";
              ?>
            
          </div>
        <?php endif; ?>
      </main>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/search.js"></script>
  <script src="../lander/Checkout/assets/order_status.js"></script>

</body>
</html>
