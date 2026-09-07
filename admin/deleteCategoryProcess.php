<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$categoryId = (int) ($_POST["category_id"] ?? 0);
if ($categoryId <= 0) {
    echo "Invalid category.";
    exit;
}

$checkRs = Database::search("SELECT category_id FROM categories WHERE category_id = $categoryId");
if (!$checkRs || $checkRs->num_rows === 0) {
    echo "Category not found.";
    exit;
}

// Listings reference categories by id with no cascading delete, so removing
// a category still in use would silently orphan its products (they'd drop
// out of every listing query, which INNER JOINs against categories) instead
// of actually deleting them. Block it and tell the admin to move/remove
// those products first.
$countRs = Database::search("SELECT COUNT(*) AS total FROM listings WHERE category_id = $categoryId");
$productCount = 0;
if ($countRs && $row = $countRs->fetch_assoc()) {
    $productCount = (int) $row["total"];
}
if ($productCount > 0) {
    echo "Move or remove the $productCount product(s) in this category first.";
    exit;
}

Database::iud("DELETE FROM categories WHERE category_id = $categoryId");

echo "success";
