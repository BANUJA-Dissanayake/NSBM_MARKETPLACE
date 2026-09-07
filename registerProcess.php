<?php
session_start();
include "connection.php";

$name = $_POST["name"] ?? "";
$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";
$phonenumber = $_POST["phone"] ?? "";
$conferm_pass = $_POST["cpassword"] ?? "";

if (empty($name)) {
    echo ("Please Enter Your Full Name.");
} else if (empty($email)) {
    echo ("Please Enter Your Email Address.");
} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo ("Please Enter a Valid Email Address.");
} else if (!empty($phonenumber) && strlen($phonenumber) != 10) {
    echo ("Mobile Number Must Contain 10 Digits.");
} else if (!empty($phonenumber) && !preg_match('/^07[0124567][0-9]{7}$/', $phonenumber)) {
    echo ("Invalid Mobile Number.");
} else if (empty($password)) {
    echo ("Please Enter Your Password.");
} else if (strlen($password) < 8) {
    echo ("Password Must Be At Least 8 Characters.");
} else if (empty($conferm_pass)) {
    echo ("Please Enter Your Confirm Password.");
} else if ($password != $conferm_pass) {
    echo ("Password and Confirm Password Do Not Match.");
} else {
    // proceed with registration

    $emailEscaped = Database::escape($email);
    $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $emailEscaped . "'");
    $n = $rs->num_rows;

    if ($n == 0) {
        $d = new DateTime();
        $tz = new DateTimeZone("Asia/Colombo");
        $d->setTimezone($tz);
        $date = $d->format("Y-m-d H:i:s");

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $nameEscaped = Database::escape($name);
        $phoneEscaped = Database::escape($phonenumber);
        $passwordEscaped = Database::escape($hashedPassword);

        Database::iud("INSERT INTO `user`
                (`name`,`email`,`password`,`phone`,`role`,`created_at`) VALUES
                ('" . $nameEscaped . "','" . $emailEscaped . "','" . $passwordEscaped . "','" . $phoneEscaped . "','1','" . $date . "')");

        // Log the new account in immediately instead of making them sign in
        // again right after registering.
        $newUserRs = Database::search("SELECT * FROM `user` WHERE user_id = " . Database::insertId());
        if ($newUserRs && $newUserRs->num_rows === 1) {
            $_SESSION["u"] = $newUserRs->fetch_assoc();
        }

        echo ("success");
    } else {
        echo ("Email Address already exists.");
    }
}