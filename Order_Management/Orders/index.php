<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../backend/db.php");

$statuses = ['Pending','Accept','Prepping','Ready','Completed','Rejected'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order Management</title>
<link rel="stylesheet" href="../assets/css/styles.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>

<?php include '../includes/header.php'; ?>
<div class="container mt-4 d-flex">
<?php include '../includes/sidebar.php'; ?>
<main class="content w-100">
  <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Order Management</h3>
      </div>

<div class="table-responsive">
<table class="table table-hover align-middle text-center">
<thead>
<tr>
<th>ID</th>
<th>Customer</th>
<th>Date</th>
<th>Total</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php
$result = $conn->query("SELECT * FROM orders ORDER BY id DESC");
if ($result && $result->num_rows > 0):
    while ($row = $result->fetch_assoc()):
?>
<tr data-id="<?= $row['id'] ?>">
    <td>#<?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['order_date']) ?></td>
    <td>Rs <?= number_format($row['final_price'], 2) ?></td>
    <td>
        <select class="form-select form-select-sm status-select" data-id="<?= $row['id'] ?>">
            <?php foreach($statuses as $status): ?>
                <option value="<?= $status ?>" <?= $row['status'] == $status ? 'selected' : '' ?>>
                    <?= $status == 'Rejected' ? 'Decline' : ($status == 'Completed' ? 'Completed/Pickup' : $status) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </td>
    <td>
        <div class="btn-group btn-group-sm" role="group">
            <button class="btn btn-outline-primary viewBtn" data-id="<?= $row['id'] ?>"><i class="fa fa-eye"></i></button>
            <button class="btn btn-outline-success editBtn" data-id="<?= $row['id'] ?>"><i class="fa fa-edit"></i></button>
            <button class="btn btn-outline-danger deleteBtn" data-id="<?= $row['id'] ?>"><i class="fa fa-trash"></i></button>
        </div>
    </td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="6" class="text-muted">No orders found</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>

</main>
</div>

<div class="modal" id="orderModal">
<div class="modal-content">
<button class="close-modal" id="closeModal">&times;</button>
<div id="modalBody"></div>
</div>
</div>

<script src="../assets/js/order.js"></script>
</body>
</html>
