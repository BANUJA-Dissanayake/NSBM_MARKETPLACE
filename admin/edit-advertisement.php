<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$ad_id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$ad = null;

if ($ad_id > 0) {
    $rs = Database::search("SELECT * FROM advertisements WHERE advertisement_id = $ad_id");
    if ($rs && $rs->num_rows > 0) {
        $ad = $rs->fetch_assoc();
    }
}

if (!$ad) {
    header("Location: advertisements.php");
    exit;
}

$pageTitle = "Edit Advertisement";
$activeAdminPage = "advertisements";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Edit advertisement</h2>
    <div id="editAdMessage" class="admin-message" style="display:none;"></div>
    <input type="hidden" id="editAdId" value="<?php echo (int) $ad["advertisement_id"]; ?>">

    <div class="admin-form-row">
        <div class="admin-field">
            <label for="editAdTitle">Title</label>
            <input id="editAdTitle" type="text" value="<?php echo htmlspecialchars($ad["title"]); ?>">
        </div>
        <div class="admin-field">
            <label for="editAdImageUrl">Image URL</label>
            <input id="editAdImageUrl" type="text" value="<?php echo htmlspecialchars($ad["image_url"] ?? ""); ?>">
        </div>
        <div class="admin-field">
            <label for="editAdLinkUrl">Link URL</label>
            <input id="editAdLinkUrl" type="text" value="<?php echo htmlspecialchars($ad["link_url"] ?? ""); ?>">
        </div>
        <div class="admin-field">
            <label for="editAdStartDate">Start date</label>
            <input id="editAdStartDate" type="date" value="<?php echo htmlspecialchars($ad["start_date"] ?? ""); ?>">
        </div>
        <div class="admin-field">
            <label for="editAdEndDate">End date</label>
            <input id="editAdEndDate" type="date" value="<?php echo htmlspecialchars($ad["end_date"] ?? ""); ?>">
        </div>
        <div class="admin-field">
            <label for="editAdStatus">Status</label>
            <select id="editAdStatus">
                <option value="1" <?php echo (int) $ad["is_active"] === 1 ? "selected" : ""; ?>>Active</option>
                <option value="0" <?php echo (int) $ad["is_active"] === 0 ? "selected" : ""; ?>>Inactive</option>
            </select>
        </div>
    </div>
    <div class="admin-field">
        <label for="editAdDescription">Description</label>
        <textarea id="editAdDescription" rows="3"><?php echo htmlspecialchars($ad["description"] ?? ""); ?></textarea>
    </div>

    <div class="admin-table-actions">
        <button type="button" class="admin-btn" id="saveAdBtn" onclick="editAdvertisement()"><i class="fas fa-check"></i> Save changes</button>
        <a class="admin-btn admin-btn-outline" href="advertisements.php">Cancel</a>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
