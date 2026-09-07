<?php
session_start();
include "../connection.php";
include "../includes/session-check.php";

requireAdminOrRedirect();

$messages = [];
$rs = Database::search("SELECT * FROM messages ORDER BY created_at DESC");
if ($rs) {
    while ($row = $rs->fetch_assoc()) {
        $messages[] = $row;
    }
}

$pageTitle = "Messages";
$activeAdminPage = "messages";
include "includes/admin-layout-top.php";
?>
<div class="admin-panel">
    <h2>Contact form messages (<?php echo count($messages); ?>)</h2>
    <div class="admin-table-wrap">
        <?php if (empty($messages)): ?>
            <p class="admin-empty">No messages yet.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>From</th>
                        <th>Topic</th>
                        <th>Message</th>
                        <th>Received</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr id="message-row-<?php echo (int) $message["message_id"]; ?>">
                            <td>
                                <strong><?php echo htmlspecialchars($message["name"]); ?></strong><br>
                                <span style="color:#55705d;font-size:0.82rem;"><?php echo htmlspecialchars($message["email"]); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($message["topic"]); ?></td>
                            <td style="max-width: 360px; white-space: normal;"><?php echo nl2br(htmlspecialchars($message["message"])); ?></td>
                            <td><?php echo htmlspecialchars($message["created_at"]); ?></td>
                            <td id="message-status-<?php echo (int) $message["message_id"]; ?>">
                                <span class="admin-badge <?php echo $message["is_read"] ? "read" : "unread"; ?>"><?php echo $message["is_read"] ? "Read" : "Unread"; ?></span>
                            </td>
                            <td class="admin-table-actions">
                                <?php if (!$message["is_read"]): ?>
                                    <button type="button" class="admin-btn admin-btn-outline" onclick="markMessageRead(<?php echo (int) $message["message_id"]; ?>)">
                                        <i class="fas fa-check"></i> Mark read
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<?php include "includes/admin-layout-bottom.php"; ?>
