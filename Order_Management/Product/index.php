<?php
include("../backend/db.php"); // ✅ Correct relative path to backend
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/styles.css">
  <link rel="stylesheet" href="../assets/css/modal.css">
</head>
<body>
  <!-- Header -->
  <?php include '../includes/header.php'; ?>
  
  <!-- Main Content -->
  <div class="container">
    <div class="main-content">
      <!-- Sidebar -->
      <?php include '../includes/sidebar.php'; ?>
      
      <!-- Content Area -->
      <main class="content">
        <div class="content-header">
          <h1 class="page-title">Product Management</h1>
          <!-- Add Product Button -->
          <button class="btn btn-primary" id="addProductBtn">
            <i class="fas fa-plus"></i> Add Product
          </button>
        </div>

        <!-- Products Table -->
        <div class="orders-table">
          <table id="productsTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Title</th>
                <th>Price</th>
                <th>Special</th>
                <th>Image</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>

        <!-- ================= Add Product Modal ================= -->
        <div class="modal" id="addProductModal">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Add New Product</h5>
              <button class="close-modal" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
              <?php include 'modules/productModal.php'; ?>
              <!-- Include Category Modal -->
       
            </div>
          </div>
        </div>
        

        <!-- ================= Edit Product Modal ================= -->
        <div class="modal" id="editProductModal">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Edit Product</h5>
              <button class="close-modal" id="closeEditModal">&times;</button>
            </div>
            <div class="modal-body">
              <?php include 'modules/editModal.php'; ?>
            </div>
          </div>
        </div>

      </main>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Add/Edit Order Modal -->
  <?php include '../includes/orderModal.php'; ?>
  
  <!-- Theme Toggle -->
  <script src="../assets/js/themeToggle.js"></script>
  <!-- Modal Functionality -->
  <script src="../assets/js/product/productModal.js"></script>
  
  <!-- View Edit Save order -->
  <script src="../assets/js/crud.js"></script>
  <!-- Live Search Functionality -->
  <script src="../assets/js/search.js"></script>
  
  <!-- ================= CRUD Script for Products ================= -->
  <script src="../assets/js/product/productModule.js"></script>
  
</body>
</html>
