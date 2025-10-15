<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>
    
    <!-- Main Content -->
    <div class="container">
        <div class="main-content">
            <!-- Sidebar -->
            <?php include 'includes/sidebar.php'; ?>
            
            <!-- Content Area -->
            <main class="content">
                <div class="content-header">
                    <h1 class="page-title">Order Management</h1>
                    <button class="btn btn-primary" id="addOrderBtn">
                        <i class="fas fa-plus"></i> Add New Order
                    </button>
                </div>

                <!-- Filter -->
                    <?php include 'includes/filter.php'?>
                <!-- Order Table  -->
                    <?php include 'includes/orderTable.php' ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Add/Edit Order Modal -->
    <?php include 'includes/orderModal.php'; ?>
    <!-- Theme Toggle -->
    <script src="assets/js/themeToggle.js"></script>
    <!-- Modal Functionality -->
    <script src="assets/js/modal.js"></script>
    <!-- View Edit Save order -->
    <script src="assets/js/crud.js"></script>
    <!-- Live Search Functionality -->
    <script src="assets/js/search.js"></script>
</body>
</html>