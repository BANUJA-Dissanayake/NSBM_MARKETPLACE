<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$adId = (int) ($_POST["advertisement_id"] ?? 0);
$title = trim($_POST["title"] ?? "");
$description = trim($_POST["description"] ?? "");
$imageUrl = trim($_POST["image_url"] ?? "");
$linkUrl = trim($_POST["link_url"] ?? "");
$startDate = trim($_POST["start_date"] ?? "");
$endDate = trim($_POST["end_date"] ?? "");
$isActive = (int) ($_POST["is_active"] ?? 1);

if ($adId <= 0) {
    echo "Invalid advertisement.";
    exit;
}
if ($title === "") {
    echo "Please enter a title.";
    exit;
}
if (!in_array($isActive, [0, 1], true)) {
    echo "Invalid status.";
    exit;
}

$titleEscaped = Database::escape($title);
$descriptionEscaped = Database::escape($description);
$imageUrlEscaped = Database::escape($imageUrl);
$linkUrlEscaped = Database::escape($linkUrl);
$startDateSql = $startDate !== "" ? "'" . Database::escape($startDate) . "'" : "NULL";
$endDateSql = $endDate !== "" ? "'" . Database::escape($endDate) . "'" : "NULL";

Database::iud("UPDATE advertisements SET
                title = '$titleEscaped',
                description = '$descriptionEscaped',
                image_url = '$imageUrlEscaped',
                link_url = '$linkUrlEscaped',
                start_date = $startDateSql,
                end_date = $endDateSql,
                is_active = $isActive
                WHERE advertisement_id = $adId");

echo "success";
