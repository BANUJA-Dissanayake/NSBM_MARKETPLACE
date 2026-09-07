<?php
session_start();
include "connection.php";
include "includes/session-check.php";

header("Content-Type: text/plain");

requireLoginOrRespond();

$user_id = currentUserId();

$name = trim($_POST["name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$password = $_POST["password"] ?? "";
$cpassword = $_POST["cpassword"] ?? "";

if (empty($name)) {
    echo "Please enter your full name.";
    exit;
}
if (!empty($phone) && strlen($phone) != 10) {
    echo "Mobile number must contain 10 digits.";
    exit;
}
if (!empty($phone) && !preg_match('/^07[0124567][0-9]{7}$/', $phone)) {
    echo "Invalid mobile number.";
    exit;
}
if (!empty($password) && strlen($password) < 8) {
    echo "Password must be at least 8 characters.";
    exit;
}
if (!empty($password) && $password !== $cpassword) {
    echo "Password and confirm password do not match.";
    exit;
}

$nameEscaped = Database::escape($name);
$phoneEscaped = Database::escape($phone);

if (!empty($password)) {
    $hashedPassword = Database::escape(password_hash($password, PASSWORD_DEFAULT));
    Database::iud("UPDATE `user` SET `name` = '$nameEscaped', `phone` = '$phoneEscaped', `password` = '$hashedPassword' WHERE user_id = $user_id");
} else {
    Database::iud("UPDATE `user` SET `name` = '$nameEscaped', `phone` = '$phoneEscaped' WHERE user_id = $user_id");
}

$user_rs = Database::search("SELECT * FROM `user` WHERE user_id = $user_id");
if ($user_rs && $user_rs->num_rows > 0) {
    $_SESSION["u"] = $user_rs->fetch_assoc();
}

echo "success";
