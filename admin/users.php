<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";
include "../pagination.php"; // reuses paginationPageList() for the ellipsis-compacted page list

requireAdminOrRedirect();

$currentUserId = (int) $_SESSION["u"]["user_id"];

const ADMIN_USERS_PER_PAGE = 20;

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";

$whereSql = "1";
if ($search !== "") {
    $searchEscaped = Database::escape($search);
    $whereSql = "(`user`.name LIKE '%$searchEscaped%' OR `user`.email LIKE '%$searchEscaped%')";
}

$totalUsers = 0;
$countRs = Database::search("SELECT COUNT(*) AS total FROM `user` WHERE $whereSql");
if ($countRs && $row = $countRs->fetch_assoc()) {
    $totalUsers = (int) $row["total"];
}
$totalPages = max(1, (int) ceil($totalUsers / ADMIN_USERS_PER_PAGE));

$page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * ADMIN_USERS_PER_PAGE;

$users = [];
$rs = Database::search("SELECT `user`.*, role.role_name
    FROM `user`
    LEFT JOIN role ON `user`.role = role.role_id
    WHERE $whereSql
    ORDER BY `user`.user_id DESC
    LIMIT " . ADMIN_USERS_PER_PAGE . " OFFSET $offset");
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $users[] = $row;
    }
}

// Carries the current search term into pagination links.
$pageQuery = $search !== "" ? "&search=" . urlencode($search) : "";

$pageTitle = "Users";
$activeAdminPage = "users";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Search users</h2>
    <form method="get" action="users.php" style="display:flex; align-items:flex-end; gap:12px; flex-wrap:wrap;">
        <div class="admin-field" style="margin-bottom:0; flex:1 1 260px; max-width: 360px;">
            <label for="userSearchInput">Name or email</label>
            <input id="userSearchInput" type="search" name="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($search); ?>">
        </div>
        <button type="submit" class="admin-btn"><i class="fas fa-magnifying-glass"></i> Search</button>
        <?php if ($search !== ""): ?>
            <a class="admin-btn admin-btn-outline" href="users.php">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="admin-panel">
    <h2>
        <?php if ($search !== ""): ?>
            Results for "<?php echo htmlspecialchars($search); ?>" (<?php echo $totalUsers; ?>)
        <?php else: ?>
            All users (<?php echo $totalUsers; ?>)
        <?php endif; ?>
    </h2>
    <div class="admin-table-wrap">
        <?php if (empty($users)): ?>
            <p class="admin-empty">No users found.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <?php $isBlocked = (int) ($user["status_id"] ?? 1) === 2; ?>
                        <tr id="user-row-<?php echo (int) $user["user_id"]; ?>">
                            <td><?php echo htmlspecialchars($user["name"]); ?></td>
                            <td><?php echo htmlspecialchars($user["email"]); ?></td>
                            <td><?php echo htmlspecialchars($user["phone"] ?? ""); ?></td>
                            <td><?php echo htmlspecialchars($user["role_name"] ?? "user"); ?></td>
                            <td id="user-status-<?php echo (int) $user["user_id"]; ?>">
                                <span class="admin-badge <?php echo $isBlocked ? "deactive" : "active"; ?>"><?php echo $isBlocked ? "Blocked" : "Active"; ?></span>
                            </td>
                            <td class="admin-table-actions">
                                <a class="admin-btn admin-btn-outline" href="edit-user.php?id=<?php echo (int) $user["user_id"]; ?>"><i class="fas fa-pen"></i> Edit</a>
                                <?php if ((int) $user["user_id"] !== $currentUserId): ?>
                                    <button type="button" class="admin-btn <?php echo $isBlocked ? "" : "admin-btn-danger"; ?>"
                                            id="toggle-btn-<?php echo (int) $user["user_id"]; ?>"
                                            onclick="toggleUserStatus(<?php echo (int) $user["user_id"]; ?>, <?php echo $isBlocked ? "1" : "2"; ?>)">
                                        <?php echo $isBlocked ? "Activate" : "Block"; ?>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php if ($totalPages > 1): ?>
                <nav class="admin-pagination" aria-label="User pages">
                    <a class="admin-page-btn <?php echo $page <= 1 ? "disabled" : ""; ?>"
                       href="?page=<?php echo max(1, $page - 1) . $pageQuery; ?>">&lt; Prev</a>

                    <?php foreach (paginationPageList($page, $totalPages) as $item): ?>
                        <?php if ($item === "..."): ?>
                            <span class="admin-page-ellipsis">&hellip;</span>
                        <?php else: ?>
                            <a class="admin-page-btn <?php echo $item === $page ? "active" : ""; ?>" href="?page=<?php echo (int) $item . $pageQuery; ?>">
                                <?php echo (int) $item; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <a class="admin-page-btn <?php echo $page >= $totalPages ? "disabled" : ""; ?>"
                       href="?page=<?php echo min($totalPages, $page + 1) . $pageQuery; ?>">Next &gt;</a>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
