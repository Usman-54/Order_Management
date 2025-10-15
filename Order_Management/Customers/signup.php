<?php
ob_start(); // Prevent headers already sent
session_start();
include "../backend/db.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $address  = trim($_POST['address']);
    $password = trim($_POST['password']); // plain password
    $role     = 'customer';

    // Check if email already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "⚠️ Email already registered!";
        $stmt->close();
    } else {
        $stmt->close();

        // Insert into users table
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $email, $password, $role);

        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt->close();

            // Insert into customers table
            $stmt = $conn->prepare("INSERT INTO customers (user_id, phone_no, address, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iss", $user_id, $phone, $address);

            if ($stmt->execute()) {
                $stmt->close();

                // Set session
                $_SESSION['user_id']   = $user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['role']      = $role;

                // Redirect to order_tracking.php
                header("Location: order_tracking.php");
                exit;
            } else {
                $error = "❌ Failed to save customer details!";
            }
        } else {
            $error = "❌ Failed to create user account!";
        }
    }
}
ob_end_flush();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/signin.css">
</head>
<body>

<div class="card">
    <h3 class="text-center mb-4">Create Account</h3>

    <?php if(!empty($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" action="signup.php">
        <div class="row mb-3">
            <div class="col">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" placeholder="Enter full name" required>
            </div>
            <div class="col">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" placeholder="Enter email" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
            </div>
            <div class="col">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>" placeholder="Enter phone" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <textarea class="form-control" id="address" name="address" placeholder="Enter address" rows="3" required><?= isset($address) ? htmlspecialchars($address) : '' ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary w-100">Register</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
