<?php
session_start();
include "connection.php";
include "includes/session-check.php";

header("Content-Type: text/plain");

requireLoginOrRespond();

$user_id = currentUserId();
$listing_id = (int) ($_POST["listing_id"] ?? 0);
$quantity = (int) ($_POST["quantity"] ?? 1);

if ($listing_id <= 0) {
    echo "Invalid product.";
    exit;
}
if ($quantity <= 0) {
    $quantity = 1;
}

$listing_rs = Database::search("SELECT price, quantity FROM listings WHERE listing_id = $listing_id AND status = 'active'");
if (!$listing_rs || $listing_rs->num_rows === 0) {
    echo "That product is no longer available.";
    exit;
}
$listing = $listing_rs->fetch_assoc();
$unit_price = (float) $listing["price"];

// Every user has at most one cart; create it on first use.
$cart_rs = Database::search("SELECT cart_id FROM carts WHERE user_id = $user_id");
if ($cart_rs && $cart_rs->num_rows > 0) {
    $cart_id = (int) $cart_rs->fetch_assoc()["cart_id"];
} else {
    Database::iud("INSERT INTO carts (user_id, created_at) VALUES ($user_id, NOW())");
    $cart_id = Database::insertId();
}

$existing_rs = Database::search("SELECT cart_item_id, quantity FROM cart_items WHERE cart_id = $cart_id AND listing_id = $listing_id");
if ($existing_rs && $existing_rs->num_rows > 0) {
    $existing = $existing_rs->fetch_assoc();
    $newQuantity = (int) $existing["quantity"] + $quantity;
    Database::iud("UPDATE cart_items SET quantity = $newQuantity WHERE cart_item_id = " . (int) $existing["cart_item_id"]);
} else {
    Database::iud("INSERT INTO cart_items (cart_id, listing_id, quantity, unit_price, added_at)
                    VALUES ($cart_id, $listing_id, $quantity, $unit_price, NOW())");
}

echo "success";
