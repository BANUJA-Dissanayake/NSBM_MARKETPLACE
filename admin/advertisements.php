<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$ads = [];
$rs = Database::search("SELECT advertisements.*, `user`.name AS advertiser_name
    FROM advertisements
    INNER JOIN `user` ON advertisements.advertiser_id = `user`.user_id
    ORDER BY advertisements.advertisement_id DESC");
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $ads[] = $row;
    }
}

$pageTitle = "Advertisements";
$activeAdminPage = "advertisements";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Add an advertisement</h2>
    <div id="addAdMessage" class="admin-message" style="display:none;"></div>
    <div class="admin-form-row">
        <div class="admin-field">
            <label for="newAdTitle">Title</label>
            <input id="newAdTitle" type="text" placeholder="e.g. Big Sale Event">
        </div>
        <div class="admin-field">
            <label for="newAdImageUrl">Image URL</label>
            <input id="newAdImageUrl" type="text" placeholder="images/ads/banner.jpg">
        </div>
        <div class="admin-field">
            <label for="newAdLinkUrl">Link URL</label>
            <input id="newAdLinkUrl" type="text" placeholder="https://example.com/sale">
        </div>
        <div class="admin-field">
            <label for="newAdStartDate">Start date</label>
            <input id="newAdStartDate" type="date">
        </div>
        <div class="admin-field">
            <label for="newAdEndDate">End date</label>
            <input id="newAdEndDate" type="date">
        </div>
    </div>
    <div class="admin-field">
        <label for="newAdDescription">Description</label>
        <textarea id="newAdDescription" rows="3" placeholder="What's the ad about?"></textarea>
    </div>
    <button type="button" class="admin-btn" id="addAdBtn" onclick="addAdvertisement()"><i class="fas fa-plus"></i> Add advertisement</button>
</div>

<div class="admin-panel">
    <h2>All advertisements (<?php echo count($ads); ?>)</h2>
    <div class="admin-table-wrap">
        <?php if (empty($ads)): ?>
            <p class="admin-empty">No advertisements yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Added by</th>
                        <th>Runs</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ads as $ad): ?>
                        <?php $isActive = (int) $ad["is_active"] === 1; ?>
                        <tr id="ad-row-<?php echo (int) $ad["advertisement_id"]; ?>">
                            <td><?php echo htmlspecialchars($ad["title"]); ?></td>
                            <td><?php echo htmlspecialchars($ad["advertiser_name"]); ?></td>
                            <td><?php echo htmlspecialchars($ad["start_date"] ?? ""); ?> &ndash; <?php echo htmlspecialchars($ad["end_date"] ?? ""); ?></td>
                            <td id="ad-status-<?php echo (int) $ad["advertisement_id"]; ?>">
                                <span class="admin-badge <?php echo $isActive ? "active" : "deactive"; ?>"><?php echo $isActive ? "Active" : "Inactive"; ?></span>
                            </td>
                            <td class="admin-table-actions">
                                <a class="admin-btn admin-btn-outline" href="edit-advertisement.php?id=<?php echo (int) $ad["advertisement_id"]; ?>"><i class="fas fa-pen"></i> Edit</a>
                                <button type="button" class="admin-btn <?php echo $isActive ? "admin-btn-danger" : ""; ?>"
                                        id="toggle-ad-btn-<?php echo (int) $ad["advertisement_id"]; ?>"
                                        onclick="toggleAdvertisementStatus(<?php echo (int) $ad["advertisement_id"]; ?>, <?php echo $isActive ? "0" : "1"; ?>)">
                                    <?php echo $isActive ? "Deactivate" : "Activate"; ?>
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
