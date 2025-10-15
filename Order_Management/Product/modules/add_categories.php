<?php
include("../../backend/db.php"); // adjust path
header('Content-Type: application/json');

$name = trim($_POST['name'] ?? '');

if (!$name) {
    echo json_encode(['success' => false, 'message' => 'Category name cannot be empty.']);
    exit;
}

// Check if category exists
$stmt = $conn->prepare("SELECT * FROM categories WHERE name = ?");
$stmt->bind_param("s", $name);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $existing = $result->fetch_assoc();
    echo json_encode([
        'success' => false,
        'message' => 'Category already exists.',
        'category_id' => $existing['category_id'],
        'name' => $existing['name']
    ]);
    exit;
}

// Insert new category
$stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
$stmt->bind_param("s", $name);
if ($stmt->execute()) {
    $id = $stmt->insert_id;
    echo json_encode([
        'success' => true,
        'category_id' => $id,
        'name' => $name
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>
