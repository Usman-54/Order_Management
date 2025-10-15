<?php
include("../../backend/db.php");


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch cart items from session
$cart = $_SESSION['cart'] ?? [];

// Calculate total
$totalPrice = 0;
foreach ($cart as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

// Discount
$discountThreshold = 2000;
$discountRate = 0.10;
$discount = ($totalPrice > $discountThreshold) ? $totalPrice * $discountRate : 0;
$finalPrice = $totalPrice - $discount;

// Encode cart as JSON
$cartJson = json_encode($cart);

// Handle form submission
$db_error = "";
$order_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_no = trim($_POST['phone']);
    $order_date = trim($_POST['delivery_date']);
    $order_time = trim($_POST['delivery_time']);
    $notes = trim($_POST['notes'] ?? '');

    // --- 1️⃣ Check if customer already exists by email ---
    $check = $conn->prepare("SELECT id FROM customers WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows === 0) {
        // Insert only if new customer
        $insertCustomer = $conn->prepare("
            INSERT INTO customers (name, email, phone_no)
            VALUES (?, ?, ?)
        ");
        $insertCustomer->bind_param("sss", $name, $email, $phone_no);
        $insertCustomer->execute();
        $insertCustomer->close();
    }
    $check->close();

    // --- 2️⃣ Insert into orders (no change) ---
    $stmt = $conn->prepare("
        INSERT INTO orders
        (name, email, phone_no, order_date, order_time, notes, cart, total_price, discount, final_price, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->bind_param(
        "sssssssddd",
        $name,
        $email,
        $phone_no,
        $order_date,
        $order_time,
        $notes,
        $cartJson,
        $totalPrice,
        $discount,
        $finalPrice
    );

    if ($stmt->execute()) {
        $order_success = true;
        unset($_SESSION['cart']);
    } else {
        $db_error = $stmt->error;
    }

    $stmt->close();
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/clockpicker/0.0.7/bootstrap-clockpicker.min.js"></script>

<link rel="stylesheet" href="../../assets/css/checkout.css">

</head>
<body>

<div class="checkout-container">
    <div class="row g-4">
        <!-- LEFT -->
        <div class="col-lg-7">
            <div class="checkout-left" id="checkoutleft">
                <h4>Customer Details</h4>

                <?php if(!empty($db_error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($db_error) ?></div>
                <?php endif; ?>

                <?php if($order_success): ?>
                    <div class="alert alert-success">
                        🎉 Your order has been placed! Redirecting...
                    </div>
                    <script>
                        setTimeout(()=>{ window.location.href='index.php'; }, 2000);
                    </script>
                <?php endif; ?>

                <form action="" method="POST">
                    <div class="row g-3">
                        <div class="col-md-4"><input type="text" class="form-control" name="name" placeholder="Full Name" required></div>
                        <div class="col-md-4"><input type="email" class="form-control" name="email" placeholder="Email" required></div>
                        <div class="col-md-4"><input type="tel" class="form-control" name="phone" placeholder="Phone" required></div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Order Date</label>
                            <input type="text" id="delivery_date" class="form-control" name="delivery_date" placeholder="Select date" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Order Time</label>
                            <input type="text" id="delivery_time" class="form-control" name="delivery_time" placeholder="Select time" required>
                        </div>
                    </div>

                    <div class="instructions-heading mt-3">Instructions / Notes</div>
                    <textarea class="form-textarea" name="notes" placeholder="Add a note"></textarea>

                    <div class="mt-3 d-flex gap-2">
                       
                        <button type="submit" class="btn btn-danger btn-lg">Place Order</button>

                        <!-- <button type="submit" class="btn btn-danger btn-lg">Place Order</button> -->
                        <a href="index.php" class="btn btn-secondary btn-lg">Close</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- RIGHT: ORDER SUMMARY -->
        <div class="col-lg-5">
            <div class="checkout-right order-summary">
                <h4>Order Summary</h4>
                <?php if(empty($cart)): ?>
                    <p class="text-center text-muted">Your cart is empty.</p>
                <?php else: ?>
                    <ul class="list-unstyled">
                        <?php foreach($cart as $item): ?>
                            <li class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="../../backend/<?= $item['image'] ?: 'uploads/default.jpg' ?>" 
                                        alt="<?= htmlspecialchars($item['title']) ?>" 
                                        style="width:50px;height:50px;">
                                    <span><?= htmlspecialchars($item['title']) ?> (x<?= $item['quantity'] ?>)</span>
                                </div>
                                <span>$<?= number_format($item['price'] * $item['quantity'],2) ?></span>
                            </li>
                            <?php endforeach; ?>


                    </ul>

                    <a href="index.php" class="btn btn-secondary w-100 mb-3">Add More Items</a>

                    <?php if($discount>0): ?>
                        <div class="alert alert-success">🎉 Discount: $<?= number_format($discount,2) ?> | Total: $<?= number_format($finalPrice,2) ?></div>
                    <?php else: ?>
                        <div class="alert alert-info">Add $<?= number_format($discountThreshold-$totalPrice,2) ?> more to get 10% off!</div>
                    <?php endif; ?>

                    <div class="mt-3 d-flex justify-content-between fw-bold" style="font-size:1.2rem;">
                        <span>Total</span>
                        <span>$<?= number_format($finalPrice,2) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<!-- <script src="../assets/checkout.js"></script> -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    flatpickr("#delivery_date", { minDate:"today", dateFormat:"Y-m-d" });
    $('#delivery_time').clockpicker({ autoclose:true, placement:'bottom', align:'left', donetext:'Done', twelvehour:false });
});
</script>

</body>
</html>
