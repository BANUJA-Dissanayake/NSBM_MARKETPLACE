<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$orderId = (int) ($_POST["order_id"] ?? 0);
$newStatus = trim($_POST["status"] ?? "");

if ($orderId <= 0) {
    echo "Invalid order.";
    exit;
}
if (!in_array($newStatus, ["pending", "completed"], true)) {
    echo "Invalid status.";
    exit;
}

$statusEscaped = Database::escape($newStatus);
Database::iud("UPDATE orders SET status = '$statusEscaped' WHERE order_id = $orderId");

echo "success";
