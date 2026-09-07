<?php
session_start();
include "connection.php";
include "includes/session-check.php";

header("Content-Type: text/plain");

requireLoginOrRespond();

$user_id = currentUserId();
$wishlist_item_id = (int) ($_POST["wishlist_item_id"] ?? 0);

$check = Database::search("SELECT wishlist_items.wishlist_item_id
    FROM wishlist_items
    INNER JOIN wishlists ON wishlist_items.wishlist_id = wishlists.wishlist_id
    WHERE wishlist_items.wishlist_item_id = $wishlist_item_id AND wishlists.user_id = $user_id");

if (!$check || $check->num_rows === 0) {
    echo "Item not found.";
    exit;
}

Database::iud("DELETE FROM wishlist_items WHERE wishlist_item_id = $wishlist_item_id");
echo "success";
