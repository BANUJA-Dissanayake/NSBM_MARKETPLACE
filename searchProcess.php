<?php
// AJAX endpoint for the header search bar (present on every page via
// header.php). Receives the typed query via POST from the JS in header.php
// and returns a small HTML fragment of matching products, which gets
// dropped into the #headerSearchResults dropdown under the search box.

session_start();
include "connection.php";
include "includes/productHelper.php";

header("Content-Type: text/html; charset=utf-8");

$search = trim($_POST["search"] ?? "");

if ($search === "") {
    echo "";
    exit;
}

$products = fetchProducts(["search" => $search, "limit" => 6]);

if (empty($products)) {
    echo '<p class="search-no-results">No products match "' . htmlspecialchars($search) . '".</p>';
    exit;
}

foreach ($products as $product) {
    $hasImage = !empty($product["image_url"]);
    ?>
    <a class="search-result-item" href="product.php?product=<?php echo (int) $product["listing_id"]; ?>">
        <span class="search-result-image">
            <?php if ($hasImage): ?>
                <img src="<?php echo htmlspecialchars($product["image_url"]); ?>" alt="<?php echo htmlspecialchars($product["title"]); ?>">
            <?php else: ?>
                <?php echo htmlspecialchars(strtoupper(substr($product["title"], 0, 1))); ?>
            <?php endif; ?>
        </span>
        <span class="search-result-info">
            <span class="search-result-title"><?php echo htmlspecialchars($product["title"]); ?></span>
            <span class="search-result-category"><?php echo htmlspecialchars($product["category_name"]); ?></span>
        </span>
        <strong class="search-result-price">Rs. <?php echo number_format((float) $product["price"], 2); ?></strong>
    </a>
    <?php
}
