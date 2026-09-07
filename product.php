<?php
session_start();
include "connection.php";

$listing_id = isset($_GET["product"]) ? (int) $_GET["product"] : 0;

$product = null;
$images = [];

if ($listing_id > 0) {
    $rs = Database::search("SELECT listings.*, categories.category_name
        FROM listings
        INNER JOIN categories ON listings.category_id = categories.category_id
        WHERE listings.listing_id = $listing_id");

    if ($rs && $rs->num_rows > 0) {
        $product = $rs->fetch_assoc();

        $img_rs = Database::search("SELECT image_url FROM listing_images WHERE listing_id = $listing_id");
        if ($img_rs) {
            while ($row = $img_rs->fetch_assoc()) {
                $images[] = $row["image_url"];
            }
        }
    }
}

$related_products = [];
if ($product) {
    $category_id = (int) $product["category_id"];
    $related_rs = Database::search("SELECT listings.*,
            (SELECT image_url FROM listing_images WHERE listing_images.listing_id = listings.listing_id LIMIT 1) AS image_url
        FROM listings
        WHERE listings.category_id = $category_id
          AND listings.listing_id != $listing_id
          AND listings.status = 'active'
        ORDER BY listings.created_at DESC
        LIMIT 8");
    if ($related_rs) {
        while ($row = $related_rs->fetch_assoc()) {
            $related_products[] = $row;
        }
    }
}

$pageTitle = $product ? htmlspecialchars($product["title"]) . " | NSBM Marketplace" : "Product not found | NSBM Marketplace";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/header-cyber.css">
    <link rel="stylesheet" href="css/product.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="product-page">
    <?php if (!$product): ?>
        <p class="product-breadcrumb"><a href="shop.php">Shop</a></p>
        <p>Sorry, we couldn't find that product. <a href="shop.php">Back to shop</a></p>
    <?php else: ?>
        <p class="product-breadcrumb"><a href="shop.php">Shop</a> <span aria-hidden="true">/</span> <?php echo htmlspecialchars($product["category_name"]); ?></p>
        <section class="product-detail-layout">
            <div class="product-detail-image product-detail-green">
                <?php if (!empty($images[0])): ?>
                    <img src="<?php echo htmlspecialchars($images[0]); ?>" alt="<?php echo htmlspecialchars($product["title"]); ?>">
                <?php else: ?>
                    <span><?php echo htmlspecialchars($product["title"]); ?></span>
                <?php endif; ?>
            </div>
            <div class="product-detail-content">
                <span class="product-detail-category"><?php echo htmlspecialchars($product["category_name"]); ?></span>
                <h1><?php echo htmlspecialchars($product["title"]); ?></h1>
                <p class="product-detail-description"><?php echo htmlspecialchars($product["description"]); ?></p>
                <div class="product-detail-price">Rs. <?php echo number_format((float) $product["price"], 2); ?></div>
                <div class="product-availability">
                    <span></span>
                    <?php echo ((int) $product["quantity"] > 0) ? "In stock and ready for campus delivery" : "Currently out of stock"; ?>
                </div>

                <div class="product-purchase">
                    <label for="quantity">Quantity</label>
                    <input id="quantity" type="number" min="1" max="<?php echo (int) $product['quantity']; ?>" value="1">
                    <div class="product-purchase-actions">
                        <button type="button" class="cart-button" onclick="addToCart(<?php echo (int) $listing_id; ?>, 'quantity')">Add to cart <span aria-hidden="true">-></span></button>
                        <button type="button" class="wishlist-button" onclick="addToWishlist(<?php echo (int) $listing_id; ?>)">Add to wishlist</button>
                    </div>
                </div>
                <p id="productMessage"></p>

                <div class="product-features">
                    <div><strong>Campus friendly</strong><span>Designed for everyday student life.</span></div>
                    <div><strong>Simple returns</strong><span>Contact our support team for help.</span></div>
                </div>
            </div>
        </section>

        <?php if (!empty($related_products)): ?>
            <section class="related-products" aria-label="Related products">
                <div class="related-products-title">
                    <h2>Related Products</h2>
                    <div class="related-products-nav">
                        <button type="button" class="related-nav-btn" id="relatedPrevBtn" onclick="scrollRelatedProducts(-1)" aria-label="Scroll left">
                            <span aria-hidden="true">&lt;</span>
                        </button>
                        <button type="button" class="related-nav-btn" id="relatedNextBtn" onclick="scrollRelatedProducts(1)" aria-label="Scroll right">
                            <span aria-hidden="true">&gt;</span>
                        </button>
                    </div>
                </div>
                <div class="related-products-scroll" id="relatedProductsScroll">
                    <?php foreach ($related_products as $related): ?>
                        <a class="related-product-card" href="product.php?product=<?php echo (int) $related['listing_id']; ?>">
                            <div class="related-product-image">
                                <?php if (!empty($related['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($related['image_url']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                                <?php else: ?>
                                    <span><?php echo htmlspecialchars($related['title']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="related-product-title"><?php echo htmlspecialchars($related['title']); ?></div>
                            <div class="related-product-price">Rs. <?php echo number_format((float) $related['price'], 2); ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
    </main>
    <?php include 'footer.php'; ?>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
