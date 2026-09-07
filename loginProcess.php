<?php
session_start();
include "connection.php";

$email = $_POST["email"] ?? "";
$password = $_POST["password"] ?? "";
$rememberme = $_POST["rememberme"] ?? "false";

if (empty($email)) {
    echo "Please Enter Your Email Address.";
} else if (empty($password)) {
    echo "Please Enter Your Password.";
} else {

    $rs = Database::search("SELECT * FROM `user` WHERE `email`='" . $email . "'");

    if ($rs && $rs->num_rows === 1) {
        $userRow = $rs->fetch_assoc();

        if (password_verify($password, $userRow["password"])) {
            if ((int) ($userRow["status_id"] ?? 1) === 2) {
                echo "Your account has been blocked. Contact support for help.";
                exit;
            }

            echo "success";

            $_SESSION["u"] = $userRow;

            if ($rememberme === "true") {
                setcookie("email", $email, time() + (60 * 60 * 24 * 365));
            } else {
                setcookie("email", "", -1);
            }
        } else {
            echo "Invalid Username or Password.";
        }
    } else {
        echo "Invalid Username or Password.";
    }
}
