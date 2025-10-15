<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../backend/db.php");

// START SESSION BEFORE ANY OUTPUT
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch products
$sql = "SELECT id, category, title, description, image, regular_price FROM products";
$result = $conn->query($sql);

$menuItems = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $menuItems[] = [
            "id" => $row['id'],
            "category" => $row['category'],
            "title" => $row['title'],
            "desc" => $row['description'],
            "price" => $row['regular_price'],
            "image" => $row['image']
        ];
    }
}

// Group products by category
$grouped = [];
foreach ($menuItems as $item) {
    $grouped[$item['category']][] = $item;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Our Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="../assets/css/lander_index.css">

  <!-- <style>
    .btn-add { border-radius: 50%; width:40px; height:40px; display:flex; align-items:center; justify-content:center;}
    .card-img-top { height:200px; object-fit:cover; }
  </style> -->
</head>
<body>
<?php
// quick initial total from session for page load
$initialTotal = '0.00';
if (session_status() === PHP_SESSION_NONE) session_start();
if (!empty($_SESSION['cart'])) {
    $t = 0;
    foreach ($_SESSION['cart'] as $it) {
        $t += (float)($it['price'] ?? 0) * (int)($it['quantity'] ?? ($it['qty'] ?? 1));
    }
    $initialTotal = number_format($t, 2, '.', '');
}
?>
<!-- Floating "View order" pill -->
<!--  -->
<?php include 'header.html'; ?> <!-- header.html must be the fragment below -->
<!-- View order button under header links -->
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <!-- Centered heading -->
    <h1 class="text-center flex-grow-1 mb-0">Our Food Menu</h1>

    <!-- Button aligned right -->
    <button onclick="openCart()" id="viewOrderButton"
            class="btn"
            style="display:flex;align-items:center;gap:.6rem;padding:.45rem .7rem;border-radius:30px;border:none;box-shadow:0 6px 18px rgba(0,0,0,0.12);background:red;color:#fff;font-weight:600;">
      <span style="font-size:.95rem;line-height:1;">View order</span>
      <span id="viewOrderTotal" style="background:rgba(255,255,255,0.12);padding:.35rem .6rem;border-radius:14px;font-weight:700;">
        $<?= htmlspecialchars($initialTotal) ?>
      </span>
    </button>
  </div>
</div>


<div class="container my-5">
  <!-- <h1 class="text-center mb-5">Our Food Menu</h1> -->

  <?php if (!empty($grouped)): ?>
    <?php foreach ($grouped as $category => $items): ?>
      <h2 class="mb-4"><?php echo htmlspecialchars($category); ?></h2>
      <div class="row row-cols-1 row-cols-md-3 g-4 mb-4">
        <?php foreach ($items as $item): ?>
          <!-- NOTE: .product-card and data-category are required by nav.js -->
          <div class="col product-card" data-category="<?php echo htmlspecialchars($category); ?>">
            <div class="card h-100 shadow-sm">
              <div class="row g-0">
                <div class="col-md-7 p-3 d-flex flex-column justify-content-between">
                  <div>
                    <h5 class="card-title mb-2"><?php echo htmlspecialchars($item['title']); ?></h5>
                    <p class="card-text mb-2 text-truncate" style="max-height:3em;">
                      <?php echo htmlspecialchars($item['desc']); ?>
                    </p>
                  </div>
                  <div>
                    <strong>$<?php echo number_format($item['price'], 2); ?></strong>
                  </div>
                </div>

                <div class="col-md-5 position-relative">
                  <?php
                    $itemImage = str_replace('\\', '/', $item['image']);
                    $serverPath = __DIR__ . "/../backend/" . $itemImage;
                    $webPath = "../backend/" . $itemImage;
                    if (!file_exists($serverPath) || empty($itemImage)) {
                        $webPath = "../backend/uploads/default.jpg";
                    }
                  ?>
                  <img src="<?php echo $webPath; ?>" class="img-fluid rounded-end"
                       style="height:150px; width:100%; object-fit:cover;"
                       alt="<?php echo htmlspecialchars($item['title']); ?>">

                  <!-- + Button -->
                  <button type="button"
                          class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 add-product-btn"
                          data-id="<?php echo $item['id']; ?>">
                    +
                  </button>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="text-center">No products found.</p>
  <?php endif; ?>
</div>

<!-- Product Modal -->
<?php include 'cart_sidebar.php'; ?>
<!-- scripts: load bootstrap BEFORE any code that uses bootstrap.Modal -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="item.js"></script>              <!-- MUST be relative to this file -->
<script src="cart.js"></script>

<script src="../assets/js/nav.js"></script>
<script>
function openCart() {
  const cartSidebar = document.getElementById("cartSidebar");
  const btn = document.getElementById("viewOrderButton");

  if (cartSidebar) {
    cartSidebar.style.transform = "translateX(0)"; // slide in
  }

  if (btn) btn.style.display = "none"; // hide button
}

function closeCart() {
  const cartSidebar = document.getElementById("cartSidebar");
  const btn = document.getElementById("viewOrderButton");

  if (cartSidebar) {
    cartSidebar.style.transform = "translateX(100%)"; // slide out
  }

  if (btn) btn.style.display = "flex"; // show button again
}
</script>

</body>
</html>
