<?php
session_start();
include "connection.php";
include "includes/session-check.php";

header("Content-Type: text/plain");

requireLoginOrRespond();

$user_id = currentUserId();
$cart_item_id = (int) ($_POST["cart_item_id"] ?? 0);

$check = Database::search("SELECT cart_items.cart_item_id
    FROM cart_items
    INNER JOIN carts ON cart_items.cart_id = carts.cart_id
    WHERE cart_items.cart_item_id = $cart_item_id AND carts.user_id = $user_id");

if (!$check || $check->num_rows === 0) {
    echo "Item not found.";
    exit;
}

Database::iud("DELETE FROM cart_items WHERE cart_item_id = $cart_item_id");
echo "success";
