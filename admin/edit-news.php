<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$news_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$article = null;

if ($news_id > 0) {
    $rs = Database::search("SELECT * FROM news WHERE news_id = $news_id");
    if ($rs && $rs->num_rows > 0) {
        $article = $rs->fetch_assoc();
    }
}

if (!$article) {
    header("Location: news.php");
    exit;
}

$pageTitle = "Edit Article";
$activeAdminPage = "news";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Edit article</h2>
    <div id="editNewsMessage" class="admin-message" style="display:none;"></div>
    <input type="hidden" id="editNewsId" value="<?php echo (int) $article["news_id"]; ?>">

    <div class="admin-form-row">
        <div class="admin-field">
            <label for="editNewsTitle">Title</label>
            <input id="editNewsTitle" type="text" value="<?php echo htmlspecialchars($article["title"]); ?>">
        </div>
        <div class="admin-field">
            <label for="editNewsImageUrl">Image URL</label>
            <input id="editNewsImageUrl" type="text" value="<?php echo htmlspecialchars($article["image_url"] ?? ""); ?>">
        </div>
        <div class="admin-field">
            <label for="editNewsStatus">Status</label>
            <select id="editNewsStatus">
                <option value="1" <?php echo (int) $article["is_active"] === 1 ? "selected" : ""; ?>>Active</option>
                <option value="0" <?php echo (int) $article["is_active"] === 0 ? "selected" : ""; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="admin-field">
        <label for="editNewsContent">Content</label>
        <textarea id="editNewsContent" rows="6"><?php echo htmlspecialchars($article["content"]); ?></textarea>
    </div>

    <div class="admin-table-actions">
        <button type="button" class="admin-btn" id="saveNewsBtn" onclick="editNews()"><i class="fas fa-check"></i> Save changes</button>
        <a class="admin-btn admin-btn-outline" href="news.php">Cancel</a>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
