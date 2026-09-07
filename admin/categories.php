<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$categories = [];
$rs = Database::search("SELECT categories.*,
        (SELECT COUNT(*) FROM listings WHERE listings.category_id = categories.category_id) AS product_count
    FROM categories ORDER BY category_name ASC");
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $categories[] = $row;
    }
}

$pageTitle = "Categories";
$activeAdminPage = "categories";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Add a category</h2>
    <div id="addCategoryMessage" class="admin-message" style="display:none;"></div>
    <div class="admin-form-row">
        <div class="admin-field">
            <label for="newCategoryName">Category name</label>
            <input id="newCategoryName" type="text" placeholder="e.g. Sportswear">
        </div>
        <div class="admin-field">
            <label for="newCategoryDescription">Description</label>
            <input id="newCategoryDescription" type="text" placeholder="Short description">
        </div>
    </div>
    <div class="admin-field">
        <label for="newCategoryImage">Category image (optional)</label>
        <input id="newCategoryImage" type="file" accept="image/png,image/jpeg,image/gif,image/webp">
    </div>
    <button type="button" class="admin-btn" id="addCategoryBtn" onclick="addCategory()"><i class="fas fa-plus"></i> Add category</button>
</div>

<div class="admin-panel">
    <h2>All categories (<?php echo count($categories); ?>)</h2>
    <div class="admin-table-wrap">
        <?php if (empty($categories)): ?>
            <p class="admin-empty">No categories yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Products</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <?php $productCount = (int) $category["product_count"]; ?>
                        <tr id="category-row-<?php echo (int) $category["category_id"]; ?>">
                            <td>
                                <?php if (!empty($category["image_url"])): ?>
                                    <img src="../<?php echo htmlspecialchars($category["image_url"]); ?>" alt="<?php echo htmlspecialchars($category["category_name"]); ?>" class="admin-table-thumb">
                                <?php else: ?>
                                    <span class="admin-table-no-image">No image</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($category["category_name"]); ?></td>
                            <td><?php echo htmlspecialchars($category["description"] ?? ""); ?></td>
                            <td><?php echo $productCount; ?></td>
                            <td class="admin-table-actions">
                                <button type="button" class="admin-btn admin-btn-danger"
                                        id="delete-category-btn-<?php echo (int) $category["category_id"]; ?>"
                                        onclick="deleteCategory(<?php echo (int) $category["category_id"]; ?>)"
                                        <?php echo $productCount > 0 ? 'disabled title="Move or remove the products in this category first"' : ""; ?>>
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
