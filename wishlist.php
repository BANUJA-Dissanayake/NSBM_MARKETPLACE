<?php
session_start();
include "connection.php";

$wishlist_items = [];

if (isset($_SESSION["u"]["user_id"])) {
    $user_id = (int) $_SESSION["u"]["user_id"];

    $rs = Database::search("SELECT wishlist_items.wishlist_item_id, listings.listing_id, listings.title,
            listings.description, listings.price, categories.category_name,
            (SELECT image_url FROM listing_images WHERE listing_images.listing_id = listings.listing_id LIMIT 1) AS image_url
        FROM wishlist_items
        INNER JOIN wishlists ON wishlist_items.wishlist_id = wishlists.wishlist_id
        INNER JOIN listings ON wishlist_items.listing_id = listings.listing_id
        INNER JOIN categories ON listings.category_id = categories.category_id
        WHERE wishlists.user_id = $user_id
        ORDER BY wishlist_items.added_at DESC");

    if ($rs) {
        while ($row = $rs->fetch_assoc()) {
            $wishlist_items[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/wishlist.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="wishlist-page">
        <header class="wishlist-heading">
            <div>
                <p class="wishlist-eyebrow">Saved for later</p>
                <h1>Your wishlist</h1>
                <p>Keep track of the products you want to come back to.</p>
            </div>
            <a class="wishlist-shop-link" href="shop.php">Continue shopping <span aria-hidden="true">-></span></a>
        </header>

        <?php if (!isset($_SESSION["u"]["user_id"])): ?>
            <p>Please <a href="register.php">log in</a> to view your wishlist.</p>
        <?php else: ?>
        <section class="wishlist-grid" aria-label="Saved products">
            <?php foreach ($wishlist_items as $item): ?>
                <article class="wishlist-card" data-wishlist-item-id="<?php echo (int) $item['wishlist_item_id']; ?>" data-listing-id="<?php echo (int) $item['listing_id']; ?>">
                    <div class="wishlist-image" role="img" aria-label="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php if (!empty($item['image_url'])): ?>
                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php else: ?>
                            <?php echo htmlspecialchars($item['title']); ?>
                        <?php endif; ?>
                    </div>
                    <div class="wishlist-card-body">
                        <span><?php echo htmlspecialchars($item['category_name']); ?></span>
                        <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <div class="wishlist-card-footer">
                            <strong>Rs. <?php echo number_format((float) $item['price'], 2); ?></strong>
                            <div class="wishlist-actions">
                                <button type="button" class="remove-item">Remove</button>
                                <button type="button" class="add-to-cart-btn">Add to cart</button>
                            </div>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <p class="wishlist-empty" <?php echo !empty($wishlist_items) ? "hidden" : ""; ?>>Your wishlist is empty. <a href="shop.php">Browse the shop</a></p>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
