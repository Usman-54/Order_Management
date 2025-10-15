<?php


// 🩵 Fix: if both admin and customer sessions exist, keep only customer session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userType = "Guest";
$userName = "Guest User";

if (isset($_SESSION['role']) && isset($_SESSION['user_name'])) {
    $userName = $_SESSION['user_name'];
    if ($_SESSION['role'] === 'admin') {
        $userType = "Admin";
    } elseif ($_SESSION['role'] === 'employer') {
        $userType = "Employer";
    } elseif ($_SESSION['role'] === 'customer') {
        $userType = "Customer";
    }
}

?>

<!-- ✅ Include Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<header>
    <div class="container">
        <div class="header-content">
            <!-- ✅ Logo -->
            <div class="logo">
                <i class="fas fa-boxes"></i>
                OrderManager Pro
            </div>

            <!-- ✅ Search Bar -->
            <div class="search-bar">
                <input type="text" placeholder="Search orders...">
                <i class="fas fa-search"></i>
            </div>

            <!-- ✅ Header Actions -->
            <div class="header-actions">
                <button class="theme-toggle" id="themeToggle">
                    <i class="fas fa-moon"></i>
                </button>

                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <span>
                        <?= htmlspecialchars($userType) ?>:
                        <?= htmlspecialchars($userName) ?>
                    </span>
                </div>

                <!-- ✅ Blue Logout Icon -->
                <!-- Logout button -->
                <!-- Blue Logout Icon -->
<?php if ($userType !== "Guest"): ?>
    <a href="../includes/logout.php" class="logout-icon" title="Logout">
        <i class="fa-solid fa-right-from-bracket"></i>
    </a>
<?php endif; ?>


            </div>
        </div>
    </div>
</header>

<!-- ✅ Style for the Logout Button -->
<style>
.logout-icon {
    background-color: #007bff; /* Blue background */
    color: #fff;               /* White icon */
    border-radius: 6px;
    padding: 8px 12px;
    margin-left: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: background-color 0.3s ease;
}

.logout-icon:hover {
    background-color: #0056b3; /* Darker blue */
}


.logout-icon i {
    font-size: 1rem;
}
</style>



    <!-- <header>
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-boxes"></i>
                    OrderManager Pro
                </div>
                
                <div class="search-bar">
                    <input type="text" placeholder="Search orders...">
                    <i class="fas fa-search"></i>
                </div>
                
                <div class="header-actions">
                    <button class="theme-toggle" id="themeToggle">
                        <i class="fas fa-moon"></i>
                    </button>
                    
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <span>Admin User</span>
                    </div>
                </div>
            </div>
        </div>
    </header> -->