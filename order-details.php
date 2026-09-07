<?php
session_start();
include "connection.php";
include "includes/session-check.php";

requireLoginOrRedirect();

$user_id = currentUserId();
$order_id = isset($_GET["order"]) ? (int) $_GET["order"] : 0;

$order = null;
$items = [];
$payment = null;

if ($order_id > 0) {
    // buyer_id check keeps a buyer from viewing someone else's order by
    // guessing an id in the URL.
    $order_rs = Database::search("SELECT * FROM orders WHERE order_id = $order_id AND buyer_id = $user_id");
    if ($order_rs && $order_rs->num_rows > 0) {
        $order = $order_rs->fetch_assoc();

        $items_rs = Database::search("SELECT order_items.quantity, order_items.unit_price,
                listings.title, listings.listing_id,
                (SELECT image_url FROM listing_images WHERE listing_images.listing_id = listings.listing_id LIMIT 1) AS image_url
            FROM order_items
            INNER JOIN listings ON order_items.listing_id = listings.listing_id
            WHERE order_items.order_id = $order_id");
        if ($items_rs) {
            while ($row = $items_rs->fetch_assoc()) {
                $items[] = $row;
            }
        }

        $payment_rs = Database::search("SELECT * FROM payments WHERE order_id = $order_id ORDER BY payment_id DESC LIMIT 1");
        if ($payment_rs && $payment_rs->num_rows > 0) {
            $payment = $payment_rs->fetch_assoc();
        }
    }
}

function orderStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case "completed":
        case "delivered":
            return "badge-success";
        case "pending":
        case "processing":
            return "badge-warning";
        case "cancelled":
            return "badge-danger";
        default:
            return "badge-secondary";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order details | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/order-details.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="order-details-page">
        <p class="order-breadcrumb"><a href="profile.php">My orders</a> <span aria-hidden="true">/</span> Order details</p>

        <?php if (!$order): ?>
            <p class="order-not-found">We couldn't find that order. <a href="profile.php">Back to your orders</a></p>
        <?php else: ?>
            <header class="order-details-heading">
                <div>
                    <h1>Order #<?php echo (int) $order["order_id"]; ?></h1>
                    <p>Placed on <?php echo htmlspecialchars($order["order_date"]); ?></p>
                </div>
                <span class="badge-status <?php echo orderStatusBadgeClass($order["status"]); ?>"><?php echo htmlspecialchars($order["status"]); ?></span>
            </header>

            <div class="order-details-layout">
                <section class="order-items-card" aria-label="Order items">
                    <h2>Items</h2>
                    <?php foreach ($items as $item): ?>
                        <div class="order-line-item">
                            <div class="order-line-image">
                                <?php if (!empty($item["image_url"])): ?>
                                    <img src="<?php echo htmlspecialchars($item["image_url"]); ?>" alt="<?php echo htmlspecialchars($item["title"]); ?>">
                                <?php else: ?>
                                    <?php echo htmlspecialchars($item["title"]); ?>
                                <?php endif; ?>
                            </div>
                            <div class="order-line-info">
                                <h3><?php echo htmlspecialchars($item["title"]); ?></h3>
                                <span>Qty <?php echo (int) $item["quantity"]; ?> &middot; Rs. <?php echo number_format((float) $item["unit_price"], 2); ?> each</span>
                            </div>
                            <strong class="order-line-total">Rs. <?php echo number_format((float) $item["quantity"] * (float) $item["unit_price"], 2); ?></strong>
                        </div>
                    <?php endforeach; ?>
                    <div class="order-items-total">
                        <span>Total</span>
                        <strong>Rs. <?php echo number_format((float) $order["total_amount"], 2); ?></strong>
                    </div>
                </section>

                <aside class="order-info-card">
                    <h2>Delivery details</h2>
                    <?php if (!empty($order["shipping_name"])): ?>
                        <p class="order-info-row"><strong><?php echo htmlspecialchars($order["shipping_name"]); ?></strong></p>
                        <p class="order-info-row"><?php echo htmlspecialchars($order["shipping_address"]); ?>, <?php echo htmlspecialchars($order["shipping_city"]); ?></p>
                        <p class="order-info-row"><?php echo htmlspecialchars($order["shipping_phone"]); ?></p>
                    <?php else: ?>
                        <p class="order-info-row">No delivery details recorded for this order.</p>
                    <?php endif; ?>

                    <h2>Payment</h2>
                    <?php if ($payment): ?>
                        <p class="order-info-row"><?php echo htmlspecialchars($payment["payment_method"]); ?></p>
                        <p class="order-info-row">Status: <?php echo htmlspecialchars($payment["payment_status"]); ?></p>
                    <?php else: ?>
                        <p class="order-info-row">No payment recorded for this order.</p>
                    <?php endif; ?>
                </aside>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
