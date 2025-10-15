<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// backend/productApi.php
header("Content-Type: application/json");
include "db.php";

// Helper: JSON response
function response($status, $message, $data = null) {
    echo json_encode([
        "status"  => $status,
        "message" => $message,
        "data"    => $data
    ]);
    exit;
}

$action = $_GET['action'] ?? '';

switch ($action) {

    // ================= CREATE PRODUCT =================
    case "create":
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') response("error", "Invalid request method");

        $category     = $_POST['category']     ?? '';
        $title        = $_POST['title']        ?? '';
        $description  = $_POST['description']  ?? '';
        $regularPrice = floatval($_POST['regularPrice'] ?? 0);
        $specialOffer = floatval($_POST['specialOffer'] ?? 0);
        $weight       = floatval($_POST['weight'] ?? 0);
        $length       = floatval($_POST['length'] ?? 0);
        $width        = floatval($_POST['width'] ?? 0);
        $height       = floatval($_POST['height'] ?? 0);
        $notes        = $_POST['notes']        ?? '';
        $additional   = $_POST['additional']   ?? '';
        $multiPrice   = isset($_POST['multiPrice']) ? 1 : 0;

        // Handle Image Upload
        $imagePath = null;
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/uploads/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $filename = time() . "_" . basename($_FILES['image']['name']);
            $target   = $uploadDir . $filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $imagePath = "uploads/" . $filename;
            }
        }

        $stmt = $conn->prepare("
            INSERT INTO products 
            (category, title, description, image, regular_price, special_offer, weight, length, width, height, notes, additional, multi_price) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssssddddddssi", 
            $category, $title, $description, $imagePath,
            $regularPrice, $specialOffer, $weight,
            $length, $width, $height,
            $notes, $additional, $multiPrice
        );

        if ($stmt->execute()) {
            response("success", "Product created successfully");
        } else {
            response("error", "Failed to create product: " . $stmt->error);
        }
        break;

    // ================= READ ALL PRODUCTS =================
    case "read":
        $result = $conn->query("SELECT * FROM products ORDER BY id DESC");
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        response("success", "Products fetched", $products);
        break;

    // ================= UPDATE PRODUCT =================
    case "update":
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = intval($_POST['id'] ?? 0);

        // Fetch existing row first
        $result = $conn->query("SELECT * FROM products WHERE id=$id");
        if (!$result || $result->num_rows == 0) {
            response("error", "Product not found");
        }
        $existing = $result->fetch_assoc();

        // Use submitted values or fall back to existing ones
        $category     = $_POST['category']     ?? $existing['category'];
        $title        = $_POST['title']        ?? $existing['title'];
        $description  = $_POST['description']  ?? $existing['description'];
        $regularPrice = $_POST['regularPrice'] ?? $existing['regular_price'];
        $specialOffer = $_POST['specialOffer'] ?? $existing['special_offer'];
        $weight       = $_POST['weight']       ?? $existing['weight'];
        $length       = $_POST['length']       ?? $existing['length'];
        $width        = $_POST['width']        ?? $existing['width'];
        $height       = $_POST['height']       ?? $existing['height'];
        $notes        = $_POST['notes']        ?? $existing['notes'];
        $additional   = $_POST['additional']   ?? $existing['additional'];
        $multiPrice   = isset($_POST['multiPrice']) ? 1 : $existing['multi_price'];

        // Handle Image
        $imagePath = $existing['image'];
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . "/uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $filename = time() . "_" . basename($_FILES['image']['name']);
            $target = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $imagePath = "uploads/" . $filename;
            }
        }

        $stmt = $conn->prepare("UPDATE products SET 
            category=?, title=?, description=?, image=?, regular_price=?, special_offer=?, weight=?, length=?, width=?, height=?, notes=?, additional=?, multi_price=? 
            WHERE id=?");
        $stmt->bind_param("ssssdddddsssii", 
            $category, $title, $description, $imagePath,
            $regularPrice, $specialOffer, $weight,
            $length, $width, $height,
            $notes, $additional, $multiPrice, $id
        );

        if ($stmt->execute()) {
            response("success", "Product updated successfully");
        } else {
            response("error", "Failed to update product: " . $stmt->error);
        }
    }
    break;

    // ================= DELETE PRODUCT =================
    case "delete":
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') response("error", "Invalid request method");

        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            response("success", "Product deleted successfully");
        } else {
            response("error", "Failed to delete product: " . $stmt->error);
        }
        break;

    // ================= INVALID =================
    default:
        response("error", "Invalid action");
}
