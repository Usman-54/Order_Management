<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../backend/db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer History</title>
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f8f9fa; }
    main.content { flex:1; padding:20px; background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
    .table th { background:#f1f3f5; }
    .btn-group-sm > .btn { margin-right:3px; }
  </style>
</head>
<body>

<?php include '../includes/header.php'; ?>
<div class="container mt-4 d-flex">
  <?php include '../includes/sidebar.php'; ?>
  <main class="content w-100">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Customer History</h3>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle text-center">
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone No</th>
            <th>Total Orders</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
        $sql = "
          SELECT 
            c.customer_id,
            u.name,
            u.email,
            c.phone_no,
            COUNT(o.customer_id) AS order_count
          FROM customers c
          JOIN users u ON c.user_id = u.user_id
          LEFT JOIN orders o ON c.customer_id = o.customer_id
          GROUP BY c.customer_id, u.name, u.email, c.phone_no
          ORDER BY c.created_at DESC
        ";

        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0):
          $i = 1;
          while ($row = $result->fetch_assoc()):
        ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone_no']) ?></td>
            <td><?= (int)$row['order_count'] ?></td>
            <td>
              <div class="btn-group btn-group-sm" role="group">
                <!-- View Button -->
                <button class="btn btn-outline-primary viewBtn btn-sm" data-id="<?= $row['customer_id'] ?>">
                  <i class="fa fa-eye"></i>
                </button>

                <!-- Delete Button -->
                <button class="btn btn-outline-danger deleteBtn btn-sm" data-id="<?= $row['customer_id'] ?>">
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </td>
          </tr>
        <?php endwhile; else: ?>
          <tr><td colspan="6" class="text-muted">No customers found</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<script src="assets/customer.js"></script>
</body>
</html>
