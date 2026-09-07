<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$title = trim($_POST["title"] ?? "");
$content = trim($_POST["content"] ?? "");
$imageUrl = trim($_POST["image_url"] ?? "");
$authorId = (int) $_SESSION["u"]["user_id"];

if ($title === "") {
    echo "Please enter a title.";
    exit;
}
if ($content === "") {
    echo "Please enter the article content.";
    exit;
}

$titleEscaped = Database::escape($title);
$contentEscaped = Database::escape($content);
$imageUrlEscaped = Database::escape($imageUrl);

Database::iud("INSERT INTO news (author_id, title, content, image_url, is_active, created_at)
                VALUES ($authorId, '$titleEscaped', '$contentEscaped', '$imageUrlEscaped', 1, NOW())");

echo "success";
