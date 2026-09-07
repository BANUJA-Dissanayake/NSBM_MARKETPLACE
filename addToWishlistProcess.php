<?php
session_start();
include "connection.php";
include "includes/session-check.php";

header("Content-Type: text/plain");

requireLoginOrRespond();

$user_id = currentUserId();
$listing_id = (int) ($_POST["listing_id"] ?? 0);

if ($listing_id <= 0) {
    echo "Invalid product.";
    exit;
}

// Every user has at most one wishlist; create it on first use.
$wishlist_rs = Database::search("SELECT wishlist_id FROM wishlists WHERE user_id = $user_id");
if ($wishlist_rs && $wishlist_rs->num_rows > 0) {
    $wishlist_id = (int) $wishlist_rs->fetch_assoc()["wishlist_id"];
} else {
    Database::iud("INSERT INTO wishlists (user_id, created_at) VALUES ($user_id, NOW())");
    $wishlist_id = Database::insertId();
}

$existing_rs = Database::search("SELECT wishlist_item_id FROM wishlist_items WHERE wishlist_id = $wishlist_id AND listing_id = $listing_id");
if ($existing_rs && $existing_rs->num_rows > 0) {
    echo "already_in_wishlist";
    exit;
}

Database::iud("INSERT INTO wishlist_items (wishlist_id, listing_id, added_at) VALUES ($wishlist_id, $listing_id, NOW())");
echo "success";
