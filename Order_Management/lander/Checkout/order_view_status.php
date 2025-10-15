<?php
// order_view_status.php — fragment (do NOT output full <html> when included via AJAX)
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . "/../../backend/db.php";

// Prefer GET id, fall back to session-stored current_order_id
$orderId = isset($_GET['id']) ? intval($_GET['id']) : (isset($_SESSION['current_order_id']) ? intval($_SESSION['current_order_id']) : 0);

if ($orderId <= 0) {
    // Graceful fragment if no order id
    ?>
    <div class="alert alert-warning">No active order to track.</div>
    <?php
    return;
}

// Protect query and check
$stmt = $conn->prepare("SELECT status FROM orders WHERE id = ?");
if (!$stmt) {
    echo '<div class="alert alert-danger">Database error.</div>';
    return;
}
$stmt->bind_param("i", $orderId);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();
$status = $order['status'] ?? 'Pending';

// Map status to image/GIF and loader color
$statusMap = [
    'Pending'   => ['label' => 'Pending',   'color' => '#0d6efd', 'img' => '../../assets/images/pending.jpg', 'percent' => 10],
    'Accept'    => ['label' => 'Accepted',  'color' => '#6f42c1', 'img' => '../../assets/images/accepted.png', 'percent' => 25],
    'Prepping'  => ['label' => 'Cooking',   'color' => '#ffc107', 'img' => '../../assets/images/cook.gif', 'percent' => 50],
    'Ready'     => ['label' => 'Ready',     'color' => '#fd7e14', 'img' => '../../assets/images/prepare.jpg', 'percent' => 75],
    'Completed' => ['label' => 'Completed', 'color' => '#198754', 'img' => '../../assets/images/carring.jpg', 'percent' => 100],
];

$current = $statusMap[$status] ?? $statusMap['Pending'];
?>

<div class="order-status-fragment text-center">
    <h5 class="mb-3">Cooking Interval 🧑‍🍳</h5>

    <div class="cooking-area mb-3" style="width:250px;height:250px;margin:0 auto;border-radius:12px;overflow:hidden;border:1px solid #e9ecef;background:#fff;">
        <img id="chef-img" src="<?= htmlspecialchars($current['img']) ?>" alt="Cooking Animation" style="width:100%;height:100%;object-fit:cover;">
    </div>

    <h6>Cooking Timer: <span id="timer-display" class="fw-bold text-danger">--:--</span></h6>

    <div class="progress mx-auto mb-2" style="width:60%;height:28px;border-radius:16px;overflow:hidden;">
        <div id="cooking-loader" class="progress-bar progress-bar-striped progress-bar-animated"
             role="progressbar"
             style="width: <?= intval($current['percent']) ?>%; background-color: <?= htmlspecialchars($current['color']) ?>;"
             aria-valuenow="<?= intval($current['percent']) ?>" aria-valuemin="0" aria-valuemax="100"></div>
    </div>

    <div class="stage-labels" style="display:flex;justify-content:space-between;width:80%;margin:10px auto;font-weight:600;font-size:0.9rem;">
        <span style="color:#0d6efd">Pending</span>
        <span style="color:#6f42c1">Accept</span>
        <span style="color:#ffc107">Cooking</span>
        <span style="color:#fd7e14">Ready</span>
        <span style="color:#198754">Completed</span>
    </div>

    <p id="stage-text" class="mt-2 fw-semibold"><?= htmlspecialchars($current['label']) ?></p>
</div>


