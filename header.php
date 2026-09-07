<?php
include_once __DIR__ . "/connection.php";
include_once __DIR__ . "/includes/session-check.php";

$search = "";
if (isset($_GET["search"])) {
    $search = $_GET["search"];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NSBM Marketplace</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/header-cyber.css">
</head>
<body class="bg-eco-soft text-dark">
    <header class="bg-white site-header">
        <div class="site-header-top shadow-sm" id="siteHeaderTop">
            <div class="header-container site-header-top-inner">
                <a class="navbar-brand site-logo" href="index.php">
                    <img src="images/nsbm-marketplace-logo.png" alt="NSBM Marketplace">
                </a>

                <form class="search-form flex-grow-1" role="search" onsubmit="return searchHeaderProducts(event)">
                    <div class="search-wrap position-relative">
                        <input id="headerSearchInput" class="form-control bg-eco-soft text-dark border border-success-subtle rounded-pill shadow-sm pe-5" type="search" name="search" placeholder="<?php if(isset($searchPlaceholder)): echo $searchPlaceholder; endif; ?> Search" aria-label="Search" autocomplete="off">
                        <button class="search-btn cyber-btn btn rounded-circle position-absolute top-50 translate-middle-y end-0 me-1 d-flex align-items-center justify-content-center" type="submit" aria-label="Search">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.005 1.005 0 0 0-.115-.098ZM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0Z"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="site-header-account">
                    <?php if (isAdmin()): ?>
                        <a class="header-action" href="admin/index.php" title="Admin panel">Admin</a>
                    <?php endif; ?>

                    <?php if (isset($_SESSION["u"]["user_id"])) {
                            $data = $_SESSION["u"];

                            $initials = "?";
                            if (!empty($data["name"])) {
                                $initials = "";
                                foreach (explode(" ", trim($data["name"])) as $part) {
                                    if ($part !== "") {
                                        $initials .= strtoupper($part[0]);
                                    }
                                }
                                $initials = substr($initials, 0, 2);
                                if ($initials === "") {
                                    $initials = "?";
                                }
                            }
                        ?>
                        <a class="header-avatar" href="profile.php" title="<?php echo htmlspecialchars($data["name"] ?? "Profile"); ?>" aria-label="Open your profile">
                            <?php echo htmlspecialchars($initials); ?>
                        </a>
                    <?php
                        } else {
                        ?>
                        <a class="cyber-btn btn btn-success rounded-pill px-4 fw-semibold" href="register.php">Login</a>
                    <?php
                     }
                    ?>

                    <button class="navbar-toggler border-success text-success d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavRow" aria-controls="mainNavRow" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="site-header-top-spacer" id="siteHeaderTopSpacer"></div>

        <div class="site-header-nav">
            <div class="header-container">
                <div class="collapse navbar-collapse" id="mainNavRow">
                    <div class="site-header-nav-inner">
                        <ul class="nav-links-list">
                            <li class="nav-item home-link"><a class="cyber-link nav-link text-dark fw-semibold" href="index.php">Home</a></li>
                            <li class="nav-item"><a class="cyber-link nav-link text-dark fw-semibold" href="shop.php">Shop</a></li>
                            <li class="nav-item"><a class="cyber-link nav-link text-dark fw-semibold" href="categories.php">Categories</a></li>
                            <li class="nav-item"><a class="cyber-link nav-link text-dark fw-semibold" href="newspage.php">News</a></li>
                            <li class="nav-item"><a class="cyber-link nav-link text-dark fw-semibold" href="abut.php">About</a></li>
                            <li class="nav-item"><a class="cyber-link nav-link text-dark fw-semibold" href="contact.php">Contact</a></li>
                        </ul>

                        <div class="nav-icons">
                            <a class="header-action" href="wishlist.php" aria-label="Open wishlist" title="Wishlist">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                            <a class="header-action" href="cart.php" aria-label="Open shopping cart" title="Shopping cart">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M3 3h2l2.4 11.2a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.4L21 7H6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <circle cx="10" cy="20" r="1.2" fill="currentColor"/>
                                    <circle cx="18" cy="20" r="1.2" fill="currentColor"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script>
        // Header search bar (this file is included on every page, so this
        // script travels with it). Results always surface on shop.php's
        // product grid: if we're already there, filter in place using its
        // existing search/category/price/sort pipeline (loadProducts() in
        // js/main.js, backed by loadProduct.php); otherwise send the visitor
        // to shop.php?search=... so it can run that same filter on load.
        function searchHeaderProducts(event) {
            if (event) {
                event.preventDefault();
            }

            var input = document.getElementById("headerSearchInput");
            if (!input) {
                return false;
            }

            var query = input.value.trim();
            var shopSearchInput = document.getElementById("productSearch");

            if (shopSearchInput && typeof loadProducts === "function") {
                shopSearchInput.value = query;
                loadProducts();
            } else {
                window.location = "shop.php" + (query ? "?search=" + encodeURIComponent(query) : "");
            }

            return false;
        }

        // Only the top row (logo/search/account) is fixed while scrolling -
        // the nav row below it scrolls away with the page. Since the fixed
        // row is taken out of normal flow, this spacer reserves the same
        // height so the nav row doesn't jump up underneath it. Recalculated
        // on load/resize because the top row's height changes when it wraps
        // onto multiple lines on narrow screens.
        function syncHeaderTopSpacer() {
            var topRow = document.getElementById("siteHeaderTop");
            var spacer = document.getElementById("siteHeaderTopSpacer");
            if (topRow && spacer) {
                spacer.style.height = topRow.offsetHeight + "px";
            }
        }

        window.addEventListener("load", syncHeaderTopSpacer);
        window.addEventListener("resize", syncHeaderTopSpacer);
        syncHeaderTopSpacer();
    </script>
</body>
</html>
