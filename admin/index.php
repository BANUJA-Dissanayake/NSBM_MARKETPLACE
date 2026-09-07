<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

function countRows($sql) {
    $rs = Database::search($sql);
    if ($rs && $row = $rs->fetch_assoc()) {
        return (int) reset($row);
    }
    return 0;
}

$productCount = countRows("SELECT COUNT(*) FROM listings WHERE status = 'active'");
$userCount = countRows("SELECT COUNT(*) FROM `user`");
$categoryCount = countRows("SELECT COUNT(*) FROM categories");
$unreadMessageCount = countRows("SELECT COUNT(*) FROM messages WHERE is_read = 0");
$adCount = countRows("SELECT COUNT(*) FROM advertisements WHERE is_active = 1");
$newsCount = countRows("SELECT COUNT(*) FROM news WHERE is_active = 1");

$totalOrders = countRows("SELECT COUNT(*) FROM orders");

$totalSales = 0;
$salesRs = Database::search("SELECT SUM(total_amount) AS total FROM orders");
if ($salesRs && $row = $salesRs->fetch_assoc()) {
    $totalSales = (float) ($row["total"] ?? 0);
}

$avgOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

$pageTitle = "Dashboard";
$activeAdminPage = "dashboard";
include "includes/admin-layout-top.php";
?>
<h2 class="admin-section-heading">Sales overview</h2>
<div class="admin-stats admin-stats-primary">
    <div class="admin-stat-card">
        <i class="fas fa-sack-dollar"></i>
        <div class="stat-value">Rs. <?php echo number_format($totalSales, 2); ?></div>
        <div class="stat-label">Total sales</div>
    </div>
    <a class="admin-stat-card" href="orders.php" style="text-decoration:none; color:inherit; display:block;">
        <i class="fas fa-cart-shopping"></i>
        <div class="stat-value"><?php echo $totalOrders; ?></div>
        <div class="stat-label">Total orders</div>
    </a>
    <div class="admin-stat-card">
        <i class="fas fa-chart-line"></i>
        <div class="stat-value">Rs. <?php echo number_format($avgOrderValue, 2); ?></div>
        <div class="stat-label">Average order value</div>
    </div>
</div>

<div class="admin-stats">
    <div class="admin-stat-card">
        <i class="fas fa-box"></i>
        <div class="stat-value"><?php echo $productCount; ?></div>
        <div class="stat-label">Active products</div>
    </div>
    <div class="admin-stat-card">
        <i class="fas fa-users"></i>
        <div class="stat-value"><?php echo $userCount; ?></div>
        <div class="stat-label">Registered users</div>
    </div>
    <div class="admin-stat-card">
        <i class="fas fa-layer-group"></i>
        <div class="stat-value"><?php echo $categoryCount; ?></div>
        <div class="stat-label">Categories</div>
    </div>
    <div class="admin-stat-card">
        <i class="fas fa-bullhorn"></i>
        <div class="stat-value"><?php echo $adCount; ?></div>
        <div class="stat-label">Active ads</div>
    </div>
    <div class="admin-stat-card">
        <i class="fas fa-newspaper"></i>
        <div class="stat-value"><?php echo $newsCount; ?></div>
        <div class="stat-label">Active articles</div>
    </div>
    <div class="admin-stat-card">
        <i class="fas fa-envelope"></i>
        <div class="stat-value"><?php echo $unreadMessageCount; ?></div>
        <div class="stat-label">Unread messages</div>
    </div>
</div>

<div class="admin-panel">
    <h2>Quick links</h2>
    <div class="admin-table-actions" style="flex-wrap: wrap; gap: 12px;">
        <a class="admin-btn" href="products.php"><i class="fas fa-plus"></i> Add a product</a>
        <a class="admin-btn admin-btn-outline" href="orders.php"><i class="fas fa-cart-shopping"></i> View orders</a>
        <a class="admin-btn admin-btn-outline" href="users.php"><i class="fas fa-users"></i> Manage users</a>
        <a class="admin-btn admin-btn-outline" href="categories.php"><i class="fas fa-layer-group"></i> Manage categories</a>
        <a class="admin-btn admin-btn-outline" href="advertisements.php"><i class="fas fa-bullhorn"></i> Manage ads</a>
        <a class="admin-btn admin-btn-outline" href="news.php"><i class="fas fa-newspaper"></i> Manage news</a>
        <a class="admin-btn admin-btn-outline" href="messages.php"><i class="fas fa-envelope"></i> View messages</a>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
