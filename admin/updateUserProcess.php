<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$userId = (int) ($_POST["user_id"] ?? 0);
$name = trim($_POST["name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$role = (int) ($_POST["role"] ?? 0);
$statusId = (int) ($_POST["status_id"] ?? 0);

if ($userId <= 0) {
    echo "Invalid user.";
    exit;
}
if ($name === "") {
    echo "Please enter a full name.";
    exit;
}
if ($role <= 0) {
    echo "Please choose a role.";
    exit;
}
if (!in_array($statusId, [1, 2], true)) {
    echo "Invalid status.";
    exit;
}
if ($userId === (int) $_SESSION["u"]["user_id"] && $statusId === 2) {
    echo "You can't block your own account.";
    exit;
}

$nameEscaped = Database::escape($name);
$phoneEscaped = Database::escape($phone);

Database::iud("UPDATE `user` SET
                name = '$nameEscaped',
                phone = '$phoneEscaped',
                role = $role,
                status_id = $statusId
                WHERE user_id = $userId");

echo "success";
