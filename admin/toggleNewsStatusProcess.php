<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$newsId = (int) ($_POST["news_id"] ?? 0);
$isActive = (int) ($_POST["is_active"] ?? -1);

if ($newsId <= 0 || !in_array($isActive, [0, 1], true)) {
    echo "Invalid request.";
    exit;
}

Database::iud("UPDATE news SET is_active = $isActive WHERE news_id = $newsId");

echo "success";
