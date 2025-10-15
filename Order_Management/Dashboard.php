<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("backend/db.php");


// Summary data
$totalCustomers = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()['total'];
$totalOrders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$totalRevenue = $conn->query("SELECT SUM(final_price) AS total FROM orders")->fetch_assoc()['total'] ?? 0;
$pendingOrders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='Pending'")->fetch_assoc()['total'];
$completedOrders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE status='Completed'")->fetch_assoc()['total'];
$totalProducts = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$query = "
  SELECT 
    MONTH(order_date) AS month,
    COUNT(id) AS total_orders
  FROM orders
  WHERE order_date IS NOT NULL
    AND YEAR(order_date) = YEAR(CURDATE())
  GROUP BY MONTH(order_date)
  ORDER BY MONTH(order_date)
";
$result = $conn->query($query);

// Prepare all 12 months
$monthNames = [
  1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun',
  7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
];
$orderCounts = array_fill(1, 12, 0);

// Fill actual order data
if ($result && $result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $month = (int)$row['month'];
    $orderCounts[$month] = (int)$row['total_orders'];
  }
}

$months = array_values($monthNames);
$orderData = array_values($orderCounts);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="assets/css/styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f8f9fa; }
    main.content { flex:1; padding:20px; background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
    .card { border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.08); }
    .icon-box { font-size: 1.8rem; color: #fff; padding: 10px; border-radius: 10px; }
  </style>
</head>
<body>
  <?php include 'includes/header.php'; ?>
  <div class="container mt-4 d-flex">
    <?php include 'includes/sidebar.php'; ?>
    <main class="content w-100">

      <h3 class="mb-4"><i class="fa fa-tachometer-alt"></i> Dashboard Overview</h3>

      <!-- Summary Cards -->
      <div class="row g-3 mb-4">
        <div class="col-md-4 col-lg-2">
          <div class="card text-center p-3">
            <div class="icon-box bg-primary mx-auto mb-2"><i class="fa fa-users"></i></div>
            <h6>Total Customers</h6>
            <h4><?= $totalCustomers ?></h4>
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="card text-center p-3">
            <div class="icon-box bg-success mx-auto mb-2"><i class="fa fa-shopping-cart"></i></div>
            <h6>Total Orders</h6>
            <h4><?= $totalOrders ?></h4>
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="card text-center p-3">
            <div class="icon-box bg-warning mx-auto mb-2"><i class="fa fa-coins"></i></div>
            <h6>Total Revenue</h6>
            <h4>Rs <?= number_format($totalRevenue, 0) ?></h4>
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="card text-center p-3">
            <div class="icon-box bg-danger mx-auto mb-2"><i class="fa fa-clock"></i></div>
            <h6>Pending Orders</h6>
            <h4><?= $pendingOrders ?></h4>
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="card text-center p-3">
            <div class="icon-box bg-info mx-auto mb-2"><i class="fa fa-check-circle"></i></div>
            <h6>Completed</h6>
            <h4><?= $completedOrders ?></h4>
          </div>
        </div>
        <div class="col-md-4 col-lg-2">
          <div class="card text-center p-3">
            <div class="icon-box bg-secondary mx-auto mb-2"><i class="fa fa-box"></i></div>
            <h6>Products</h6>
            <h4><?= $totalProducts ?></h4>
          </div>
        </div>
      </div>

      <!-- Charts -->
    <div class="container mt-5">
  <div class="card mb-4">
    <div class="card-header bg-dark text-white"><strong>Orders Overview (Current Year)</strong></div>
    <div class="card-body">
      <canvas id="ordersChart" height="100"></canvas>
    </div>
  </div>
</div>
      <!-- Recent Orders -->
      <div class="card mb-4">
        <div class="card-header bg-primary text-white"><strong>Recent Orders</strong></div>
        <div class="card-body p-0">
          <table class="table table-hover text-center mb-0">
            <thead>
              <tr><th>ID</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr>
            </thead>
            <tbody>
              <?php
              $recent = $conn->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
              if ($recent && $recent->num_rows > 0):
                while($r = $recent->fetch_assoc()):
              ?>
              <tr>
                <td>#<?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td>Rs <?= number_format($r['final_price'], 2) ?></td>
                <td><?= htmlspecialchars($r['status']) ?></td>
                <td><?= htmlspecialchars($r['order_date']) ?></td>
              </tr>
              <?php endwhile; else: ?>
                <tr><td colspan="5" class="text-muted">No recent orders</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </main>
  </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  const ctx = document.getElementById('ordersChart').getContext('2d');
  
  const labels = <?= json_encode($months) ?>;
  const data = <?= json_encode($orderData) ?>;

  new Chart(ctx, {
    type: 'bar',  // 🟩 Vertical bar chart
    data: {
      labels: labels,
      datasets: [{
        label: 'Orders per Month',
        data: data,
        backgroundColor: 'rgba(54, 162, 235, 0.7)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1,
        borderRadius: 6,
        hoverBackgroundColor: 'rgba(75, 192, 192, 0.9)',
      }]
    },
    options: {
      responsive: true,
      scales: {
        x: {
          grid: {
            display: true,
            color: "rgba(0,0,0,0.1)",
            drawBorder: false,
          },
          ticks: {
            color: "#333",
            font: { size: 12 }
          }
        },
        y: {
          beginAtZero: true,
          grid: {
            color: "rgba(0,0,0,0.1)",
            drawBorder: false,
          },
          ticks: {
            precision: 0,
            color: "#333"
          }
        }
      },
      plugins: {
        legend: { display: true, position: 'bottom' },
        title: {
          display: true,
          text: 'Monthly Orders (Bar Chart)',
          font: { size: 16 }
        }
      }
    }
  });
});
</script>

</body>
</html>
