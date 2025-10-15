<?php
include("../../backend/db.php");
if (session_status() === PHP_SESSION_NONE)
    session_start();

$cart = $_SESSION['cart'] ?? [];
$totalPrice = 0;
foreach ($cart as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

$discountThreshold = 2000;
$discountRate = 0.10;
$discount = ($totalPrice > $discountThreshold) ? $totalPrice * $discountRate : 0;
$finalPrice = $totalPrice - $discount;

// Optional: if already has an order session
$orderId = $_SESSION['current_order_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.js"></script>
    <link rel="stylesheet" href="../../assets/css/checkout.css">
</head>

<body>
    <?php include '../header.html'; ?>

    <div class="checkout-container container mt-4">
        <div class="row g-4">

            <!-- LEFT SIDE -->
            <div class="col-lg-7">
                <div id="checkout-left">
                    <h4>Customer Details</h4>
                    <form id="checkout-form">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-4">
                                <input type="email" class="form-control" name="email" placeholder="Email" required>
                            </div>
                            <div class="col-md-4">
                                <input type="tel" class="form-control" name="phone" placeholder="Phone" required>
                            </div>
                        </div>

                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label class="form-label">Order Date</label>
                                <input type="text" id="delivery_date" class="form-control" name="delivery_date" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Order Time</label>
                                <input type="text" id="delivery_time" class="form-control" name="delivery_time" required>
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label">Instructions / Notes</label>
                            <textarea class="form-control" name="notes" placeholder="Add a note"></textarea>
                        </div>

                        <div class="mt-3 d-flex gap-2">
                            <button type="button" id="place-order-btn" class="btn btn-danger btn-lg">
                                Place Order
                            </button>
                            <a href="../index.php" class="btn btn-secondary btn-lg">Close</a>
                        </div>
                    </form>
                </div>

                <!-- Order View (AJAX-loaded after placing order) -->
                <div id="order-view" class="mt-3 d-none"></div>
            </div>

            <!-- RIGHT SIDE: Cart Summary -->
            <?php include 'right_checkout_cart.php'; ?>

        </div>
    </div>

    <script src="./assets/checkout.js"></script>
    <script src="../../assets/js/nav.js"></script>
    <script>
        flatpickr("#delivery_date", { minDate: "today", dateFormat: "Y-m-d" });
    </script>
</body>

</html>
