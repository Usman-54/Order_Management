<?php
define("BASE_URL", "http://localhost/resturent/Order_Management/");

// Make sure session is started
if (session_status() === PHP_SESSION_NONE) session_start();

// Get the logged-in user role
$role = $_SESSION['role'] ?? '';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

<aside class="sidebar" id="sidebar">
  <ul class="sidebar-menu">
    <!-- Admin-only links -->
    <?php if($role === 'admin'): ?>
      <li><a href="<?php echo BASE_URL; ?>index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li>
        <a href="<?php echo BASE_URL; ?>Employers">
          <i class="fas fa-users"></i> Employers
        </a>
      </li>
    <?php endif; ?>

    <!-- Links for both admin and employer -->
    <li><a href="<?php echo BASE_URL; ?>Orders"><i class="fas fa-shopping-cart"></i> Orders</a></li>
    <li><a href="<?php echo BASE_URL; ?>Customers"><i class="fas fa-users"></i> Customers</a></li>
    <li><a href="<?php echo BASE_URL; ?>Product"><i class="fas fa-box-open"></i> Products</a></li>
  </ul>
</aside>

