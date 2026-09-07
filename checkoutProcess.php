<?php
session_start();
include "connection.php";
include "includes/session-check.php";

header("Content-Type: text/plain");

requireLoginOrRespond();

$user_id = currentUserId();

$shipping_name = trim($_POST["shipping_name"] ?? "");
$shipping_phone = trim($_POST["shipping_phone"] ?? "");
$shipping_address = trim($_POST["shipping_address"] ?? "");
$shipping_city = trim($_POST["shipping_city"] ?? "");
$payment_method = trim($_POST["payment_method"] ?? "Cash on Delivery");

if ($shipping_name === "" || $shipping_phone === "" || $shipping_address === "" || $shipping_city === "") {
    echo "Please fill in all shipping details.";
    exit;
}

$cart_rs = Database::search("SELECT cart_id FROM carts WHERE user_id = $user_id");
if (!$cart_rs || $cart_rs->num_rows === 0) {
    echo "Your cart is empty.";
    exit;
}
$cart_id = (int) $cart_rs->fetch_assoc()["cart_id"];

$items_rs = Database::search("SELECT * FROM cart_items WHERE cart_id = $cart_id");
if (!$items_rs || $items_rs->num_rows === 0) {
    echo "Your cart is empty.";
    exit;
}

$items = [];
$total = 0;
while ($row = $items_rs->fetch_assoc()) {
    $items[] = $row;
    $total += (float) $row["quantity"] * (float) $row["unit_price"];
}

$nameEscaped = Database::escape($shipping_name);
$phoneEscaped = Database::escape($shipping_phone);
$addressEscaped = Database::escape($shipping_address);
$cityEscaped = Database::escape($shipping_city);
$paymentMethodEscaped = Database::escape($payment_method);

Database::iud("INSERT INTO orders (buyer_id, order_date, total_amount, status, shipping_name, shipping_phone, shipping_address, shipping_city)
                VALUES ($user_id, NOW(), $total, 'pending', '$nameEscaped', '$phoneEscaped', '$addressEscaped', '$cityEscaped')");
$order_id = Database::insertId();

foreach ($items as $item) {
    $listing_id = (int) $item["listing_id"];
    $quantity = (int) $item["quantity"];
    $unit_price = (float) $item["unit_price"];
    Database::iud("INSERT INTO order_items (order_id, listing_id, quantity, unit_price)
                    VALUES ($order_id, $listing_id, $quantity, $unit_price)");
}

Database::iud("INSERT INTO payments (order_id, payment_method, payment_status, payment_date)
                VALUES ($order_id, '$paymentMethodEscaped', 'pending', NOW())");

Database::iud("DELETE FROM cart_items WHERE cart_id = $cart_id");

echo "order_placed:" . $order_id;
