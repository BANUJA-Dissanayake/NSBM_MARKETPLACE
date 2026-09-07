<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$messageId = (int) ($_POST["message_id"] ?? 0);
if ($messageId <= 0) {
    echo "Invalid message.";
    exit;
}

Database::iud("UPDATE messages SET is_read = 1 WHERE message_id = $messageId");

echo "success";
