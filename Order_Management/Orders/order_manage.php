<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include("../backend/db.php");

/* ────────────── DELETE ORDER ────────────── */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM orders WHERE id=$id");
    echo "Deleted"; 
    exit;
}

/* ────────────── VIEW ORDER DETAILS ────────────── */
if (isset($_GET['view'])) {
    $id = intval($_GET['view']);
    $order = $conn->query("SELECT * FROM orders WHERE id=$id")->fetch_assoc();
    if (!$order) { echo "<p class='text-danger'>Order not found!</p>"; exit; }

    $cartHTML = "";
    $cartTotal = 0;

    if (!empty($order['cart'])) {
        $cart = json_decode($order['cart'], true);
        if (is_array($cart)) {
            foreach ($cart as $item) {
                $lineTotal = $item['price'] * $item['quantity'];
                $cartTotal += $lineTotal;
                $extras = !empty($item['extras']) ? "<div><strong>Extras:</strong> " . htmlspecialchars(is_array($item['extras']) ? implode(', ', $item['extras']) : $item['extras']) . "</div>" : "";
                $drinks = !empty($item['drinks']) ? "<div><strong>Drinks:</strong> " . htmlspecialchars(is_array($item['drinks']) ? implode(', ', $item['drinks']) : $item['drinks']) . "</div>" : "";
                $sauces = !empty($item['sauce']) ? "<div><strong>Sauce:</strong> " . htmlspecialchars(is_array($item['sauce']) ? implode(', ', $item['sauce']) : $item['sauce']) . "</div>" : "";

                $cartHTML .= "<tr>
                    <td><strong>".htmlspecialchars($item['title'])."</strong>$extras$drinks$sauces</td>
                    <td>{$item['quantity']}</td>
                    <td>Rs ".number_format($item['price'],2)."</td>
                    <td>Rs ".number_format($lineTotal,2)."</td>
                </tr>";
            }
        }
    }

    $final = ($order['final_price'] > 0) ? $order['final_price'] : $cartTotal;
    ?>
    <div class="p-3">
        <h4 class="mb-3 text-primary">Order #<?= $order['id'] ?></h4>
        <div class="mb-3">
            <strong>Customer:</strong> <?= htmlspecialchars($order['name']) ?><br>
            <strong>Date:</strong> <?= htmlspecialchars($order['order_date']) ?><br>
            <strong>Status:</strong> <span class="badge bg-secondary"><?= htmlspecialchars($order['status']) ?></span>
        </div>

        <h5 class="mt-4 mb-2 text-decoration-underline">Order Details</h5>
        <table class="table table-sm table-bordered">
            <thead class="table-light">
                <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr>
            </thead>
            <tbody><?= $cartHTML ?: '<tr><td colspan="4" class="text-muted">No products</td></tr>' ?></tbody>
        </table>

        <div class="d-flex justify-content-between border-top pt-2 fw-bold">
            <span>Total</span>
            <span>Rs <?= number_format($final,2) ?></span>
        </div>

        <p class="mt-3"><strong>Notes:</strong> <?= htmlspecialchars($order['notes'] ?: 'No notes') ?></p>

        <div class="text-end mt-4">
            <button class="btn btn-secondary" onclick="document.getElementById('orderModal').style.display='none'">Close</button>
        </div>
    </div>
    <?php
    exit;
}

/* ────────────── EDIT ORDER FORM ────────────── */
if (isset($_GET['update'])) {
    $id = intval($_GET['update']);
    $order = $conn->query("SELECT * FROM orders WHERE id=$id")->fetch_assoc();
    if (!$order) { echo "<p class='text-danger'>Order not found!</p>"; exit; }
    ?>
    <div class="p-3">
        <h4 class="mb-3 text-success">Update Order #<?= $order['id'] ?></h4>
        <form id="updateOrderForm">
            <input type="hidden" name="id" value="<?= $order['id'] ?>">

            <div class="mb-3">
                <label class="form-label fw-semibold">Customer Name</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($order['name']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
    <option value="Pending"   <?= $order['status']=='Pending'?'selected':'' ?>>Pending</option>
    <option value="Accept"    <?= $order['status']=='Accept'?'selected':'' ?>>Accept</option>
    <option value="Prepping"  <?= $order['status']=='Prepping'?'selected':'' ?>>Prepping</option>
    <option value="Ready"     <?= $order['status']=='Ready'?'selected':'' ?>>Ready</option>
    <option value="Completed" <?= $order['status']=='Completed'?'selected':'' ?>>Completed/Pickup</option>
    <option value="Rejected"  <?= $order['status']=='Rejected'?'selected':'' ?>>Decline</option>
</select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Total Amount</label>
                <input type="number" step="0.01" name="final_price" class="form-control" value="<?= $order['final_price'] ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Notes</label>
                <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($order['notes']) ?></textarea>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success w-50 me-2">Save</button>
                <button type="button" class="btn btn-secondary w-50" onclick="document.getElementById('orderModal').style.display='none'">Cancel</button>
            </div>
        </form>
    </div>
    <?php
    exit;
}

/* ────────────── SAVE UPDATE ────────────── */
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $id = intval($_POST['id'] ?? 0);
    if($id <= 0){ echo "Invalid ID"; exit; }

    $statuses = ['Pending','Accept','Prepping','Ready','Completed','Rejected'];

    // Inline status update only — ONLY if name is NOT set
    if(isset($_POST['status']) && !isset($_POST['name'])){
        $status = $_POST['status'];
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $stmt->execute();
        $stmt->close();
        echo "Success";
        exit;
    }

    // Full update (from modal)
    $name = trim($_POST['name'] ?? '');
    $status = trim($_POST['status'] ?? 'Pending');
    $notes = trim($_POST['notes'] ?? '');
    $final_price = isset($_POST['final_price']) && $_POST['final_price'] !== '' ? floatval($_POST['final_price']) : 0;

    $stmt = $conn->prepare("UPDATE orders SET name=?, status=?, notes=?, final_price=? WHERE id=?");
    $stmt->bind_param("sssdi", $name, $status, $notes, $final_price, $id);
    echo $stmt->execute() ? "Success" : "Error: ".$stmt->error;
    $stmt->close();
    exit;
}
