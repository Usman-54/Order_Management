<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/../backend/db.php';
?>

<div id="cartSidebar" class="position-fixed top-0 end-0 vh-100 bg-white shadow-lg p-3"
     style="width:450px; transform: translateX(100%); transition: transform 0.3s ease; z-index:1050; display:flex; flex-direction:column;">

    <!-- Header (fixed) -->
    <div class="d-flex align-items-center mb-3 border-bottom pb-2">
        <img src="../assets/images/resturnt_logo.jpg" alt="Logo" style="height:40px; margin-right:10px;">
        <h5 class="fw-bold text-dark mb-0 flex-grow-1">Your Order</h5>
        <!-- <button class="btn btn-sm btn-outline-danger" onclick="closeCart()">
            <i class="fas fa-times"></i>
        </button> -->
        <button type="button" class="btn-close position-absolute top-0 end-0 m-2" 
        onclick="closeCart()" aria-label="Close"></button>

    </div>

    <!-- Scrollable body (Cart + Recommended) -->
    <div style="flex:1; overflow-y:auto; padding-right:5px;">
        
        <!-- Cart Items -->
        <div id="cartItems" class="mb-3">
            <?php include __DIR__ . "/cart.php"; ?>
        </div>

        <!-- Recommended Section -->
        <div class="mt-3">
            <h6 class="fw-bold mb-2">Recommended for you</h6>
            <div class="row g-2">
                <?php
                $res = $conn->query("SELECT id, title, image, regular_price FROM products LIMIT 6"); // show up to 6 items
                while ($row = $res->fetch_assoc()) {
                    $imgPath = "../backend/" . ($row['image'] ?: 'uploads/default.jpg');
                    ?>
                    <div class="col-6">
  <div class="card h-100 border-0 shadow-sm p-2">
    <div class="text-center">
      <img src="<?= $imgPath ?>" alt="Product" class="rounded mb-2" style="width:100%; height:100px; object-fit:cover;">
      <h6 class="fw-semibold text-truncate mb-2" style="font-size:0.9rem;">
        <?= htmlspecialchars($row['title']) ?>
      </h6>
    </div>

    <!-- Row layout: price left, button right -->
    <div class=" justify-content-between align-items-center mt-auto px-1">
      <span class="text-muted" style="font-size:0.85rem;">
        $<?= number_format($row['regular_price'], 2) ?>
      </span>
      <button class="btn btn-sm btn-danger px-3 py-1" onclick="addRecommendedToCart(<?= $row['id'] ?>)">
        Add
      </button>
    </div>
  </div>
</div>

                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Checkout Button (fixed) -->
    <div class="mt-3">
        <a href="Checkout/checkout.php" class="btn btn-danger w-100">Proceed to Checkout</a>
    </div>
</div>
