<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$news_items = [];
$rs = Database::search("SELECT news.*, `user`.name AS author_name
    FROM news
    INNER JOIN `user` ON news.author_id = `user`.user_id
    ORDER BY news.news_id DESC");
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $news_items[] = $row;
    }
}

$pageTitle = "News";
$activeAdminPage = "news";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Add a news article</h2>
    <div id="addNewsMessage" class="admin-message" style="display:none;"></div>
    <div class="admin-form-row">
        <div class="admin-field">
            <label for="newNewsTitle">Title</label>
            <input id="newNewsTitle" type="text" placeholder="e.g. 5 Tips for Selling Your Textbooks Fast">
        </div>
        <div class="admin-field">
            <label for="newNewsImageUrl">Image URL</label>
            <input id="newNewsImageUrl" type="text" placeholder="images/listings/photo-book.jpg">
        </div>
    </div>
    <div class="admin-field">
        <label for="newNewsContent">Content</label>
        <textarea id="newNewsContent" rows="5" placeholder="Full article text"></textarea>
    </div>
    <button type="button" class="admin-btn" id="addNewsBtn" onclick="addNews()"><i class="fas fa-plus"></i> Add article</button>
</div>

<div class="admin-panel">
    <h2>All articles (<?php echo count($news_items); ?>)</h2>
    <div class="admin-table-wrap">
        <?php if (empty($news_items)): ?>
            <p class="admin-empty">No articles yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Published</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($news_items as $item): ?>
                        <?php $isActive = (int) $item["is_active"] === 1; ?>
                        <tr id="news-row-<?php echo (int) $item["news_id"]; ?>">
                            <td><?php echo htmlspecialchars($item["title"]); ?></td>
                            <td><?php echo htmlspecialchars($item["author_name"]); ?></td>
                            <td><?php echo htmlspecialchars($item["created_at"]); ?></td>
                            <td id="news-status-<?php echo (int) $item["news_id"]; ?>">
                                <span class="admin-badge <?php echo $isActive ? "active" : "deactive"; ?>"><?php echo $isActive ? "Active" : "Inactive"; ?></span>
                            </td>
                            <td class="admin-table-actions">
                                <a class="admin-btn admin-btn-outline" href="edit-news.php?id=<?php echo (int) $item["news_id"]; ?>"><i class="fas fa-pen"></i> Edit</a>
                                <button type="button" class="admin-btn <?php echo $isActive ? "admin-btn-danger" : ""; ?>"
                                        id="toggle-news-btn-<?php echo (int) $item["news_id"]; ?>"
                                        onclick="toggleNewsStatus(<?php echo (int) $item["news_id"]; ?>, <?php echo $isActive ? "0" : "1"; ?>)">
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
