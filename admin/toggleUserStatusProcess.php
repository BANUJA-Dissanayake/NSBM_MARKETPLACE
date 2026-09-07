<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$targetUserId = (int) ($_POST["user_id"] ?? 0);
$newStatusId = (int) ($_POST["status_id"] ?? 0);
$currentUserId = (int) $_SESSION["u"]["user_id"];

if ($targetUserId <= 0 || !in_array($newStatusId, [1, 2], true)) {
    echo "Invalid request.";
    exit;
}
if ($targetUserId === $currentUserId) {
    echo "You can't block your own account.";
    exit;
}

Database::iud("UPDATE `user` SET status_id = $newStatusId WHERE user_id = $targetUserId");

echo "success";
