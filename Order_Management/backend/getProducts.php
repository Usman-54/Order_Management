<?php
// getProducts.php
include 'db.php';

$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : "";

// Fetch products
$sql = "SELECT product_id, min_price, max_price FROM product 
        WHERE stock_status='instock' 
        AND (product_id LIKE '%$search%')
        LIMIT 20";

$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    $price = ($row['min_price'] == $row['max_price']) 
        ? $row['min_price'] 
        : $row['min_price'] . " - " . $row['max_price'];
    $products[] = [
        "id" => $row['product_id'],
        "text" => "Product #" . $row['product_id'] . " ($" . $price . ")"
    ];
}

echo json_encode(["results" => $products]);
?>
