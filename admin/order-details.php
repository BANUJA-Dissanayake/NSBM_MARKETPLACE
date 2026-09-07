<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$order_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$order = null;
$items = [];
$payment = null;

if ($order_id > 0) {
    $rs = Database::search("SELECT orders.*, `user`.name AS buyer_name, `user`.email AS buyer_email
        FROM orders
        INNER JOIN `user` ON orders.buyer_id = `user`.user_id
        WHERE orders.order_id = $order_id");
    if ($rs && $rs->num_rows > 0) {
        $order = $rs->fetch_assoc();

        $itemsRs = Database::search("SELECT order_items.quantity, order_items.unit_price,
                listings.title, listings.listing_id
            FROM order_items
            INNER JOIN listings ON order_items.listing_id = listings.listing_id
            WHERE order_items.order_id = $order_id");
        if ($itemsRs) {
            while ($row = $itemsRs->fetch_assoc()) {
                $items[] = $row;
            }
        }

        $paymentRs = Database::search("SELECT * FROM payments WHERE order_id = $order_id ORDER BY payment_id DESC LIMIT 1");
        if ($paymentRs && $paymentRs->num_rows > 0) {
            $payment = $paymentRs->fetch_assoc();
        }
    }
}

if (!$order) {
    header("Location: orders.php");
    exit;
}

function orderStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case "completed":
        case "delivered":
            return "active";
        case "cancelled":
            return "deactive";
        default:
            return "unread";
    }
}

$pageTitle = "Order #" . (int) $order["order_id"];
$activeAdminPage = "orders";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 18px;">
        <h2 style="margin:0;">Order #<?php echo (int) $order["order_id"]; ?></h2>
        <button type="button" class="admin-btn admin-btn-danger" onclick="deleteOrder(<?php echo (int) $order['order_id']; ?>, true)"><i class="fas fa-trash"></i> Remove order</button>
    </div>

    <div class="admin-form-row">
        <div>
            <p class="admin-field-static-label">Buyer</p>
            <p><?php echo htmlspecialchars($order["buyer_name"]); ?> (<?php echo htmlspecialchars($order["buyer_email"]); ?>)</p>
        </div>
        <div>
            <p class="admin-field-static-label">Order date</p>
            <p><?php echo htmlspecialchars($order["order_date"]); ?></p>
        </div>
        <div>
            <p class="admin-field-static-label">Status</p>
            <p id="order-status-<?php echo (int) $order["order_id"]; ?>">
                <span class="admin-badge <?php echo orderStatusBadgeClass($order["status"]); ?>"><?php echo htmlspecialchars($order["status"]); ?></span>
            </p>
            <?php $isCompleted = strtolower($order["status"]) === "completed"; ?>
            <button type="button" class="admin-btn admin-btn-outline" id="toggle-order-btn-<?php echo (int) $order["order_id"]; ?>"
                    onclick="toggleOrderStatus(<?php echo (int) $order["order_id"]; ?>, '<?php echo $isCompleted ? "pending" : "completed"; ?>')">
                <?php echo $isCompleted ? "Mark Pending" : "Mark Completed"; ?>
            </button>
        </div>
        <div>
            <p class="admin-field-static-label">Payment</p>
            <p><?php echo $payment ? htmlspecialchars($payment["payment_method"]) . " (" . htmlspecialchars($payment["payment_status"]) . ")" : "No payment recorded"; ?></p>
        </div>
    </div>

    <?php if (!empty($order["shipping_name"])): ?>
        <p class="admin-field-static-label">Shipping to</p>
        <p><?php echo htmlspecialchars($order["shipping_name"]); ?>, <?php echo htmlspecialchars($order["shipping_address"]); ?>, <?php echo htmlspecialchars($order["shipping_city"]); ?> &middot; <?php echo htmlspecialchars($order["shipping_phone"]); ?></p>
    <?php endif; ?>
</div>

<div class="admin-panel">
    <h2>Items</h2>
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit price</th>
                    <th>Line total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item["title"]); ?></td>
                        <td><?php echo (int) $item["quantity"]; ?></td>
                        <td>Rs. <?php echo number_format((float) $item["unit_price"], 2); ?></td>
                        <td>Rs. <?php echo number_format((float) $item["quantity"] * (float) $item["unit_price"], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div style="text-align:right; margin-top:16px; font-weight:800; font-size:1.05rem;">
        Total: Rs. <?php echo number_format((float) $order["total_amount"], 2); ?>
    </div>
</div>

<a class="admin-btn admin-btn-outline" href="orders.php"><i class="fas fa-arrow-left"></i> Back to orders</a>
<?php include "includes/admin-layout-bottom.php"; ?>
