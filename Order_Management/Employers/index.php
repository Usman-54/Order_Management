<?php
session_start();
define("BASE_URL","http://localhost/resturent/Order_Management/");

// Admin-only
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employers</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Employers</h1>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addEmployerModal">
            <i class="fas fa-plus"></i> Add Employer
        </button>
    </div>

    <table class="table table-bordered" id="employersTable">
        <thead class="table-dark">
            <tr>
                <th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Role</th><th>Created At</th><th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Add Employer Modal -->
<div class="modal fade" id="addEmployerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form id="addEmployerForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Employer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3"><label>Name</label><input type="text" name="name" class="form-control" required></div>
        <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
        <div class="mb-3"><label>Phone</label><input type="text" name="phone" class="form-control"></div>
        <div class="mb-3"><label>Address</label><input type="text" name="address" class="form-control"></div>
        <div class="mb-3"><label>Role</label>
            <select name="role" class="form-control" required>
                <option value="employer">Employer</option>
                <option value="admin">Admin</option>
            </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Save</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/employers.js"></script>
</body>
</html>
