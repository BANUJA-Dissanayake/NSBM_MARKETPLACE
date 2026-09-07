<?php
session_start();
include "connection.php";
include "includes/session-check.php";

requireLoginOrRedirect();

$user_id = currentUserId();
$user_rs = Database::search("SELECT * FROM `user` WHERE user_id = $user_id");
$user_details = $user_rs && $user_rs->num_rows > 0 ? $user_rs->fetch_assoc() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - NSBM</title>

    <link rel="stylesheet" href="css/fontawesome.min.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.min.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/editProfile.css">
</head>
<body>

    <div class="page-wrap edit-profile-wrap">
        <div class="profile-card animate__animated animate__fadeIn edit-profile-card">
            <div class="login-logo">
                <i class="fas fa-leaf"></i>
                NSBM
            </div>

            <a class="back-to-profile" href="profile.php"><i class="fas fa-arrow-left me-1"></i> Back to profile</a>

            <div class="avatar-circle"><i class="fas fa-user-pen"></i></div>
            <h1 class="edit-profile-title">Edit profile</h1>
            <p class="edit-profile-subtitle">Update your details below.</p>

            <div id="editProfileMessage" class="form-message" hidden></div>

            <div class="info-row edit-row">
                <i class="fas fa-user"></i>
                <div class="edit-field">
                    <label for="editName" class="info-label">Full name</label>
                    <input id="editName" class="form-control" type="text" value="<?php echo htmlspecialchars($user_details["name"] ?? ""); ?>" required>
                </div>
            </div>

            <div class="info-row edit-row">
                <i class="fas fa-envelope"></i>
                <div class="edit-field">
                    <span class="info-label">Email address</span>
                    <div class="info-value"><?php echo htmlspecialchars($user_details["email"] ?? ""); ?></div>
                </div>
            </div>

            <div class="info-row edit-row">
                <i class="fas fa-phone"></i>
                <div class="edit-field">
                    <label for="editPhone" class="info-label">Phone number</label>
                    <input id="editPhone" class="form-control" type="tel" value="<?php echo htmlspecialchars($user_details["phone"] ?? ""); ?>" placeholder="07XXXXXXXX">
                </div>
            </div>

            <div class="info-row edit-row">
                <i class="fas fa-lock"></i>
                <div class="edit-field">
                    <label for="editPassword" class="info-label">New password (optional)</label>
                    <input id="editPassword" class="form-control" type="password" placeholder="Leave blank to keep current password">
                </div>
            </div>

            <div class="info-row edit-row">
                <i class="fas fa-lock"></i>
                <div class="edit-field">
                    <label for="editCPassword" class="info-label">Confirm new password</label>
                    <input id="editCPassword" class="form-control" type="password" placeholder="Repeat new password">
                </div>
            </div>

            <button id="saveProfileBtn" type="button" class="cyber-btn btn btn-success rounded-pill w-100 fw-semibold mt-3" onclick="updateProfile()">
                <i class="fas fa-check me-2"></i>Save changes
            </button>
            <a href="profile.php" class="cyber-btn btn btn-outline-secondary rounded-pill w-100 fw-semibold mt-2">
                Cancel
            </a>
        </div>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>
