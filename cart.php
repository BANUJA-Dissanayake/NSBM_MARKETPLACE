<?php
session_start();
include "connection.php";

$cart_items = [];
$subtotal = 0;

if (isset($_SESSION["u"]["user_id"])) {
    $user_id = (int) $_SESSION["u"]["user_id"];

    $rs = Database::search("SELECT cart_items.cart_item_id, cart_items.quantity, cart_items.unit_price,
            listings.listing_id, listings.title, listings.description, categories.category_name,
            (SELECT image_url FROM listing_images WHERE listing_images.listing_id = listings.listing_id LIMIT 1) AS image_url
        FROM cart_items
        INNER JOIN carts ON cart_items.cart_id = carts.cart_id
        INNER JOIN listings ON cart_items.listing_id = listings.listing_id
        INNER JOIN categories ON listings.category_id = categories.category_id
        WHERE carts.user_id = $user_id
        ORDER BY cart_items.added_at DESC");

    if ($rs) {
        while ($row = $rs->fetch_assoc()) {
            $cart_items[] = $row;
            $subtotal += (float) $row["quantity"] * (float) $row["unit_price"];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="cart-page">
        <header class="cart-heading">
            <div>
                <p class="cart-eyebrow">Ready when you are</p>
                <h1>Your cart</h1>
                <p>Review your items before checking out.</p>
            </div>
            <a href="shop.php">Continue shopping <span aria-hidden="true">-></span></a>
        </header>

        <?php if (!isset($_SESSION["u"]["user_id"])): ?>
            <p>Please <a href="register.php">log in</a> to view your cart.</p>
        <?php else: ?>
        <div class="cart-layout">
            <section class="cart-items" aria-label="Cart items">
                <?php foreach ($cart_items as $item): ?>
                    <article class="cart-item" data-cart-item-id="<?php echo (int) $item['cart_item_id']; ?>" data-price="<?php echo (float) $item['unit_price']; ?>">
                        <div class="cart-item-image">
                            <?php if (!empty($item['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                            <?php else: ?>
                                <?php echo htmlspecialchars($item['title']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="cart-item-info">
                            <span><?php echo htmlspecialchars($item['category_name']); ?></span>
                            <h2><?php echo htmlspecialchars($item['title']); ?></h2>
                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                            <button class="remove-cart-item" type="button">Remove</button>
                        </div>
                        <div class="cart-item-actions">
                            <div class="quantity-control" aria-label="Quantity controls">
                                <button class="quantity-minus" type="button" aria-label="Decrease quantity">-</button>
                                <span class="quantity"><?php echo (int) $item['quantity']; ?></span>
                                <button class="quantity-plus" type="button" aria-label="Increase quantity">+</button>
                            </div>
                            <strong class="item-total">Rs. <?php echo number_format((float) $item['quantity'] * (float) $item['unit_price'], 2); ?></strong>
                        </div>
                    </article>
                <?php endforeach; ?>

                <p class="cart-empty" <?php echo !empty($cart_items) ? "hidden" : ""; ?>>Your cart is empty. <a href="shop.php">Browse the shop</a></p>
            </section>

            <aside class="cart-summary">
                <h2>Order summary</h2>
                <div class="summary-row"><span>Subtotal</span><strong id="cartSubtotal">Rs. <?php echo number_format($subtotal, 2); ?></strong></div>
                <div class="summary-row"><span>Delivery</span><strong>Free</strong></div>
                <div class="summary-total"><span>Total</span><strong id="cartTotal">Rs. <?php echo number_format($subtotal, 2); ?></strong></div>
                <?php if (!empty($cart_items)): ?>
                    <a class="checkout-button" href="checkout.php">Proceed to checkout <span aria-hidden="true">-></span></a>
                <?php else: ?>
                    <button class="checkout-button" type="button" disabled>Proceed to checkout <span aria-hidden="true">-></span></button>
                <?php endif; ?>
                <p class="secure-note">Secure checkout for the NSBM community.</p>
            </aside>
        </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
    <script src="js/main.js"></script>
</body>
</html>
