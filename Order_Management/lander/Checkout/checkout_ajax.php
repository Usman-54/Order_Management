<?php
include("../../backend/db.php");
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone_no = trim($_POST['phone'] ?? '');
$order_date = trim($_POST['delivery_date'] ?? '');
$order_time = trim($_POST['delivery_time'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if (!$name || !$email || !$phone_no) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

// Calculate totals
$totalPrice = 0;
foreach ($cart as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
$discountThreshold = 2000;
$discountRate = 0.10;
$discount = ($totalPrice > $discountThreshold) ? $totalPrice * $discountRate : 0;
$finalPrice = $totalPrice - $discount;

// Encode cart
$cartJson = json_encode($cart, JSON_UNESCAPED_UNICODE);

// 1️⃣ Check if user exists
$checkUser = $conn->prepare("SELECT user_id FROM users WHERE email=?");
$checkUser->bind_param("s", $email);
$checkUser->execute();
$checkUser->store_result();

if ($checkUser->num_rows === 0) {
    // Insert new user
    $insertUser = $conn->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
    $insertUser->bind_param("ss", $name, $email);
    $insertUser->execute();
    $user_id = $insertUser->insert_id;
    $insertUser->close();
} else {
    $checkUser->bind_result($user_id);
    $checkUser->fetch();
}
$checkUser->close();

// 2️⃣ Check if customer exists
$checkCustomer = $conn->prepare("SELECT customer_id FROM customers WHERE user_id=?");
$checkCustomer->bind_param("i", $user_id);
$checkCustomer->execute();
$checkCustomer->store_result();

if ($checkCustomer->num_rows === 0) {
    // Insert customer linked to user
    $insertCustomer = $conn->prepare("INSERT INTO customers (user_id, phone_no) VALUES (?, ?)");
    $insertCustomer->bind_param("is", $user_id, $phone_no);
    $insertCustomer->execute();
    $customer_id = $insertCustomer->insert_id;
    $insertCustomer->close();
} else {
    $checkCustomer->bind_result($customer_id);
    $checkCustomer->fetch();
}
$checkCustomer->close();

// 3️⃣ Insert order
$stmt = $conn->prepare("INSERT INTO orders 
    (customer_id, name, email, phone_no, order_date, order_time, notes, cart, total_price, discount, final_price, created_at, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Pending')");

$stmt->bind_param(
    "issssssdddd",
    $customer_id,
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
    $orderId = $stmt->insert_id;
    $_SESSION['current_order_id'] = $orderId;
    unset($_SESSION['cart']);
    echo json_encode(['success' => true, 'order_id' => $orderId]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
