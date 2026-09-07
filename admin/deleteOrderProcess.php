<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$orderId = (int) ($_POST["order_id"] ?? 0);
if ($orderId <= 0) {
    echo "Invalid order.";
    exit;
}

$checkRs = Database::search("SELECT order_id FROM orders WHERE order_id = $orderId");
if (!$checkRs || $checkRs->num_rows === 0) {
    echo "Order not found.";
    exit;
}

// Child rows first - order_items/payments have FK constraints back to orders.
Database::iud("DELETE FROM order_items WHERE order_id = $orderId");
Database::iud("DELETE FROM payments WHERE order_id = $orderId");
Database::iud("DELETE FROM orders WHERE order_id = $orderId");

echo "success";
