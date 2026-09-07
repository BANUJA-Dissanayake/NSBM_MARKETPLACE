<?php
session_start();
include "connection.php";
include "includes/session-check.php";

header("Content-Type: text/plain");

requireLoginOrRespond();

$user_id = currentUserId();
$cart_item_id = (int) ($_POST["cart_item_id"] ?? 0);
$action = $_POST["action"] ?? "";

// Confirm this cart item actually belongs to the logged-in user before
// touching it.
$check = Database::search("SELECT cart_items.cart_item_id, cart_items.quantity
    FROM cart_items
    INNER JOIN carts ON cart_items.cart_id = carts.cart_id
    WHERE cart_items.cart_item_id = $cart_item_id AND carts.user_id = $user_id");

if (!$check || $check->num_rows === 0) {
    echo "Item not found.";
    exit;
}

$item = $check->fetch_assoc();
$quantity = (int) $item["quantity"];

if ($action === "increase") {
    $quantity++;
} else if ($action === "decrease") {
    $quantity--;
} else {
    echo "Invalid action.";
    exit;
}

if ($quantity < 1) {
    Database::iud("DELETE FROM cart_items WHERE cart_item_id = $cart_item_id");
    echo "removed";
} else {
    Database::iud("UPDATE cart_items SET quantity = $quantity WHERE cart_item_id = $cart_item_id");
    echo $quantity;
}
