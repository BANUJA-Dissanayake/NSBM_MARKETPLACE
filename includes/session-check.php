<?php
// Include this file (after session_start()) on any page or AJAX endpoint
// that requires the visitor to be logged in.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION["u"]["user_id"]);
}

function currentUserId() {
    return isLoggedIn() ? (int) $_SESSION["u"]["user_id"] : null;
}

// For normal pages: redirects to the login/register page if not logged in.
function requireLoginOrRedirect() {
    if (!isLoggedIn()) {
        header("Location: register.php");
        exit;
    }
}

// For AJAX endpoints: prints a token the JS understands and stops execution.
function requireLoginOrRespond() {
    if (!isLoggedIn()) {
        echo "login_required";
        exit;
    }
}

// The `role` table has admin/buyer/seller rows (some duplicated across ids),
// so admin status is checked by name rather than a hardcoded role_id.
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    $roleId = (int) ($_SESSION["u"]["role"] ?? 0);
    if ($roleId <= 0) {
        return false;
    }
    $rs = Database::search("SELECT role_name FROM role WHERE role_id = $roleId");
    return $rs && $rs->num_rows > 0 && $rs->fetch_assoc()["role_name"] === "admin";
}

// For admin pages: redirects non-admins back to the storefront.
function requireAdminOrRedirect() {
    if (!isAdmin()) {
        header("Location: ../index.php");
        exit;
    }
}

// For admin AJAX endpoints.
function requireAdminOrRespond() {
    if (!isAdmin()) {
        echo "admin_required";
        exit;
    }
}
?>
