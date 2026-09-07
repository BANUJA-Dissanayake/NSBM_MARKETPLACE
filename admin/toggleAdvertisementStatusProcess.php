<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$adId = (int) ($_POST["advertisement_id"] ?? 0);
$isActive = (int) ($_POST["is_active"] ?? -1);

if ($adId <= 0 || !in_array($isActive, [0, 1], true)) {
    echo "Invalid request.";
    exit;
}

Database::iud("UPDATE advertisements SET is_active = $isActive WHERE advertisement_id = $adId");

echo "success";
