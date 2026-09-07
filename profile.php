<?php
session_start();
include "connection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - NSBM</title>

    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/animate.min.css">
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
<?php

function statusBadgeClass($status) {
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

$user_details = null;
$role_name = "";
$initials = "?";
$orders = [];
$latest_news = [];

if (isset($_SESSION["u"]["user_id"])) {
    $user_id = (int) $_SESSION["u"]["user_id"];

    $user_rs = Database::search("SELECT * FROM `user` WHERE user_id = $user_id");
    if ($user_rs && $user_rs->num_rows > 0) {
        $user_details = $user_rs->fetch_assoc();

        if (!empty($user_details["name"])) {
            $initials = "";
            foreach (explode(" ", trim($user_details["name"])) as $part) {
                if ($part !== "") {
                    $initials .= strtoupper($part[0]);
                }
            }
            $initials = substr($initials, 0, 2);
            if ($initials === "") {
                $initials = "?";
            }
        }

        if (!empty($user_details["role"])) {
            $role_rs = Database::search("SELECT role_name FROM role WHERE role_id = " . (int) $user_details["role"]);
            if ($role_rs && $role_rs->num_rows > 0) {
                $role_name = $role_rs->fetch_assoc()["role_name"];
            }
        }

        // One row per order, with all its items collapsed into a single
        // "Title x2, Other Title x1" style label.
        $orders_rs = Database::search("SELECT orders.order_id, orders.order_date, orders.total_amount, orders.status,
                GROUP_CONCAT(CONCAT(listings.title, ' x', order_items.quantity) SEPARATOR ', ') AS items_label
            FROM orders
            INNER JOIN order_items ON orders.order_id = order_items.order_id
            INNER JOIN listings ON order_items.listing_id = listings.listing_id
            WHERE orders.buyer_id = $user_id
            GROUP BY orders.order_id
            ORDER BY orders.order_date DESC");

        if ($orders_rs) {
            while ($row = $orders_rs->fetch_assoc()) {
                $orders[] = $row;
            }
        }

        // Same "latest news" query as index.php's homepage feed.
        $news_rs = Database::search("SELECT news_id, title, created_at FROM news WHERE author_id = $user_id ORDER BY created_at DESC LIMIT 5");
        if ($news_rs) {
            while ($row = $news_rs->fetch_assoc()) {
                $latest_news[] = $row;
            }
        }
    }
}
?>

    <?php include 'header.php'; ?>


    <div class="page-wrap">
    <?php if (!$user_details): ?>
        <center><h1><br><br><br>Please log in first<br><br><br></h1>
        <a href="register.php" class="cyber-btn btn btn-success rounded-pill px-4">Log in</a></center>
    <?php else: ?>

        <!-- LEFT: Profile -->
        <div class="profile-card animate__animated animate__fadeIn">
            <div class="login-logo">
                <i class="fas fa-leaf"></i>
                NSBM
            </div>

            <div class="avatar-circle"><?php echo htmlspecialchars($initials); ?></div>
            <div class="profile-name"><?php echo htmlspecialchars($user_details["name"]); ?></div>
            <div class="profile-role"><?php echo htmlspecialchars($role_name); ?></div>

            <div class="info-row">
                <i class="fas fa-envelope"></i>
                <div>
                    <div class="info-label">Email Address</div>
                    <div class="info-value"><?php echo htmlspecialchars($user_details["email"]); ?></div>
                </div>
            </div>

            <div class="info-row">
                <i class="fas fa-phone"></i>
                <div>
                    <div class="info-label">Phone Number</div>
                    <div class="info-value"><?php echo htmlspecialchars($user_details["phone"] ?? ""); ?></div>
                </div>
            </div>

            <div class="info-row">
                <i class="fas fa-calendar-alt"></i>
                <div>
                    <div class="info-label">Member Since</div>
                    <div class="info-value"><?php echo htmlspecialchars($user_details["created_at"] ?? ""); ?></div>
                </div>
            </div>

            <a href="editProfile.php" class="cyber-btn btn btn-success rounded-pill w-100 fw-semibold mt-3">
                <i class="fas fa-pen me-2"></i>Edit Profile
            </a>
            <a href="logout.php" class="cyber-btn btn btn-outline-danger rounded-pill fw-semibold">
                <i class="fas fa-sign-out-alt me-2"></i>Log Out
            </a>
        </div>

        <!-- RIGHT: Orders + News -->
        <div class="right-column">
            <div class="orders-card animate__animated animate__fadeIn">
                <div class="orders-header">
                    <h2>My Orders</h2>
                    <span class="orders-count"><?php echo count($orders); ?> orders</span>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="empty-orders">
                        <i class="fas fa-box-open fa-2x mb-2"></i>
                        <p>You haven't placed any orders yet.</p>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($orders as $order): ?>
                            <a class="order-item" href="order-details.php?order=<?php echo (int) $order["order_id"]; ?>">
                                <div class="order-icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="order-main">
                                    <div class="order-item-name"><?php echo htmlspecialchars($order["items_label"]); ?></div>
                                    <div class="order-meta">
                                        Order #<?php echo (int) $order["order_id"]; ?> &middot; <?php echo htmlspecialchars($order["order_date"]); ?>
                                    </div>
                                </div>
                                <span class="badge-status <?php echo statusBadgeClass($order["status"]); ?>">
                                    <?php echo htmlspecialchars($order["status"]); ?>
                                </span>
                                <div class="order-total">Rs. <?php echo number_format((float) $order["total_amount"], 2); ?></div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="news-card animate__animated animate__fadeIn">
                <div class="orders-header">
                    <h2>Latest News</h2>
                    <span class="orders-count"><?php echo count($latest_news); ?> updates</span>
                </div>

                <?php if (empty($latest_news)): ?>
                    <div class="empty-orders">
                        <i class="fas fa-newspaper fa-2x mb-2"></i>
                        <p>No news posted yet.</p>
                    </div>
                <?php else: ?>
                    <div class="orders-list">
                        <?php foreach ($latest_news as $item): ?>
                            <a class="order-item" href="news-details.php?id=<?php echo (int) $item["news_id"]; ?>">
                                <div class="order-icon">
                                    <i class="fas fa-newspaper"></i>
                                </div>
                                <div class="order-main">
                                    <div class="order-item-name"><?php echo htmlspecialchars($item["title"]); ?></div>
                                    <div class="order-meta"><?php echo htmlspecialchars($item["created_at"]); ?></div>
                                </div>
                                <span class="badge-status <?php echo htmlspecialchars($item["status"]); ?>">
                                    <?php echo htmlspecialchars($item["status"]); ?>
                                </span>
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
