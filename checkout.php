<?php
session_start();
include "connection.php";
include "includes/session-check.php";

requireLoginOrRedirect();

$user_id = currentUserId();

$user_rs = Database::search("SELECT name, phone FROM `user` WHERE user_id = $user_id");
$user_details = $user_rs && $user_rs->num_rows > 0 ? $user_rs->fetch_assoc() : ["name" => "", "phone" => ""];

$cart_items = [];
$subtotal = 0;

$cart_rs = Database::search("SELECT cart_items.cart_item_id, cart_items.quantity, cart_items.unit_price,
        listings.listing_id, listings.title,
        (SELECT image_url FROM listing_images WHERE listing_images.listing_id = listings.listing_id LIMIT 1) AS image_url
    FROM cart_items
    INNER JOIN carts ON cart_items.cart_id = carts.cart_id
    INNER JOIN listings ON cart_items.listing_id = listings.listing_id
    WHERE carts.user_id = $user_id
    ORDER BY cart_items.added_at DESC");

if ($cart_rs) {
    while ($row = $cart_rs->fetch_assoc()) {
        $cart_items[] = $row;
        $subtotal += (float) $row["quantity"] * (float) $row["unit_price"];
    }
}

if (empty($cart_items)) {
    header("Location: cart.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | NSBM Marketplace</title>
    <link rel="stylesheet" href="css/checkout.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="checkout-page">
        <header class="checkout-heading">
            <div>
                <p class="checkout-eyebrow">Almost there</p>
                <h1>Checkout</h1>
                <p>Confirm your delivery details and payment method.</p>
            </div>
            <a href="cart.php">Back to cart <span aria-hidden="true">-></span></a>
        </header>

        <div class="checkout-layout">
            <section class="checkout-form" aria-label="Shipping and payment details">
                <h2>Delivery details</h2>
                <div class="form-field">
                    <label for="shippingName">Full name</label>
                    <input id="shippingName" type="text" value="<?php echo htmlspecialchars($user_details["name"] ?? ""); ?>" placeholder="Enter your name" required>
                </div>
                <div class="form-field">
                    <label for="shippingPhone">Phone number</label>
                    <input id="shippingPhone" type="tel" value="<?php echo htmlspecialchars($user_details["phone"] ?? ""); ?>" placeholder="07XXXXXXXX" required>
                </div>
                <div class="form-field">
                    <label for="shippingAddress">Delivery address</label>
                    <textarea id="shippingAddress" rows="3" placeholder="Hostel / house number, street, campus block" required></textarea>
                </div>
                <div class="form-field">
                    <label for="shippingCity">City</label>
                    <input id="shippingCity" type="text" placeholder="e.g. Homagama" required>
                </div>                
                <h2>Payment method</h2>
                <div class="payment-options" id="paymentOptions">
                    <label class="payment-option">
                        <input type="radio" name="paymentMethod" value="Cash on Delivery" >
                        <span>Cash on Delivery</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="paymentMethod" value="Online Payment" checked>
                        <span>Online Payment</span>
                    </label>
                </div>

                <p class="checkout-error" id="checkoutError" hidden></p>
            </section>

            <aside class="checkout-summary">
                <h2>Order summary</h2>
                <div class="checkout-summary-items">
                    <?php foreach ($cart_items as $item): ?>
                        <div class="checkout-summary-item">
                            <span class="summary-item-name"><?php echo htmlspecialchars($item["title"]); ?> <span class="summary-item-qty">x<?php echo (int) $item["quantity"]; ?></span></span>
                            <span class="summary-item-price">Rs. <?php echo number_format((float) $item["quantity"] * (float) $item["unit_price"], 2); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="summary-row"><span>Subtotal</span><strong>Rs. <?php echo number_format($subtotal, 2); ?></strong></div>
                <div class="summary-row"><span>Delivery</span><strong>Free</strong></div>
                <div class="summary-total"><span>Total</span><strong>Rs. <?php echo number_format($subtotal, 2); ?></strong></div>
                <button class="place-order-button" type="button" id="placeOrderBtn" onclick="placeOrder()">Place order <span aria-hidden="true">-></span></button>
                <p class="secure-note">Secure checkout for the NSBM community.</p>
            </aside>
        </div>
    </main>

    <?php include 'footer.php'; ?>
    <script src="js/main.js"></script>
    <script type="text/javascript" src="https://www.payhere.lk/lib/payhere.js"></script>

</body>
</html>
