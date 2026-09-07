<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$listingId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$product = null;

if ($listingId > 0) {
    $rs = Database::search("SELECT * FROM listings WHERE listing_id = $listingId");
    if ($rs && $rs->num_rows > 0) {
        $product = $rs->fetch_assoc();
    }
}

if (!$product) {
    header("Location: products.php");
    exit;
}

$categories = [];
$catRs = Database::search("SELECT * FROM categories ORDER BY category_name ASC");
if ($catRs) {
    while ($row = $catRs->fetch_assoc()) {
        $categories[] = $row;
    }
}

$currentImageUrl = null;
$imgRs = Database::search("SELECT image_url FROM listing_images WHERE listing_id = $listingId LIMIT 1");
if ($imgRs && $imgRow = $imgRs->fetch_assoc()) {
    $currentImageUrl = $imgRow["image_url"];
}

$pageTitle = "Edit Product";
$activeAdminPage = "products";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Edit product</h2>
    <div id="editProductMessage" class="admin-message" style="display:none;"></div>
    <input type="hidden" id="editProductId" value="<?php echo (int) $product["listing_id"]; ?>">

    <div class="admin-form-row">
        <div class="admin-field">
            <label for="editProductTitle">Title</label>
            <input id="editProductTitle" type="text" value="<?php echo htmlspecialchars($product["title"]); ?>">
        </div>
        <div class="admin-field">
            <label for="editProductCategory">Category</label>
            <select id="editProductCategory">
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo (int) $category["category_id"]; ?>" <?php echo (int) $category["category_id"] === (int) $product["category_id"] ? "selected" : ""; ?>>
                        <?php echo htmlspecialchars($category["category_name"]); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="admin-field">
            <label for="editProductPrice">Price (Rs.)</label>
            <input id="editProductPrice" type="number" min="0" step="0.01" value="<?php echo (float) $product["price"]; ?>">
        </div>
        <div class="admin-field">
            <label for="editProductQuantity">Quantity</label>
            <input id="editProductQuantity" type="number" min="0" value="<?php echo (int) $product["quantity"]; ?>">
        </div>
        <div class="admin-field">
            <label for="editProductStatus">Status</label>
            <select id="editProductStatus">
                <option value="active" <?php echo $product["status"] === "active" ? "selected" : ""; ?>>Active</option>
                <option value="inactive" <?php echo $product["status"] === "inactive" ? "selected" : ""; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="admin-field">
        <label for="editProductDescription">Description</label>
        <textarea id="editProductDescription" rows="4"><?php echo htmlspecialchars($product["description"] ?? ""); ?></textarea>
    </div>

    <div class="admin-field">
        <label for="editProductImage">Product image</label>
        <?php if ($currentImageUrl): ?>
            <img class="admin-current-image" src="../<?php echo htmlspecialchars($currentImageUrl); ?>" alt="Current product image">
        <?php endif; ?>
        <input id="editProductImage" type="file" accept="image/png,image/jpeg,image/gif,image/webp">
        <p class="admin-field-hint">Upload a new image only if you want to replace the current one.</p>
    </div>

    <div class="admin-table-actions">
        <button type="button" class="admin-btn" id="saveProductBtn" onclick="editProduct()"><i class="fas fa-check"></i> Save changes</button>
        <a class="admin-btn admin-btn-outline" href="products.php">Cancel</a>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
