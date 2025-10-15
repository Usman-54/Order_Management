<?php
define("BASE_URL", "http://localhost/resturent/Order_Management/Customers/");
$customerName = $_SESSION['customer_name'] ?? null;
?>

<aside id="customer_sidebar" class="sidebar">
  <div class="sidebar-header">
      <?php if ($customerName): ?>
          <h3>Welcome, <?= htmlspecialchars($customerName) ?></h3>
      <?php else: ?>
          <h3>Customer Panel</h3>
      <?php endif; ?>
  </div>

  <ul class="sidebar-menu">
      <li>
          <a href="<?= BASE_URL ?>profile/profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>">
              <i class="fas fa-user"></i> Profile
          </a>
      </li>
      <li>
          <a href="<?= BASE_URL ?>order_tracking.php" class="<?= basename($_SERVER['PHP_SELF']) == 'order_tracking.php' ? 'active' : '' ?>">
              <i class="fas fa-truck"></i> Order Tracking
          </a>
      </li>
      <li>
          <a href="<?= BASE_URL ?>order_history.php" class="<?= basename($_SERVER['PHP_SELF']) == 'order_history.php' ? 'active' : '' ?>">
              <i class="fas fa-history"></i> Order History
          </a>
      </li>
  </ul>
</aside>
