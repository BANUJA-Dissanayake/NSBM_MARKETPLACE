<?php
// Shared chrome for every admin page. Expects $pageTitle and
// $activeAdminPage to already be set by the including page.
function adminNavClass($page, $current) {
    return $page === $current ? "active" : "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? "Admin"); ?> | NSBM Admin</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/fontawesome.min.css">
    <?php
    // Cache-busted with the file's own mtime: InfinityFree serves static
    // assets with Cache-Control: max-age=2592000 (30 days), so without this
    // a browser that loaded the admin panel before an admin.css change would
    // keep using the stale copy for up to a month.
    $adminCssVersion = @filemtime(__DIR__ . "/../../css/admin.css") ?: time();
    ?>
    <link rel="stylesheet" href="../css/admin.css?v=<?php echo $adminCssVersion; ?>">
</head>
<body class="admin-body">
    <div class="admin-shell">
        <aside class="admin-sidebar">
            <div class="admin-brand">
                <span><i class="fas fa-leaf"></i> NSBM Admin</span>
                <button type="button" class="admin-nav-toggle" data-bs-toggle="collapse" data-bs-target="#adminNavCollapse" aria-controls="adminNavCollapse" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            <div class="collapse admin-nav-collapse" id="adminNavCollapse">
                <nav class="admin-nav">
                    <a href="index.php" class="<?php echo adminNavClass("dashboard", $activeAdminPage); ?>"><i class="fas fa-gauge-high"></i> Dashboard</a>
                    <a href="products.php" class="<?php echo adminNavClass("products", $activeAdminPage); ?>"><i class="fas fa-box"></i> Products</a>
                    <a href="orders.php" class="<?php echo adminNavClass("orders", $activeAdminPage); ?>"><i class="fas fa-cart-shopping"></i> Orders</a>
                    <a href="users.php" class="<?php echo adminNavClass("users", $activeAdminPage); ?>"><i class="fas fa-users"></i> Users</a>
                    <a href="categories.php" class="<?php echo adminNavClass("categories", $activeAdminPage); ?>"><i class="fas fa-layer-group"></i> Categories</a>
                    <a href="advertisements.php" class="<?php echo adminNavClass("advertisements", $activeAdminPage); ?>"><i class="fas fa-bullhorn"></i> Advertisements</a>
                    <a href="news.php" class="<?php echo adminNavClass("news", $activeAdminPage); ?>"><i class="fas fa-newspaper"></i> News</a>
                    <a href="messages.php" class="<?php echo adminNavClass("messages", $activeAdminPage); ?>"><i class="fas fa-envelope"></i> Messages</a>
                </nav>
                <div class="admin-sidebar-footer">
                    <a href="../index.php"><i class="fas fa-arrow-left"></i> Back to site</a>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log out</a>
                </div>
            </div>
        </aside>

        <main class="admin-main">
            <header class="admin-topbar">
                <h1><?php echo htmlspecialchars($pageTitle ?? "Dashboard"); ?></h1>
                <span class="admin-topbar-user"><i class="fas fa-user-shield"></i> <?php echo htmlspecialchars($_SESSION["u"]["name"] ?? "Admin"); ?></span>
            </header>
            <div class="admin-content">