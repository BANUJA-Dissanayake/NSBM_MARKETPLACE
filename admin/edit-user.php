<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$userId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
$editUser = null;

if ($userId > 0) {
    $rs = Database::search("SELECT * FROM `user` WHERE user_id = $userId");
    if ($rs && $rs->num_rows > 0) {
        $editUser = $rs->fetch_assoc();
    }
}

if (!$editUser) {
    header("Location: users.php");
    exit;
}

$roles = [];
$roleRs = Database::search("SELECT DISTINCT role_id, role_name FROM role ORDER BY role_name ASC, role_id ASC");
if ($roleRs) {
    while ($row = $roleRs->fetch_assoc()) {
        $roles[] = $row;
    }
}

$pageTitle = "Edit User";
$activeAdminPage = "users";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Edit user</h2>
    <div id="editUserMessage" class="admin-message" style="display:none;"></div>
    <input type="hidden" id="editUserId" value="<?php echo (int) $editUser["user_id"]; ?>">

    <div class="admin-form-row">
        <div class="admin-field">
            <label for="editUserName">Full name</label>
            <input id="editUserName" type="text" value="<?php echo htmlspecialchars($editUser["name"]); ?>">
        </div>
        <div class="admin-field">
            <label>Email address</label>
            <input type="text" value="<?php echo htmlspecialchars($editUser["email"]); ?>" disabled>
        </div>
        <div class="admin-field">
            <label for="editUserPhone">Phone</label>
            <input id="editUserPhone" type="tel" value="<?php echo htmlspecialchars($editUser["phone"] ?? ""); ?>" placeholder="07XXXXXXXX">
        </div>
        <div class="admin-field">
            <label for="editUserRole">Role</label>
            <select id="editUserRole">
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo (int) $role["role_id"]; ?>" <?php echo (int) $role["role_id"] === (int) $editUser["role"] ? "selected" : ""; ?>>
                        <?php echo htmlspecialchars($role["role_name"]); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="admin-field">
            <label for="editUserStatus">Account status</label>
            <select id="editUserStatus">
                <option value="1" <?php echo (int) ($editUser["status_id"] ?? 1) === 1 ? "selected" : ""; ?>>Active</option>
                <option value="2" <?php echo (int) ($editUser["status_id"] ?? 1) === 2 ? "selected" : ""; ?>>Blocked</option>
            </select>
        </div>
    </div>

    <div class="admin-table-actions">
        <button type="button" class="admin-btn" id="saveUserBtn" onclick="updateUser()"><i class="fas fa-check"></i> Save changes</button>
        <a class="admin-btn admin-btn-outline" href="users.php">Cancel</a>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
