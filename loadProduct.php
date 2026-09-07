<?php
// AJAX endpoint: receives filter values via POST and returns the matching
// product cards as an HTML fragment, which main.js drops into #productGrid.

session_start();
include "connection.php";
include "includes/productHelper.php";
include "pagination.php";

header("Content-Type: text/html; charset=utf-8");

$filters = [
    "search"    => $_POST["search"] ?? "",
    "category"  => $_POST["category"] ?? "all",
    "min_price" => $_POST["min_price"] ?? "",
    "max_price" => $_POST["max_price"] ?? "",
    "sort"      => $_POST["sort"] ?? "",
    "page"      => (int) ($_POST["page"] ?? 1),
    "per_page"  => PRODUCTS_PER_PAGE,
];

$totalProducts = countProducts($filters);
$totalPages = totalPagesFor($totalProducts);
$filters["page"] = max(1, min($filters["page"], $totalPages));

$products = fetchProducts($filters);

echo renderProductCards($products);
echo "<!--PAGINATION-->";
echo renderPagination($filters["page"], $totalPages, $totalProducts);
