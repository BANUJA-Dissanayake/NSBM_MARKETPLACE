<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$name = trim($_POST["name"] ?? "");
$description = trim($_POST["description"] ?? "");

if ($name === "") {
    echo "Please enter a category name.";
    exit;
}

$nameEscaped = Database::escape($name);
$rs = Database::search("SELECT category_id FROM categories WHERE category_name = '$nameEscaped'");
if ($rs && $rs->num_rows > 0) {
    echo "That category already exists.";
    exit;
}

// Extension is derived from the file's real image type (via getimagesize),
// never from the client-supplied name, so a disguised upload can't land
// with an executable extension.
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

$descriptionEscaped = Database::escape($description);
Database::iud("INSERT INTO categories (category_name, description) VALUES ('$nameEscaped', '$descriptionEscaped')");

$categoryId = Database::insertId();

if ($imageExt !== null && $categoryId > 0) {
    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
    if ($slug === "") {
        $slug = "category";
    }
    $slug = substr($slug, 0, 60);

    $fileName = $categoryId . "-" . $slug . "." . $imageExt;
    $destDir = __DIR__ . "/../images/categories";
    if (!is_dir($destDir)) {
        mkdir($destDir, 0755, true);
    }
    $destPath = $destDir . "/" . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $destPath)) {
        $imageUrlEscaped = Database::escape("images/categories/" . $fileName);
        Database::iud("UPDATE categories SET image_url = '$imageUrlEscaped' WHERE category_id = $categoryId");
    }
}

echo "success";
