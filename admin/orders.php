<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$orders = [];
$rs = Database::search("SELECT orders.*, `user`.name AS buyer_name, `user`.email AS buyer_email,
        (SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.order_id) AS item_count
    FROM orders
    INNER JOIN `user` ON orders.buyer_id = `user`.user_id
    ORDER BY orders.order_date DESC");
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $orders[] = $row;
    }
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

$pageTitle = "Orders";
$activeAdminPage = "orders";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>All orders (<?php echo count($orders); ?>)</h2>
    <div class="admin-table-wrap">
        <?php if (empty($orders)): ?>
            <p class="admin-empty">No orders yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Buyer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr id="order-row-<?php echo (int) $order["order_id"]; ?>">
                            <td>#<?php echo (int) $order["order_id"]; ?></td>
                            <td>
                                <?php echo htmlspecialchars($order["buyer_name"]); ?><br>
                                <span style="color:#55705d;font-size:0.82rem;"><?php echo htmlspecialchars($order["buyer_email"]); ?></span>
                            </td>
                            <td><?php echo (int) $order["item_count"]; ?></td>
                            <td>Rs. <?php echo number_format((float) $order["total_amount"], 2); ?></td>
                            <td><?php echo htmlspecialchars($order["order_date"]); ?></td>
                            <td id="order-status-<?php echo (int) $order["order_id"]; ?>">
                                <span class="admin-badge <?php echo orderStatusBadgeClass($order["status"]); ?>"><?php echo htmlspecialchars($order["status"]); ?></span>
                            </td>
                            <td class="admin-table-actions">
                                <a class="admin-btn admin-btn-outline" href="order-details.php?id=<?php echo (int) $order["order_id"]; ?>"><i class="fas fa-eye"></i> View</a>
                                <?php $isCompleted = strtolower($order["status"]) === "completed"; ?>
                                <button type="button" class="admin-btn admin-btn-outline" id="toggle-order-btn-<?php echo (int) $order["order_id"]; ?>"
                                        onclick="toggleOrderStatus(<?php echo (int) $order["order_id"]; ?>, '<?php echo $isCompleted ? "pending" : "completed"; ?>')">
                                    <?php echo $isCompleted ? "Mark Pending" : "Mark Completed"; ?>
                                </button>
                                <button type="button" class="admin-btn admin-btn-danger" onclick="deleteOrder(<?php echo (int) $order["order_id"]; ?>)"><i class="fas fa-trash"></i> Remove</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
