<?php
// cart.php → returns only the cart items + subtotal (no sidebar wrapper)
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

include __DIR__ . '/../backend/db.php'; // ensure path correct

// --- CART LOGIC ---

// Add to cart (POST action=add)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    $id = intval($_POST['id'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 1);
    $size = $_POST['size'] ?? 'medium';

    // accept title/price/image if sent, otherwise fetch from DB
    $title = $_POST['title'] ?? null;
    $price = isset($_POST['price']) ? $_POST['price'] : null;
    $image = $_POST['image'] ?? null;

    if (!$title || !$price || !$image) {
        $stmt = $conn->prepare("SELECT title, regular_price, image FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $title = $row['title'];
            $price = $row['regular_price'];
            $image = $row['image'] ?: 'uploads/default.jpg';
        }
        $stmt->close();
    }
    $extras = $_POST['extras'] ?? ''; // new field

    $newItem = [
        'id'       => $id,
        'title'    => $title,
        'price'    => floatval($price),
        'quantity' => $quantity,
        'size'     => $size,
        'extras'   => $extras,
        'image'    => $image,
    ];

    // $newItem = [
    //     'id'       => $id,
    //     'title'    => $title,
    //     'price'    => floatval($price),
    //     'quantity' => $quantity,
    //     'size'     => $size,
    //     'image'    => $image,
    // ];

    // merge with existing item if same id+size
    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $newItem['id'] && ($item['size'] ?? '') == $newItem['size']) {
            // $item['quantity'] += $newItem['quantity'];
            $item['quantity'] = $newItem['quantity'];

            $found = true;
            break;
        }
    }
    unset($item);
    if (!$found) $_SESSION['cart'][] = $newItem;
}

// Update quantity (GET)
if (isset($_GET['update'], $_GET['index'])) {
    $i = intval($_GET['index']);
    if (isset($_SESSION['cart'][$i])) {
        if ($_GET['update'] === 'increase') $_SESSION['cart'][$i]['quantity']++;
        if ($_GET['update'] === 'decrease') {
            $_SESSION['cart'][$i]['quantity']--;
            if ($_SESSION['cart'][$i]['quantity'] < 1) {
                unset($_SESSION['cart'][$i]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
            }
        }
    }
}

// Remove item (GET)
if (isset($_GET['remove'], $_GET['index'])) {
    $i = intval($_GET['index']);
    if (isset($_SESSION['cart'][$i])) {
        unset($_SESSION['cart'][$i]);
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// --- CART OUTPUT ---
$total = 0.0;
$count = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $index => $item) {
        $img = "../backend/" . ($item['image'] ?: 'uploads/default.jpg');
        $price = floatval($item['price']);
        $qty   = intval($item['quantity']);
        $lineTotal = $price * $qty;
        $total += $lineTotal;
        $count += $qty;
        ?>
        <div class="d-flex align-items-center mb-3">
            <img src="<?= htmlspecialchars($img) ?>" style="width:60px;height:60px;object-fit:cover;border-radius:6px;margin-right:10px;" alt="">
            <div class="flex-grow-1">
            <h6 class="mb-1"><?= htmlspecialchars($item['title']) ?> (<?= htmlspecialchars($item['size']) ?>)</h6>
            <small>$<?= number_format($price, 2) ?> × <?= $qty ?></small><br>

            <?php if (!empty($item['extras'])): ?>
                <small class="text-muted">Extras: <?= htmlspecialchars($item['extras']) ?></small><br>
            <?php endif; ?>

            <strong>$<?= number_format($lineTotal, 2) ?></strong>
        </div>

            <div class="d-flex align-items-center" style="gap:5px;">
                <button onclick="updateCart('decrease', <?= $index ?>)" class="btn btn-sm btn-outline-secondary">-</button>
                <span><?= $qty ?></span>
                <button onclick="updateCart('increase', <?= $index ?>)" class="btn btn-sm btn-outline-secondary">+</button>
                <button onclick="removeCart(<?= $index ?>)" class="btn btn-sm btn-danger">🗑</button>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p class='text-center text-muted'>Your cart is empty</p>";
}
?>
<hr>
<div class="text-end">
    <strong>Subtotal: $<?= number_format($total, 2) ?></strong>
</div>

<!-- Machine-readable -- used by JS after ajax replace -->
<div id="cartSubtotal" data-total="<?= htmlspecialchars(number_format($total, 2, '.', '')) ?>" data-count="<?= intval($count) ?>" style="display:none"></div>
