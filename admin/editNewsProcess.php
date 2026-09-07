<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$newsId = (int) ($_POST["news_id"] ?? 0);
$title = trim($_POST["title"] ?? "");
$content = trim($_POST["content"] ?? "");
$imageUrl = trim($_POST["image_url"] ?? "");
$isActive = (int) ($_POST["is_active"] ?? 1);

if ($newsId <= 0) {
    echo "Invalid article.";
    exit;
}
if ($title === "") {
    echo "Please enter a title.";
    exit;
}
if ($content === "") {
    echo "Please enter the article content.";
    exit;
}
if (!in_array($isActive, [0, 1], true)) {
    echo "Invalid status.";
    exit;
}

$titleEscaped = Database::escape($title);
$contentEscaped = Database::escape($content);
$imageUrlEscaped = Database::escape($imageUrl);

Database::iud("UPDATE news SET
                title = '$titleEscaped',
                content = '$contentEscaped',
                image_url = '$imageUrlEscaped',
                is_active = $isActive
                WHERE news_id = $newsId");

echo "success";
