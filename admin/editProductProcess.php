<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

$listingId = (int) ($_POST["listing_id"] ?? 0);
$title = trim($_POST["title"] ?? "");
$categoryId = (int) ($_POST["category_id"] ?? 0);
$price = (float) ($_POST["price"] ?? 0);
$quantity = (int) ($_POST["quantity"] ?? 0);
$status = $_POST["status"] ?? "";
$description = trim($_POST["description"] ?? "");

if ($listingId <= 0) {
    echo "Invalid product.";
    exit;
}
if ($title === "") {
    echo "Please enter a product title.";
    exit;
}
if ($categoryId <= 0) {
    echo "Please choose a category.";
    exit;
}
if ($price <= 0) {
    echo "Please enter a price greater than 0.";
    exit;
}
if (!in_array($status, ["active", "inactive"], true)) {
    echo "Invalid status.";
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

$titleEscaped = Database::escape($title);
$descriptionEscaped = Database::escape($description);
$statusEscaped = Database::escape($status);

Database::iud("UPDATE listings SET
                title = '$titleEscaped',
                category_id = $categoryId,
                price = $price,
                quantity = $quantity,
                status = '$statusEscaped',
                description = '$descriptionEscaped'
                WHERE listing_id = $listingId");

if ($imageExt !== null) {
    $listingsDir = realpath(__DIR__ . "/../images/listings");

    $oldRs = Database::search("SELECT image_url FROM listing_images WHERE listing_id = $listingId");
    if ($oldRs) {
        while ($oldRow = $oldRs->fetch_assoc()) {
            $oldPath = realpath(__DIR__ . "/../" . $oldRow["image_url"]);
            // Only unlink files that actually resolve inside images/listings,
            // in case a stray row ever pointed somewhere unexpected.
            if ($oldPath && $listingsDir && strpos($oldPath, $listingsDir) === 0) {
                @unlink($oldPath);
            }
        }
    }
    Database::iud("DELETE FROM listing_images WHERE listing_id = $listingId");

    $slug = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
    if ($slug === "") {
        $slug = "product";
    }
    $slug = substr($slug, 0, 60);

    $fileName = $listingId . "-" . $slug . "." . $imageExt;
    $destPath = __DIR__ . "/../images/listings/" . $fileName;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $destPath)) {
        $imageUrlEscaped = Database::escape("images/listings/" . $fileName);
        Database::iud("INSERT INTO listing_images (listing_id, image_url) VALUES ($listingId, '$imageUrlEscaped')");
    }
}

echo "success";
