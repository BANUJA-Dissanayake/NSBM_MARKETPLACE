<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$title = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$imageUrl = trim($_POST["image_url"] ?? "");
$linkUrl = trim($_POST["link_url"] ?? "");
$startDate = trim($_POST["start_date"] ?? "");
$endDate = trim($_POST["end_date"] ?? "");
$advertiserId = (int) $_SESSION["u"]["user_id"];

if ($title === "") {
    echo "Please enter a title.";
    exit;
}

$titleEscaped = Database::escape($title);
$descriptionEscaped = Database::escape($description);
$imageUrlEscaped = Database::escape($imageUrl);
$linkUrlEscaped = Database::escape($linkUrl);
$startDateSql = $startDate !== "" ? "'" . Database::escape($startDate) . "'" : "NULL";
$endDateSql = $endDate !== "" ? "'" . Database::escape($endDate) . "'" : "NULL";

Database::iud("INSERT INTO advertisements (advertiser_id, title, description, image_url, link_url, start_date, end_date, is_active, created_at)
                VALUES ($advertiserId, '$titleEscaped', '$descriptionEscaped', '$imageUrlEscaped', '$linkUrlEscaped', $startDateSql, $endDateSql, 1, NOW())");

echo "success";
