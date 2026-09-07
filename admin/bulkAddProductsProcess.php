<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

header("Content-Type: text/plain");

requireAdminOrRespond();

// Each line: Title | Category | Price | Quantity | Description | Image filename (optional)
// Lets an admin paste many products at once instead of filling the single-add
// form one at a time - same underlying inserts as addProductProcess.php.

$sellerId = (int) ($_POST["seller_id"] ?? 0);
$rawLines = (string) ($_POST["products"] ?? "");

if ($sellerId <= 0) {
    echo "Please choose a seller.";
    exit;
}

$sellerCheck = Database::search("SELECT user_id FROM `user` WHERE user_id = $sellerId");
if (!$sellerCheck || $sellerCheck->num_rows === 0) {
    echo "Seller not found.";
    exit;
}

$categoryIdByName = [];
$categoryRs = Database::search("SELECT category_id, category_name FROM categories");
if ($categoryRs) {
    while ($row = $categoryRs->fetch_assoc()) {
        $categoryIdByName[strtolower(trim($row["category_name"]))] = (int) $row["category_id"];
    }
}

$lines = preg_split('/\r\n|\r|\n/', trim($rawLines));
$added = 0;
$skipped = [];

foreach ($lines as $lineNumber => $line) {
    $line = trim($line);
    if ($line === "") {
        continue;
    }

    $parts = array_map("trim", explode("|", $line));
    if (count($parts) < 5) {
        $skipped[] = "Line " . ($lineNumber + 1) . ": needs at least Title | Category | Price | Quantity | Description";
        continue;
    }

    $title = $parts[0];
    $categoryName = $parts[1];
    $price = (float) $parts[2];
    $quantity = (int) $parts[3];
    $description = $parts[4];
    $imageName = isset($parts[5]) ? trim($parts[5]) : "";

    $categoryId = $categoryIdByName[strtolower($categoryName)] ?? null;

    if ($title === "" || $categoryId === null || $price <= 0 || $quantity <= 0) {
        $skipped[] = "Line " . ($lineNumber + 1) . ": invalid title/category/price/quantity ('" . $categoryName . "')";
        continue;
    }

    $titleEscaped = Database::escape($title);
    $descriptionEscaped = Database::escape($description);

    Database::iud("INSERT INTO listings (seller_id, category_id, title, description, listing_type, price, quantity, status, created_at)
                    VALUES ($sellerId, $categoryId, '$titleEscaped', '$descriptionEscaped', 'sale', $price, $quantity, 'active', NOW())");

    $listingId = Database::insertId();

    if ($listingId > 0 && $imageName !== "") {
        // basename() so a line can only ever reference an existing file
        // already inside images/listings, never an arbitrary path.
        $safeName = basename($imageName);
        if (is_file(__DIR__ . "/../images/listings/" . $safeName)) {
            $imageUrlEscaped = Database::escape("images/listings/" . $safeName);
            Database::iud("INSERT INTO listing_images (listing_id, image_url) VALUES ($listingId, '$imageUrlEscaped')");
        }
    }

    $added++;
}

if ($added === 0) {
    echo "No products added.\n" . implode("\n", $skipped);
    exit;
}

echo "success: added $added product" . ($added === 1 ? "" : "s")
    . (!empty($skipped) ? "; skipped " . count($skipped) . " line(s):\n" . implode("\n", $skipped) : "");
