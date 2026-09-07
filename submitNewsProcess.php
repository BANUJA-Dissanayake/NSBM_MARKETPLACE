<?php
session_start();
include "connection.php";
include "includes/session-check.php";

header("Content-Type: text/plain");

requireLoginOrRespond();

$title = trim($_POST["title"] ?? "");
$content = trim($_POST["content"] ?? "");
$authorId = (int) $_SESSION["u"]["user_id"];

if ($title === "") {
    echo "Please enter a title.";
    exit;
}
if ($content === "") {
    echo "Please write the article content.";
    exit;
}

// Same image-upload validation as admin/addProductProcess.php - extension
// is derived from the file's real type (via getimagesize), never the
// client-supplied name, so a disguised upload can't land with an
// executable extension.
$imageExtensionsByType = [
    IMAGETYPE_JPEG => "jpg",
    IMAGETYPE_PNG => "png",
    IMAGETYPE_GIF => "gif",
    IMAGETYPE_WEBP => "webp",
];

$imageExt = null;
if (!empty($_FILES["image"]["name"])) {
    if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) {
        echo "The image failed to upload. Please try again.";
        exit;
    }
    if ($_FILES["image"]["size"] > 5 * 1024 * 1024) {
        echo "Image must be 5MB or smaller.";
        exit;
    }
    $imageInfo = @getimagesize($_FILES["image"]["tmp_name"]);
    if ($imageInfo === false || !isset($imageExtensionsByType[$imageInfo[2]])) {
        echo "Please upload a valid JPG, PNG, GIF, or WEBP image.";
        exit;
    }
    $imageExt = $imageExtensionsByType[$imageInfo[2]];
}

$titleEscaped = Database::escape($title);
$contentEscaped = Database::escape($content);

// Submitted inactive (is_active = 0) - an admin reviews and activates it
// from admin/news.php (the same toggle already used for admin-authored
// articles) before it appears on newspage.php.
Database::iud("INSERT INTO news (author_id, title, content, image_url, is_active, created_at)
                VALUES ($authorId, '$titleEscaped', '$contentEscaped', '', 0, NOW())");

$newsId = Database::insertId();

if ($imageExt !== null && $newsId > 0) {
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    if ($slug === "") {
        $slug = "news";
    }
    $slug = substr($slug, 0, 60);

    $fileName = $newsId . "-" . $slug . "." . $imageExt;
    $destDir = __DIR__ . "/images/news";
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $destPath = $destDir . "/" . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $destPath)) {
        $imageUrlEscaped = Database::escape("images/news/" . $fileName);
        Database::iud("UPDATE news SET image_url = '$imageUrlEscaped' WHERE news_id = $newsId");
    }
}

echo "success";
