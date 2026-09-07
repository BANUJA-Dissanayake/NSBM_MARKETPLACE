<?php
session_start();
include "connection.php";

$featured_rs = Database::search("SELECT listings.*, categories.category_name,
        (SELECT image_url FROM listing_images WHERE listing_images.listing_id = listings.listing_id LIMIT 1) AS image_url
    FROM listings
    INNER JOIN categories ON listings.category_id = categories.category_id
    WHERE listings.status = 'active'
    ORDER BY listings.created_at DESC
    LIMIT 3");

$featured_products = [];
if ($featured_rs) {
    while ($row = $featured_rs->fetch_assoc()) {
        $featured_products[] = $row;
    }
}

// All advertisements currently within their run dates, set up via
// admin/advertisements.php - shown as a scrolling banner carousel.
$ad_rs = Database::search("SELECT * FROM advertisements
    WHERE is_active = 1
      AND (start_date IS NULL OR start_date <= CURDATE())
      AND (end_date IS NULL OR end_date >= CURDATE())
    ORDER BY created_at DESC");
$active_ads = [];
if ($ad_rs) {
    while ($row = $ad_rs->fetch_assoc()) {
        $active_ads[] = $row;
    }
}

// categories has duplicate rows sharing the same name (pre-existing data
// issue) - group by name so each real category only shows once.
$category_rs = Database::search("SELECT categories.category_id, categories.category_name, categories.image_url
    FROM categories
    INNER JOIN (
        SELECT MIN(category_id) AS category_id
        FROM categories
        GROUP BY category_name
    ) AS grouped ON grouped.category_id = categories.category_id
    ORDER BY categories.category_name ASC");

$category_icons = [
    "Electronics" => "images/listings/photo-laptop.jpg",
    "Books" => "images/listings/photo-book.jpg",
    "Clothing" => "images/listings/photo-tshirt.jpg",
    "Sports & Fitness" => "images/listings/photo-shoes.jpg",
    "Sports & Outdoors" => "images/listings/photo-shoes.jpg",
    "Furniture" => "images/listings/photo-furniture.jpg",
    "Stationery & Office Supplies" => "images/listings/photo-notes.jpg",
    "Kitchen & Dining" => "images/listings/photo-kitchen.jpg",
    "Accessories" => "images/listings/photo-accessory.jpg",
];

$categories = [];
if ($category_rs) {
    while ($row = $category_rs->fetch_assoc()) {
        $row["icon"] = !empty($row["image_url"])
            ? $row["image_url"]
            : ($category_icons[$row["category_name"]] ?? "images/listings/photo-generic-electronics.jpg");
        $categories[] = $row;
    }
}

$latest_news = [];
$news_rs = Database::search("SELECT news_id, title, content, image_url, created_at FROM news
    WHERE is_active = 1
    ORDER BY created_at DESC
    LIMIT 6");
if ($news_rs) {
    while ($row = $news_rs->fetch_assoc()) {
        $latest_news[] = $row;
    }
}

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
    <link rel="stylesheet" href="css/marketplace.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="featured-product">
        <?php if (!empty($active_ads)): ?>
            <section class="promo-carousel" aria-label="Marketplace promotions">
                <div class="promo-track" id="promoTrack">
                    <?php foreach ($active_ads as $ad): ?>
                        <a class="promo-slide" href="<?php echo htmlspecialchars($ad['link_url'] ?: '#'); ?>">
                            <img src="<?php echo htmlspecialchars($ad['image_url']); ?>" alt="<?php echo htmlspecialchars($ad['title']); ?>">
                        </a>
                    <?php endforeach; ?>
                </div>
                <?php if (count($active_ads) > 1): ?>
                    <div class="promo-dots" id="promoDots"></div>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <?php if (!empty($categories)): ?>
            <section class="category-section" aria-label="Shop by category">
                <div class="category-section-title">
                    <h2>Category</h2>
                </div>
                <div class="category-icon-grid">
                    <?php foreach ($categories as $category): ?>
                        <a class="category-icon-card" href="shop.php">
                            <span class="category-icon-frame">
                                <img src="<?php echo htmlspecialchars($category["icon"]); ?>" alt="<?php echo htmlspecialchars($category["category_name"]); ?>">
                            </span>
                            <span class="category-icon-label"><?php echo htmlspecialchars($category["category_name"]); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <div class="featured-heading">
            <div>
                <h1>Featured Product</h1>
            </div>
        </div>
        <div class="product-grid" aria-label="Featured products">
            <?php if (empty($featured_products)): ?>
                <p>No products available yet. Check back soon.</p>
            <?php else: ?>
                <?php foreach ($featured_products as $product): $listingId = (int) $product['listing_id']; ?>
                    <article class="product-card">
                        <a class="product-image" href="product.php?product=<?php echo $listingId; ?>">
                            <?php if (!empty($product['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" style="width:100%;height:100%;object-fit:cover;">
                            <?php else: ?>
                                <?php echo htmlspecialchars($product['title']); ?>
                            <?php endif; ?>
                        </a>
                        <div class="product-details">
                            <h2 class="product-title"><a href="product.php?product=<?php echo $listingId; ?>"><?php echo htmlspecialchars($product['title']); ?></a></h2>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="product-footer">
                                <span class="product-price">Rs. <?php echo number_format((float) $product['price'], 2); ?></span>
                            </div>
                            <div class="product-actions">
                                <button type="button" class="shop-wishlist-btn" onclick="addToWishlist(<?php echo $listingId; ?>)">Add to wishlist</button>
                                <button type="button" class="shop-cart-btn" onclick="addToCart(<?php echo $listingId; ?>)">Add to cart</button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($latest_news)): ?>
            <section class="latest-news-section" aria-label="Latest news">
                <div class="latest-news-title">
                    <h2>Latest News</h2>
                    <div class="latest-news-nav">
                        <button type="button" class="related-nav-btn" onclick="scrollLatestNews(-1)" aria-label="Scroll left">
                            <span aria-hidden="true">&lt;</span>
                        </button>
                        <button type="button" class="related-nav-btn" onclick="scrollLatestNews(1)" aria-label="Scroll right">
                            <span aria-hidden="true">&gt;</span>
                        </button>
                    </div>
                </div>
                <div class="latest-news-scroll" id="latestNewsScroll">
                    <?php foreach ($latest_news as $item): ?>
                        <a class="latest-news-card" href="news-details.php?id=<?php echo (int) $item['news_id']; ?>">
                            <div class="latest-news-image">
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                                <?php else: ?>
                                    <span><?php echo htmlspecialchars($item['title']); ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="latest-news-date"><?php echo htmlspecialchars($item['created_at']); ?></span>
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p><?php
                                $excerpt = trim($item['content']);
                                echo htmlspecialchars(strlen($excerpt) > 120 ? substr($excerpt, 0, 120) . "..." : $excerpt);
                            ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
