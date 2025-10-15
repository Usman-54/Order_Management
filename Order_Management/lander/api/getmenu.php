<?php
// api/addProduct.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 1. Database Connection (Use your exact details)
$mysqli = new mysqli("localhost", "root", "admin", "order_management");
if ($mysqli->connect_error) {
    // Log error and handle gracefully
    error_log("DB Connection failed: " . $mysqli->connect_error);
    header("Location: ../admin/products.php?status=db_error"); // Redirect back to admin with error
    exit;
}

// 2. Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin/products.php");
    exit;
}

// 3. Image Upload Handling (Ensure your upload directory is correct)
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $target_dir = "../uploads/product_images/"; // Adjust path relative to addProduct.php
    // Ensure directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $file_name = uniqid('product_') . '.' . $file_ext;
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = 'uploads/product_images/' . $file_name; // Path to store in DB
    } else {
        header("Location: ../admin/products.php?status=img_error");
        exit;
    }
} else {
    // Handle case where image is required but missing
    header("Location: ../admin/products.php?status=img_required");
    exit;
}

// 4. Sanitize and Collect Other Data
// NOTE: Ensure your field names match your database columns and form inputs
$category = $_POST['category'] ?? '';
$title = $_POST['title'] ?? '';
$description = $_POST['description'] ?? NULL;
$regular_price = $_POST['regularPrice'] ?? 0.00;
$special_offer = $_POST['specialOffer'] ?: NULL; 
$weight = $_POST['weight'] ?: NULL;
$length = $_POST['length'] ?: NULL;
$width = $_POST['width'] ?: NULL;
$height = $_POST['height'] ?: NULL;
$notes = $_POST['notes'] ?: NULL;
$additional = $_POST['additional'] ?: NULL;
$multi_price = isset($_POST['multiPrice']) ? 1 : 0; 


// 5. Database Insertion using Prepared Statement
$sql = "INSERT INTO products (category, title, description, regular_price, special_offer, image, weight, length, width, height, notes, additional, multi_price) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
$stmt = $mysqli->prepare($sql);

if ($stmt === false) {
    error_log("Prepare failed: " . $mysqli->error);
    header("Location: ../admin/products.php?status=sql_error");
    exit;
}

// Bind parameters (s=string, d=double/decimal, i=integer)
$stmt->bind_param("sssdiddddssis", 
    $category, 
    $title, 
    $description, 
    $regular_price, 
    $special_offer, 
    $image_path,
    $weight, 
    $length, 
    $width, 
    $height, 
    $notes, 
    $additional, 
    $multi_price
);

if ($stmt->execute()) {
    // Success: Redirect the user to the public lander page to show the result.
    // Use an absolute path or relative path from the server root.
    header("Location: ../lander/index.php?status=success"); 
    exit;
} else {
    error_log("Execute failed: " . $stmt->error);
    header("Location: ../admin/products.php?status=fail");
    exit;
}

$stmt->close();
$mysqli->close();
?>