<?php
// right_checkout_cart.php
if (session_status() === PHP_SESSION_NONE) session_start();

// Use session cart
$cart = $_SESSION['cart'] ?? [];

$totalPrice = 0;
$count = 0;
$discountThreshold = 2000;
$discountRate = 0.10;
?>

<div class="col-lg-5">
    <div class="checkout-right order-summary p-3 shadow-sm rounded bg-white">
        <h4 class="mb-3 border-bottom pb-2">Order Summary</h4>

        <?php if (empty($cart)): ?>
            <p class="text-center text-muted">Your cart is empty.</p>
        <?php else: ?>
            <ul class="list-unstyled">
                <?php foreach ($cart as $item):
                    // If image path in $item['image'] is already full, use it; otherwise assume it is relative to backend/uploads
                    $storedImage = $item['image'] ?? '';
                    if ($storedImage === '') {
                        $img = "../../backend/uploads/default.jpg";
                    } elseif (strpos($storedImage, 'http') === 0 || strpos($storedImage, '/') === 0) {
                        $img = $storedImage;
                    } else {
                        $img = "../../backend/" . ltrim($storedImage, '/');
                    }

                    $price = floatval($item['price']);
                    $qty = intval($item['quantity']);
                    $lineTotal = $price * $qty;
                    $totalPrice += $lineTotal;
                    $count += $qty;
                ?>
                <li class="d-flex justify-content-between align-items-start mb-3 border-bottom pb-2">
                    <div class="d-flex align-items-start gap-2">
                        <img src="<?= htmlspecialchars($img) ?>" style="width:55px;height:55px;object-fit:cover;border-radius:6px;">
                        <div>
                            <h6 class="mb-0 fw-semibold text-dark">
                                <?= htmlspecialchars($item['title'] ?? 'Item') ?>
                                <small class="text-muted">(<?= htmlspecialchars($item['size'] ?? 'medium') ?>)</small>
                            </h6>
                            <div style="font-size:0.85rem;line-height:1.3;">
                                <span class="text-muted">$<?= number_format($price, 2) ?> × <?= $qty ?></span><br>

                                <?php if (!empty($item['extras'])): ?>
                                    <span class="text-muted d-block">🍕 <strong>Extras:</strong> <?= htmlspecialchars($item['extras']) ?></span>
                                <?php endif; ?>

                                <?php if (!empty($item['drink'])): ?>
                                    <span class="text-muted d-block">🥤 <strong>Drink:</strong> <?= htmlspecialchars($item['drink']) ?></span>
                                <?php endif; ?>

                                <?php if (!empty($item['sauce'])): ?>
                                    <span class="text-muted d-block">🥫 <strong>Sauce:</strong> <?= htmlspecialchars($item['sauce']) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <span class="fw-bold text-success">$<?= number_format($lineTotal, 2) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>

            <?php
            // Calculate discount and final price
            $discount = ($totalPrice > $discountThreshold) ? $totalPrice * $discountRate : 0;
            $finalPrice = $totalPrice - $discount;
            ?>

            <?php if ($discount > 0): ?>
                <div class="alert alert-success p-2">🎉 Discount: $<?= number_format($discount, 2) ?> applied!</div>
            <?php else: ?>
                <div class="alert alert-info p-2">Add $<?= number_format(max(0, $discountThreshold - $totalPrice), 2) ?> more to get 10% off!</div>
            <?php endif; ?>

            <div class="d-flex justify-content-between fw-bold mt-3 border-top pt-2" style="font-size:1.1rem;">
                <span>Total</span>
                <span>$<?= number_format($finalPrice, 2) ?></span>
            </div>
        <?php endif; ?>
    </div>
</div>
