<?php
// Shared helpers for querying and rendering listings, so shop.php's initial
// page load and loadProduct.php's AJAX responses always stay in sync.

// Shared by fetchProducts() and countProducts() so the row list and the
// total count are always filtered the exact same way.
function buildProductWhereClause($filters) {
    $where = ["listings.status = 'active'"];

    if (!empty($filters["search"])) {
        $s = Database::escape($filters["search"]);
        $where[] = "(listings.title LIKE '%$s%' OR listings.description LIKE '%$s%')";
    }

    if (!empty($filters["category"]) && $filters["category"] !== "all") {
        $categoryId = (int) $filters["category"];
        $where[] = "listings.category_id = $categoryId";
    }

    if (isset($filters["min_price"]) && $filters["min_price"] !== "") {
        $min = (float) $filters["min_price"];
        $where[] = "listings.price >= $min";
    }

    if (isset($filters["max_price"]) && $filters["max_price"] !== "") {
        $max = (float) $filters["max_price"];
        $where[] = "listings.price <= $max";
    }

    return implode(" AND ", $where);
}

function fetchProducts($filters = []) {
    $whereSql = buildProductWhereClause($filters);

    $orderBy = "listings.created_at DESC";
    switch ($filters["sort"] ?? "") {
        case "price-low":
            $orderBy = "listings.price ASC";
            break;
        case "price-high":
            $orderBy = "listings.price DESC";
            break;
        case "name":
            $orderBy = "listings.title ASC";
            break;
    }

    $limitSql = "";
    if (!empty($filters["per_page"])) {
        $perPage = (int) $filters["per_page"];
        $page = max(1, (int) ($filters["page"] ?? 1));
        $offset = ($page - 1) * $perPage;
        $limitSql = " LIMIT $perPage OFFSET $offset";
    } else if (!empty($filters["limit"])) {
        $limitSql = " LIMIT " . (int) $filters["limit"];
    }

    $sql = "SELECT listings.*, categories.category_name,
                (SELECT image_url FROM listing_images
                 WHERE listing_images.listing_id = listings.listing_id
                 LIMIT 1) AS image_url
            FROM listings
            INNER JOIN categories ON listings.category_id = categories.category_id
            WHERE $whereSql
            ORDER BY $orderBy$limitSql";

    $rs = Database::search($sql);

    $products = [];
    if ($rs) {
        while ($row = $rs->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Total number of listings matching $filters, ignoring page/sort - used to
// work out how many pages of results there are.
function countProducts($filters = []) {
    $whereSql = buildProductWhereClause($filters);

    $sql = "SELECT COUNT(*) AS total
            FROM listings
            INNER JOIN categories ON listings.category_id = categories.category_id
            WHERE $whereSql";

    $rs = Database::search($sql);
    if ($rs && $row = $rs->fetch_assoc()) {
        return (int) $row["total"];
    }
    return 0;
}

function renderProductCard($product) {
    $hasImage = !empty($product["image_url"]);
    $listingId = (int) $product["listing_id"];
    ob_start();
    ?>
    <article class="shop-product-card"
             data-category="<?php echo (int) $product["category_id"]; ?>"
             data-price="<?php echo (float) $product["price"]; ?>"
             data-name="<?php echo htmlspecialchars($product["title"]); ?>">
        <a class="shop-product-image" href="product.php?product=<?php echo $listingId; ?>">
            <?php if ($hasImage): ?>
                <img src="<?php echo htmlspecialchars($product["image_url"]); ?>" alt="<?php echo htmlspecialchars($product["title"]); ?>">
            <?php else: ?>
                <?php echo htmlspecialchars($product["title"]); ?>
            <?php endif; ?>
        </a>
        <div class="shop-product-details">
            <span class="product-category"><?php echo htmlspecialchars($product["category_name"]); ?></span>
            <h2><a href="product.php?product=<?php echo $listingId; ?>"><?php echo htmlspecialchars($product["title"]); ?></a></h2>
            <p><?php echo htmlspecialchars($product["description"]); ?></p>
            <div class="shop-product-footer">
                <strong>Rs. <?php echo number_format((float) $product["price"], 2); ?></strong>
            </div>
            <div class="shop-product-actions">
                <button type="button" class="shop-wishlist-btn" onclick="addToWishlist(<?php echo $listingId; ?>)">Add to wishlist</button>
                <button type="button" class="shop-cart-btn" onclick="addToCart(<?php echo $listingId; ?>)">Add to cart</button>
            </div>
        </div>
    </article>
    <?php
    return ob_get_clean();
}

function renderProductCards($products) {
    if (empty($products)) {
        return '<p class="empty-results">No products match those filters.</p>';
    }
    $html = "";
    foreach ($products as $product) {
        $html .= renderProductCard($product);
    }
    return $html;
}
?>
