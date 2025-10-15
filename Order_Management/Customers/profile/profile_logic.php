<?php
// Start session before anything else
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

// include database
include "../../backend/db.php";

// ✅ Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$customer_id = $_SESSION['customer_id'];

// ✅ Update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone_no = trim($_POST['phone']); // input name in form is "phone"
    $address = trim($_POST['address']);

    $stmt = $conn->prepare("UPDATE customers SET name=?, email=?, phone_no=?, address=? WHERE customer_id=?");
    $stmt->bind_param("ssssi", $name, $email, $phone_no, $address, $customer_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Database update failed"]);
    }
    exit;
}

// ✅ Fetch logic
$stmt = $conn->prepare("SELECT name, email, phone_no, address FROM customers WHERE customer_id=?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode(["status" => "success", "data" => $data]);
} else {
    echo json_encode(["status" => "error", "message" => "Customer not found"]);
}
?>
